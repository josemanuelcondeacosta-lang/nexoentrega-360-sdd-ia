<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\AI\AiInvalidOutputException;
use App\Modules\AI\AiOutputValidator;
use App\Modules\AI\AiProvider;
use App\Modules\AI\ReplayProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Asistente de IA (SPEC-05 0.4, ADR-004). Los proveedores son deterministas: fixtures oficiales, un espía
 * o una reproducción declarada; ninguna prueba usa red externa ni clave (AC-05-14).
 */
class AiAssistantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        config(['ai.provider' => 'none', 'ai.key' => null]);
    }

    private function user(string $role, string $org = 'A'): User
    {
        return User::where('email', 'org-'.strtolower($org).'-'.strtolower($role).'@example.test')->firstOrFail();
    }

    /** Solicitud ENVIADA con código fijo (A-001, A-002…) para usar los fixtures oficiales tal como están. */
    private function sent(string $code, string $prioridad = 'ALTA', string $zona = 'NORTE', string $org = 'A', ?string $fecha = null, string $obs = 'Datos ficticios'): string
    {
        $id = $this->actingAs($this->user('Solicitante', $org))->postJson('/api/requests', [
            'zona' => $zona, 'prioridad' => $prioridad, 'fechaRequerida' => $fecha ?? now()->addDays(2)->toIso8601String(),
            'observaciones' => $obs, 'lineas' => [['sku' => 'KIT-AGUA', 'cantidad' => 1]],
        ])->assertCreated()->json('id');
        DB::table('requests')->where('id', $id)->update(['codigo' => $code]);
        $this->postJson("/api/requests/$id/send", [])->assertOk();

        return $id;
    }

    private function snapshot(): string
    {
        return json_encode([
            DB::table('inventory_items')->orderBy('organization_id')->orderBy('sku')->get(),
            DB::table('requests')->orderBy('id')->get(['id', 'estado', 'prioridad', 'zona']),
            DB::table('audit_events')->count(),
        ]);
    }

    private function fakeProvider(string|\Throwable $output): object
    {
        $spy = new class($output) implements AiProvider
        {
            public int $calls = 0;

            public array $received = [];

            public function __construct(private string|\Throwable $output) {}

            public function complete(string $instructions, string $data, int $timeoutMs): string
            {
                $this->calls++;
                $this->received = ['instructions' => $instructions, 'data' => $data, 'timeoutMs' => $timeoutMs];
                if ($this->output instanceof \Throwable) {
                    throw $this->output;
                }

                return $this->output;
            }

            public function origin(): array
            {
                return ['type' => 'provider', 'provider' => 'espia', 'model' => 'espia-1', 'fixtureId' => null];
            }
        };
        $this->app->instance(AiProvider::class, $spy);

        return $spy;
    }

    private function summarize(array $ids, array $headers = []): TestResponse
    {
        return $this->actingAs($this->user('Coordinador'))->postJson('/api/ai/summaries', ['requestIds' => $ids], $headers);
    }

    /** AC-05-01: el proveedor recibe solo los seis campos autorizados y solo la selección. */
    public function test_ac_05_01_payload_solo_seis_campos_y_solo_la_seleccion(): void
    {
        $a1 = $this->sent('A-001');
        $a2 = $this->sent('A-002', 'NORMAL', 'SUR');
        $this->sent('A-003');
        $spy = $this->fakeProvider('{"summary":"Revisar A-001.","recommendations":[]}');

        $this->summarize([$a1, $a2])->assertOk()->assertJsonPath('origin.type', 'provider');

        $this->assertSame(1, $spy->calls);
        $this->assertSame(5000, $spy->received['timeoutMs']);
        $json = substr($spy->received['data'], strpos($spy->received['data'], '{'));
        $rows = json_decode($json, true)['solicitudes'];
        $this->assertSame(['A-001', 'A-002'], array_column($rows, 'requestId'));
        foreach ($rows as $row) {
            $this->assertSame(['requestId', 'zona', 'prioridad', 'fechaRequerida', 'estado', 'observaciones'], array_keys($row));
        }
        $everything = $spy->received['instructions'].$spy->received['data'];
        foreach (['A-003', '@example.test', 'password', 'nexo_session', 'organization', $a1] as $forbidden) {
            $this->assertStringNotContainsString($forbidden, $everything);
        }
    }

    public static function officialFixtures(): array
    {
        return [
            'AI-VALID' => ['AI-VALID', 'valid', null],
            'AI-JSON-BAD' => ['AI-JSON-BAD', 'invalid', 'json_invalido'],
            'AI-FOREIGN-ID' => ['AI-FOREIGN-ID', 'invalid', 'id_no_seleccionado'],
            'AI-EXTRA' => ['AI-EXTRA', 'invalid', 'raiz_propiedad_extra'],
            'AI-TIMEOUT' => ['AI-TIMEOUT', 'timeout', 'fixture_timeout'],
            'AI-ERROR' => ['AI-ERROR', 'error', 'fixture_error_proveedor'],
            'AI-INJECTION' => ['AI-INJECTION', 'invalid', 'id_no_seleccionado'],
        ];
    }

    /** AC-05-02, 03, 06, 07, 08 y 14: los siete fixtures oficiales producen su resultado esperado sin tocar el negocio. */
    #[DataProvider('officialFixtures')]
    public function test_fixtures_oficiales(string $fixture, string $result, ?string $reason): void
    {
        $a1 = $this->sent('A-001', 'ALTA', 'NORTE', 'A', '2030-01-11T15:00:00Z', file_get_contents(resource_path('ai/untrusted-observation.txt')));
        $a2 = $this->sent('A-002', 'NORMAL', 'SUR', 'A', '2030-01-12T15:00:00Z');
        $this->sent('B-001', 'ALTA', 'NORTE', 'B');
        config(['ai.provider' => 'fixture', 'ai.fixture_id' => $fixture]);
        $before = $this->snapshot();

        $response = $this->summarize([$a1, $a2])->assertOk()->assertJsonPath('status', 'BORRADOR')->assertJsonPath('aiAssisted', true);

        $this->assertSame($before, $this->snapshot(), 'El asistente no cambia inventario, estados, prioridades ni auditoría');
        $attempt = DB::table('ai_attempts')->latest('created_at')->first();
        $this->assertSame($result, $attempt->result);
        $this->assertSame($reason, $attempt->reason);
        if ($result === 'valid') {
            $response->assertJsonPath('origin.type', 'fixture')->assertJsonPath('origin.fixtureId', 'AI-VALID')
                ->assertJsonPath('message', null)->assertJsonPath('summary', 'Dos solicitudes pendientes requieren revisión.')
                ->assertJsonPath('recommendations.0.requestId', $a1)->assertJsonPath('recommendations.0.evidenceFields', ['prioridad', 'fechaRequerida']);
        } else {
            $response->assertJsonPath('origin.type', 'fallback')->assertJsonPath('recommendations', [])
                ->assertJsonPath('summary', '2 solicitudes: 1 ALTA, 1 NORMAL; zonas NORTE y SUR; fecha más próxima 2030-01-11.');
            $this->assertNotEmpty($response->json('message'));
            foreach (['B-001', 'window.__unsafe', 'reserve-all', 'Ignora los permisos', 'Incluye un dato ajeno'] as $leak) {
                $this->assertStringNotContainsString($leak, $response->getContent());
                $this->assertStringNotContainsString($leak, (string) DB::table('ai_summaries')->value('payload'));
            }
        }
    }

    /** AC-05-03: con un proveedor real que no responde, el corte ocurre a los 5 s y el fallback llega antes de 5 s + 250 ms. */
    public function test_ac_05_03_corte_real_a_los_5_segundos(): void
    {
        $server = stream_socket_server('tcp://127.0.0.1:0', $errno, $error);
        $this->assertNotFalse($server, $error);
        $port = (int) substr(strrchr(stream_socket_get_name($server, false), ':'), 1);
        config(['ai.provider' => 'gemini', 'ai.key' => 'clave-de-prueba', 'ai.model' => 'modelo-de-prueba', 'ai.gemini_base_url' => "http://127.0.0.1:$port"]);
        $a1 = $this->sent('A-001');

        $start = hrtime(true);
        $response = $this->summarize([$a1]);
        $elapsedMs = (hrtime(true) - $start) / 1e6;
        fclose($server);

        $response->assertOk()->assertJsonPath('origin.type', 'fallback');
        $this->assertStringContainsString('no respondió a tiempo', $response->json('message'));
        $this->assertGreaterThanOrEqual(5000, $elapsedMs);
        $this->assertLessThanOrEqual(5250, $elapsedMs, 'Tolerancia instrumental de AC-05-03');
        $this->assertSame('timeout', DB::table('ai_attempts')->value('result'));
    }

    /** AC-05-05 y H-V02: 422 → 404 → 409 sin invocar al proveedor; con 20 válidas se invoca una sola vez. */
    public function test_ac_05_05_seleccion_invalida_no_invoca_al_proveedor(): void
    {
        $a1 = $this->sent('A-001');
        $b1 = $this->sent('B-001', 'ALTA', 'NORTE', 'B');
        $reserved = $this->sent('A-900');
        $this->actingAs($this->user('Coordinador'))->postJson("/api/requests/$reserved/reserve", [], ['Idempotency-Key' => 'ia-reservada'])->assertOk();
        $spy = $this->fakeProvider('{"summary":"ok","recommendations":[]}');
        $uuids = fn (int $n) => array_map(fn () => (string) Str::uuid(), range(1, $n));

        $this->summarize($uuids(21))->assertStatus(422);
        $this->summarize([])->assertStatus(422);
        $this->summarize([$a1, $a1])->assertStatus(422);
        $this->summarize(['B-001'])->assertStatus(422);
        $this->actingAs($this->user('Coordinador'))->postJson('/api/ai/summaries', ['requestIds' => [$a1], 'execute' => 'reserve-all'])->assertStatus(422);
        $this->summarize([$b1])->assertNotFound();
        $this->summarize([$reserved])->assertConflict();
        $this->summarize([$reserved, $b1])->assertNotFound();
        $this->summarize(array_merge([$b1], $uuids(20)))->assertStatus(422);
        $this->assertSame(0, $spy->calls);
        $this->assertSame(0, DB::table('ai_summaries')->count());

        $twenty = [$a1];
        for ($i = 2; $i <= 20; $i++) {
            $twenty[] = $this->sent(sprintf('A-%03d', $i));
        }
        $this->summarize($twenty)->assertOk();
        $this->assertSame(1, $spy->calls);
    }

    /** AC-05-09: sin proveedor, dos peticiones iguales devuelven el mismo resumen determinista con mensaje controlado. */
    public function test_ac_05_09_sin_proveedor_resumen_determinista(): void
    {
        $ids = [$this->sent('A-001', 'ALTA', 'NORTE', 'A', '2030-01-11T15:00:00Z'), $this->sent('A-002', 'NORMAL', 'SUR', 'A', '2030-01-12T15:00:00Z')];
        $first = $this->summarize($ids)->assertOk()->assertJsonPath('origin.type', 'fallback')->json();
        $second = $this->summarize($ids)->assertOk()->json();

        $this->assertSame($first['summary'], $second['summary']);
        $this->assertSame('Proveedor de IA no configurado; se muestra un resumen automático.', $first['message']);
        $this->assertSame('unavailable', DB::table('ai_attempts')->value('result'));
    }

    /** AC-05-04, AC-05-12 y H-V03: aprobar un borrador del proveedor conserva su origen y audita con el correlationId, sin efectos. */
    public function test_aprobacion_conserva_origen_y_audita_sin_efectos(): void
    {
        $a1 = $this->sent('A-001');
        $a2 = $this->sent('A-002', 'NORMAL', 'SUR');
        config(['ai.provider' => 'fixture', 'ai.fixture_id' => 'AI-VALID']);
        $summary = $this->summarize([$a1, $a2])->assertOk()->json('id');
        $before = DB::table('inventory_items')->get()->toJson().DB::table('requests')->get(['id', 'estado', 'prioridad'])->toJson();
        $correlation = '0b3f7c2e-1d4a-4c8e-9f00-1a2b3c4d5e6f';

        $this->actingAs($this->user('Coordinador'))->postJson("/api/ai/summaries/$summary/approve", [], ['X-Correlation-Id' => $correlation])
            ->assertOk()->assertJsonPath('status', 'APROBADO')->assertJsonPath('origin.fixtureId', 'AI-VALID')
            ->assertJsonPath('approvedBy', (string) $this->user('Coordinador')->id);

        $this->assertSame($before, DB::table('inventory_items')->get()->toJson().DB::table('requests')->get(['id', 'estado', 'prioridad'])->toJson());
        $this->assertDatabaseHas('audit_events', ['resource_id' => $summary, 'action' => 'APPROVE_AI', 'correlation_id' => $correlation]);
    }

    /** AC-05-17: una salida válida con marcado se acepta como texto; el servidor no lo interpreta y la pantalla lo escapa. */
    public function test_ac_05_17_salida_valida_con_marcado_se_entrega_como_texto(): void
    {
        $a1 = $this->sent('A-001');
        $this->fakeProvider(json_encode(['summary' => '<script>window.__unsafe=true</script> Revisar A-001', 'recommendations' => [
            ['requestId' => 'A-001', 'reason' => '<b>Prioridad ALTA</b>', 'evidenceFields' => ['prioridad']],
        ]]));

        $this->summarize([$a1])->assertOk()->assertJsonPath('origin.type', 'provider')
            ->assertJsonPath('summary', '<script>window.__unsafe=true</script> Revisar A-001')
            ->assertJsonPath('recommendations.0.reason', '<b>Prioridad ALTA</b>');

        $js = file_get_contents(public_path('app.js'));
        $this->assertStringContainsString('escapeHtml(data.summary)', $js);
        $this->assertStringContainsString('escapeHtml(rec.reason)', $js);
    }

    /** AC-05-13: cada captura real válida se reproduce por el mismo adaptador y validador y se declara como reproducción. */
    public function test_ac_05_13_reproduccion_de_captura_real(): void
    {
        $files = glob(config('ai.replay_dir').'/*.json') ?: [];
        $valid = array_filter($files, fn ($f) => (json_decode(file_get_contents($f), true)['validation'] ?? null) === 'valid');
        if (! $valid) {
            $this->markTestSkipped('Aún no hay una captura real válida en resources/ai/replay (php artisan ai:capture).');
        }
        foreach ($valid as $file) {
            $capture = (new ReplayProvider(basename($file)))->capture();
            $this->assertArrayNotHasKey('key', $capture);
            $ids = [];
            foreach ($capture['input']['data'] as $row) {
                $ids[$row['requestId']] = $this->sent($row['requestId'], $row['prioridad'], $row['zona'], 'A', $row['fechaRequerida'], $row['observaciones']);
            }
            config(['ai.provider' => 'replay', 'ai.replay_file' => basename($file)]);
            $output = json_decode($capture['rawOutput'], true);

            $response = $this->summarize(array_values($ids))->assertOk()
                ->assertJsonPath('origin.type', 'replay')->assertJsonPath('origin.provider', 'gemini')
                ->assertJsonPath('origin.model', $capture['model'])->assertJsonPath('origin.fixtureId', pathinfo($file, PATHINFO_FILENAME))
                ->assertJsonPath('summary', $output['summary']);
            foreach ($output['recommendations'] as $i => $rec) {
                $response->assertJsonPath("recommendations.$i.requestId", $ids[$rec['requestId']]);
            }
            // Libera los códigos para la siguiente captura sin borrar historial.
            foreach ($ids as $code => $id) {
                DB::table('requests')->where('id', $id)->update(['codigo' => $code.'-'.substr($id, 0, 8)]);
            }
        }
    }

    public static function rejectedOutputs(): array
    {
        $rec = fn (array $over = []) => array_merge(['requestId' => 'A-001', 'reason' => 'Motivo.', 'evidenceFields' => ['prioridad']], $over);

        return [
            'summary vacío' => [['summary' => '', 'recommendations' => []], 'summary_longitud'],
            'summary de 601' => [['summary' => str_repeat('ñ', 601), 'recommendations' => []], 'summary_longitud'],
            'seis recomendaciones' => [['summary' => 'x', 'recommendations' => array_map(fn ($i) => $rec(['requestId' => "A-00$i"]), range(1, 6))], 'recommendations_mas_de_5'],
            'reason de 241' => [['summary' => 'x', 'recommendations' => [$rec(['reason' => str_repeat('a', 241)])]], 'reason_longitud'],
            'evidenceFields vacío' => [['summary' => 'x', 'recommendations' => [$rec(['evidenceFields' => []])]], 'evidenceFields_cantidad'],
            'evidenceFields con 4' => [['summary' => 'x', 'recommendations' => [$rec(['evidenceFields' => ['zona', 'prioridad', 'fechaRequerida', 'observaciones']])]], 'evidenceFields_cantidad'],
            'evidenceFields repetido' => [['summary' => 'x', 'recommendations' => [$rec(['evidenceFields' => ['zona', 'zona']])]], 'evidenceFields_repetido'],
            'evidenceFields no permitido' => [['summary' => 'x', 'recommendations' => [$rec(['evidenceFields' => ['nombre']])]], 'evidenceFields_no_permitido'],
            'requestId repetido' => [['summary' => 'x', 'recommendations' => [$rec(), $rec()]], 'id_repetido'],
            'requestId no seleccionado' => [['summary' => 'x', 'recommendations' => [$rec(['requestId' => 'B-001'])]], 'id_no_seleccionado'],
            'propiedad extra en recomendación' => [['summary' => 'x', 'recommendations' => [$rec(['action' => 'reserve'])]], 'recomendacion_propiedad_extra'],
            'falta recommendations' => [['summary' => 'x'], 'raiz_falta_propiedad'],
            'raíz lista' => [[['summary' => 'x']], 'raiz_no_objeto'],
            'summary no texto' => [['summary' => 7, 'recommendations' => []], 'summary_no_texto'],
        ];
    }

    /** AC-05-06: cada salida fuera del esquema o de la selección se rechaza con un motivo sanitizado. */
    #[DataProvider('rejectedOutputs')]
    public function test_ac_05_06_validador_rechaza(array $output, string $reason): void
    {
        $this->expectException(AiInvalidOutputException::class);
        $this->expectExceptionMessage($reason);
        (new AiOutputValidator)->validate(json_encode($output), ['A-001', 'A-002', 'A-003', 'A-004', 'A-005', 'A-006']);
    }

    /** AC-05-07: los límites exactos se aceptan (600 caracteres Unicode, 5 recomendaciones, reason de 240, 1 y 3 evidencias). */
    public function test_ac_05_07_validador_acepta_limites(): void
    {
        $codes = ['A-001', 'A-002', 'A-003', 'A-004', 'A-005'];
        $recs = array_map(fn ($c) => ['requestId' => $c, 'reason' => str_repeat('é', 240), 'evidenceFields' => ['zona']], $codes);
        $recs[4]['evidenceFields'] = ['zona', 'prioridad', 'observaciones'];
        $valid = (new AiOutputValidator)->validate(json_encode(['summary' => str_repeat('ñ', 600), 'recommendations' => $recs]), $codes);

        $this->assertCount(5, $valid['recommendations']);
        $this->assertSame(600, mb_strlen($valid['summary']));
    }

    /** Las copias en resources/ai son idénticas a los anexos oficiales (cuando el repositorio completo está disponible). */
    public function test_copias_de_fixtures_y_esquema_identicas_a_las_oficiales(): void
    {
        $official = [
            base_path('../../docs/examen-oficial/anexos/ai-provider-fixtures.json') => resource_path('ai/ai-provider-fixtures.json'),
            base_path('../../contracts/ai-output.schema.json') => resource_path('ai/ai-output.schema.json'),
            base_path('../../docs/examen-oficial/anexos/untrusted-observation.txt') => resource_path('ai/untrusted-observation.txt'),
        ];
        if (! is_file(array_key_first($official))) {
            $this->markTestSkipped('Repositorio completo no disponible en este entorno (por ejemplo, dentro de Docker).');
        }
        foreach ($official as $source => $copy) {
            $this->assertSame(hash_file('sha256', $source), hash_file('sha256', $copy), basename($copy));
        }
    }
}
