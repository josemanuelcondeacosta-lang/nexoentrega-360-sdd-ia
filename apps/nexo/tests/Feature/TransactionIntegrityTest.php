<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Pruebas de integridad transaccional e idempotencia (SPEC-03 0.4, ADR-002).
 * Hallazgo R-E04 de la revisión de Edier al PR #12.
 * Oráculos tomados de la SPEC y del anexo acceptance-oracles.md antes de ejecutar;
 * no reutilizan el algoritmo de producción.
 */
class TransactionIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function user(string $role, string $org = 'A'): User
    {
        return User::where('email', 'org-'.strtolower($org).'-'.strtolower($role).'@example.test')->firstOrFail();
    }

    /** A-001 del oráculo: solicitud ENVIADA de ORG-A con 7 KIT-ALIMENTO (físico 10, reservado 0). */
    private function sentRequest(int $cantidad = 7): string
    {
        $id = $this->actingAs($this->user('Solicitante'))->postJson('/api/requests', [
            'zona' => 'NORTE', 'prioridad' => 'ALTA', 'fechaRequerida' => now()->addDays(2)->toIso8601String(),
            'observaciones' => 'Datos ficticios', 'lineas' => [['sku' => 'KIT-ALIMENTO', 'cantidad' => $cantidad]],
        ])->assertCreated()->json('id');
        $this->postJson("/api/requests/$id/send", [])->assertOk();

        return $id;
    }

    private function stock(): array
    {
        $row = DB::table('inventory_items')->where('organization_id', 'ORG-A')->where('sku', 'KIT-ALIMENTO')->first();

        return [(int) $row->fisico, (int) $row->reservado, (int) $row->fisico - (int) $row->reservado];
    }

    private function events(string $id, string $action): int
    {
        return DB::table('audit_events')->where('resource_id', $id)->where('action', $action)->count();
    }

    /**
     * AC-03-09 (H-E01): un fallo inducido al escribir el evento de auditoría antes del commit
     * responde 500 con correlationId, revierte todo y no guarda resultado idempotente;
     * reintentar con la misma clave después del fallo sí reserva.
     */
    public function test_ac_03_09_fallo_de_auditoria_antes_del_commit_revierte_todo(): void
    {
        $id = $this->sentRequest();
        $this->assertSame([10, 0, 10], $this->stock());

        // Fallo inducido solo en la base de pruebas: el INSERT en audit_events lanza una excepción.
        DB::unprepared("CREATE FUNCTION test_fail_audit() RETURNS trigger AS $$ BEGIN RAISE EXCEPTION 'fallo inducido de auditoria'; END; $$ LANGUAGE plpgsql");
        DB::unprepared('CREATE TRIGGER test_fail_audit BEFORE INSERT ON audit_events FOR EACH ROW EXECUTE FUNCTION test_fail_audit()');

        $response = $this->actingAs($this->user('Coordinador'))
            ->postJson("/api/requests/$id/reserve", [], ['Idempotency-Key' => 'ac-03-09']);

        $response->assertStatus(500);
        $correlationId = $response->json('correlationId');
        $this->assertNotEmpty($correlationId);
        $this->assertSame($correlationId, $response->headers->get('X-Correlation-Id'));
        $this->assertStringNotContainsString('fallo inducido', $response->getContent(), 'El 500 no debe revelar detalles internos');

        $this->assertDatabaseHas('requests', ['id' => $id, 'estado' => 'ENVIADA']);
        $this->assertSame([10, 0, 10], $this->stock());
        $this->assertSame(0, $this->events($id, 'RESERVE'));
        $this->assertSame(0, DB::table('idempotency_records')->count());

        DB::unprepared('DROP TRIGGER test_fail_audit ON audit_events');
        DB::unprepared('DROP FUNCTION test_fail_audit()');

        $this->actingAs($this->user('Coordinador'))
            ->postJson("/api/requests/$id/reserve", [], ['Idempotency-Key' => 'ac-03-09'])
            ->assertOk()->assertJsonPath('estado', 'RESERVADA');
        $this->assertSame([10, 7, 3], $this->stock());
        $this->assertSame(1, $this->events($id, 'RESERVE'));
        $this->assertSame(1, DB::table('idempotency_records')->count());
    }

    /**
     * AC-03-10 y AC-03-11, retención de la clave (RF07, CL-11): a las 23 h 59 min repite el mismo código y cuerpo sin nuevos efectos;
     * a las 24 h exactas deja de repetir y la petición se procesa como nueva (409 porque ya está RESERVADA),
     * sin una segunda reserva.
     */
    public function test_retencion_de_clave_repite_hasta_23h59_y_deja_de_repetir_a_las_24h(): void
    {
        $t0 = Carbon::parse('2030-01-10 12:00:00');
        $this->travelTo($t0);
        $id = $this->sentRequest();
        $coordinador = $this->user('Coordinador');

        $original = $this->actingAs($coordinador)
            ->postJson("/api/requests/$id/reserve", [], ['Idempotency-Key' => 'ttl-24h'])->assertOk()->json();
        $this->assertSame([10, 7, 3], $this->stock());

        $this->travelTo($t0->copy()->addHours(23)->addMinutes(59));
        $replay = $this->actingAs($coordinador)
            ->postJson("/api/requests/$id/reserve", [], ['Idempotency-Key' => 'ttl-24h'])->assertOk()->json();
        $this->assertSame($original, $replay);
        $this->assertSame(1, $this->events($id, 'RESERVE'));
        $this->assertSame([10, 7, 3], $this->stock());

        $this->travelTo($t0->copy()->addHours(24));
        $this->actingAs($coordinador)
            ->postJson("/api/requests/$id/reserve", [], ['Idempotency-Key' => 'ttl-24h'])
            ->assertConflict()->assertJsonPath('code', 'INVALID_STATE');
        $this->assertSame(1, $this->events($id, 'RESERVE'));
        $this->assertSame([10, 7, 3], $this->stock());
        $this->assertSame(1, DB::table('idempotency_records')->where('idempotency_key', 'ttl-24h')->count());
    }

    /**
     * Complemento de AC-03-12: la misma clave textual usada por otro usuario no recupera la respuesta ajena (RF07: ámbito clave, usuario,
     * organización y operación). El coordinador de ORG-B con la misma clave sobre una solicitud de ORG-A recibe 404
     * y no se guarda nada para él.
     */
    public function test_misma_clave_de_otro_usuario_no_recupera_la_respuesta_ajena(): void
    {
        $id = $this->sentRequest();
        $this->actingAs($this->user('Coordinador'))
            ->postJson("/api/requests/$id/reserve", [], ['Idempotency-Key' => 'compartida'])->assertOk();

        $this->actingAs($this->user('Coordinador', 'B'))
            ->postJson("/api/requests/$id/reserve", [], ['Idempotency-Key' => 'compartida'])
            ->assertNotFound()->assertJsonMissingPath('estado');
        $this->assertSame(1, DB::table('idempotency_records')->count());
        $this->assertSame(1, $this->events($id, 'RESERVE'));
    }
}
