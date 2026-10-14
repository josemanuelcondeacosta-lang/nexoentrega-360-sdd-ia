<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class NexoFlowTest extends TestCase
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

    private function draft(array $lines = [['sku' => 'KIT-ALIMENTO', 'cantidad' => 7]]): string
    {
        return $this->actingAs($this->user('Solicitante'))->postJson('/api/requests', ['zona' => 'NORTE', 'prioridad' => 'ALTA', 'fechaRequerida' => now()->addDays(2)->toIso8601String(), 'observaciones' => 'Datos ficticios', 'lineas' => $lines])->assertCreated()->json('id');
    }

    private function send(string $id): void
    {
        $this->actingAs($this->user('Solicitante'))->postJson('/api/requests/'.$id.'/send', [])->assertOk();
    }

    public function test_complete_flow_persists_stock_state_and_audit(): void
    {
        $id = $this->draft();
        $this->send($id);
        $this->actingAs($this->user('Coordinador'))->postJson("/api/requests/$id/reserve", [], ['Idempotency-Key' => 'flow-1'])->assertOk();
        $this->assertDatabaseHas('inventory_items', ['organization_id' => 'ORG-A', 'sku' => 'KIT-ALIMENTO', 'fisico' => 10, 'reservado' => 7]);
        $this->actingAs($this->user('Bodega'))->postJson("/api/requests/$id/dispatch", [])->assertOk();
        $this->actingAs($this->user('Coordinador'))->postJson("/api/requests/$id/deliver", [])->assertOk();
        $this->assertDatabaseHas('requests', ['id' => $id, 'estado' => 'ENTREGADA']);
        $this->assertDatabaseHas('inventory_items', ['organization_id' => 'ORG-A', 'sku' => 'KIT-ALIMENTO', 'fisico' => 3, 'reservado' => 0]);
        $this->assertSame(4, DB::table('audit_events')->where('resource_id', $id)->count());
        $this->assertSame(1, DB::table('idempotency_records')->count());
        $this->actingAs($this->user('Auditor'))->getJson('/api/audit')->assertOk()->assertJsonPath('total', 4);
        $this->getJson("/api/requests/$id/audit")->assertOk()->assertJsonPath('total', 4)
            ->assertJsonPath('items.0.action', 'SEND')->assertJsonPath('items.1.action', 'RESERVE')
            ->assertJsonPath('items.2.action', 'DISPATCH')->assertJsonPath('items.3.action', 'DELIVER');
        $this->actingAs($this->user('Bodega'))->getJson("/api/requests/$id/audit")->assertForbidden();
    }

    public function test_partial_reservation_rejected_without_side_effects(): void
    {
        $id = $this->draft([['sku' => 'KIT-AGUA', 'cantidad' => 5], ['sku' => 'KIT-HIGIENE', 'cantidad' => 9]]);
        $this->send($id);
        $before = DB::table('inventory_items')->orderBy('organization_id')->orderBy('sku')->get()->toJson();
        $this->actingAs($this->user('Coordinador'))->postJson("/api/requests/$id/reserve", [], ['Idempotency-Key' => 'partial'])->assertConflict();
        $this->assertSame($before, DB::table('inventory_items')->orderBy('organization_id')->orderBy('sku')->get()->toJson());
        $this->assertDatabaseHas('requests', ['id' => $id, 'estado' => 'ENVIADA']);
        $this->assertSame(1, DB::table('audit_events')->count());
    }

    public function test_idempotent_replay_and_conflicting_key(): void
    {
        $first = $this->draft();
        $this->send($first);
        $second = $this->draft();
        $this->send($second);
        $this->actingAs($this->user('Coordinador'));
        $a = $this->postJson("/api/requests/$first/reserve", [], ['Idempotency-Key' => 'repeat'])->assertOk()->json();
        $b = $this->postJson("/api/requests/$first/reserve", [], ['Idempotency-Key' => 'repeat'])->assertOk()->json();
        $this->assertSame($a, $b);
        $this->postJson("/api/requests/$second/reserve", [], ['Idempotency-Key' => 'repeat'])->assertConflict();
        $this->assertSame(1, DB::table('audit_events')->where('action', 'RESERVE')->count());
        $this->assertDatabaseHas('requests', ['id' => $second, 'estado' => 'ENVIADA']);
    }

    public function test_cancel_releases_exact_reservation_and_invalid_dispatch_is_atomic(): void
    {
        $id = $this->draft();
        $this->send($id);
        $this->actingAs($this->user('Coordinador'))->postJson("/api/requests/$id/reserve", [], ['Idempotency-Key' => 'cancel'])->assertOk();
        $this->actingAs($this->user('Solicitante'))->postJson("/api/requests/$id/cancel", [])->assertOk();
        $this->actingAs($this->user('Bodega'))->postJson("/api/requests/$id/dispatch", [])->assertConflict();
        $this->assertDatabaseHas('inventory_items', ['organization_id' => 'ORG-A', 'sku' => 'KIT-ALIMENTO', 'fisico' => 10, 'reservado' => 0]);
        $this->assertSame(3, DB::table('audit_events')->count());
    }

    public function test_roles_organization_and_historial_are_enforced_on_server(): void
    {
        $id = $this->draft();
        $this->send($id);
        $this->actingAs($this->user('Auditor'))->postJson("/api/requests/$id/reserve", [], ['Idempotency-Key' => 'deny'])->assertForbidden();
        foreach (['GET', 'POST'] as $method) {
            $this->actingAs($this->user('Coordinador', 'B'));
            $this->json($method, "/api/requests/$id".($method === 'POST' ? '/reserve' : ''), [], ['Idempotency-Key' => 'cross'])->assertNotFound();
        }
        $this->actingAs($this->user('Auditor', 'B'))->getJson("/api/requests/$id/audit")->assertNotFound();
        $this->actingAs($this->user('Bodega', 'B'))->postJson("/api/requests/$id/dispatch", [])->assertNotFound();
        $this->assertDatabaseHas('requests', ['id' => $id, 'estado' => 'ENVIADA']);
        $this->assertSame(0, DB::table('idempotency_records')->count());
    }

    public function test_fallback_approval_has_no_privileged_effects(): void
    {
        $id = $this->draft();
        $this->send($id);
        $before = DB::table('inventory_items')->get()->toJson();
        $this->actingAs($this->user('Coordinador'));
        $summary = $this->postJson('/api/ai/summaries', ['requestIds' => [$id]])->assertOk()->assertJsonPath('origin.type', 'fallback')->json('id');
        $this->postJson("/api/ai/summaries/$summary/approve", [])->assertOk();
        $this->postJson("/api/ai/summaries/$summary/approve", [])->assertConflict();
        $this->assertSame($before, DB::table('inventory_items')->get()->toJson());
        $this->assertDatabaseHas('requests', ['id' => $id, 'estado' => 'ENVIADA']);
        $this->assertSame(1, DB::table('audit_events')->where('action', 'APPROVE_AI')->count());
    }

    public function test_requester_audit_lists_only_owned_requests_with_uuid_ids(): void
    {
        $owned = $this->draft();
        $this->send($owned);
        $other = User::factory()->create(['organization_id' => 'ORG-A', 'role' => 'Solicitante']);
        $foreign = $this->actingAs($other)->postJson('/api/requests', ['zona' => 'NORTE', 'prioridad' => 'NORMAL', 'fechaRequerida' => now()->addDay()->toIso8601String(), 'lineas' => [['sku' => 'KIT-AGUA', 'cantidad' => 1]]])->assertCreated()->json('id');
        $this->postJson("/api/requests/$foreign/send", [])->assertOk();
        $this->actingAs($this->user('Solicitante'))->getJson('/api/audit')->assertOk()->assertJsonPath('total', 1)->assertJsonPath('items.0.resourceId', $owned);
        $this->getJson("/api/requests/$foreign/audit")->assertForbidden();
        $this->actingAs($this->user('Solicitante', 'B'))->getJson('/api/audit')->assertOk()->assertJsonPath('total', 0);
    }

    public function test_invalid_quantities_rejected_before_writes(): void
    {
        $this->actingAs($this->user('Solicitante'))->postJson('/api/requests', ['zona' => 'NORTE', 'prioridad' => 'NORMAL', 'fechaRequerida' => now()->addDay()->toIso8601String(), 'lineas' => [['sku' => 'KIT-ALIMENTO', 'cantidad' => 0]]])->assertUnprocessable();
        $this->assertDatabaseCount('requests', 0);
        $this->assertDatabaseCount('request_lines', 0);
        $this->assertDatabaseCount('audit_events', 0);
    }
}
