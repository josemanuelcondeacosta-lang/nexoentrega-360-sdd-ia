<?php

namespace App\Modules\AI;

use App\Modules\Audit\Audit;
use App\Modules\Requests\RequestService as Requests;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AiController
{
    public function create(Request $r)
    {
        $u = $r->user();
        Requests::role($u, ['Coordinador']);
        $data = $r->validate(['requestIds' => 'required|array|min:1|max:20', 'requestIds.*' => 'required|string|distinct']);
        abort_unless(count($r->all()) === 1, 422, 'Campos adicionales no permitidos.');
        $rows = [];
        foreach ($data['requestIds'] as $id) {
            $rows[] = Requests::find($u, $id);
        }
        foreach ($rows as $row) {
            abort_unless($row->estado === 'ENVIADA', 409, 'Solo se resumen solicitudes ENVIADA.');
        }
        // Provisional: proveedor no configurado. Nunca presentar este resumen como LLM real.
        $high = count(array_filter($rows, fn ($row) => $row->prioridad === 'ALTA'));
        $payload = ['origin' => ['type' => 'fallback'], 'aiAssisted' => true, 'message' => 'Proveedor no configurado; resumen determinista.',
            'summary' => count($rows).' solicitudes enviadas; '.$high.' de prioridad ALTA. Revisa fecha y zona antes de reservar.',
            'recommendations' => [], 'requestIds' => $data['requestIds']];
        $id = (string) Str::uuid();
        DB::transaction(function () use ($u, $id, $payload) {
            DB::table('ai_summaries')->insert(['id' => $id, 'organization_id' => $u->organization_id, 'coordinador_id' => $u->id, 'payload' => json_encode($payload), 'created_at' => now()]);
            DB::table('ai_attempts')->insert(['id' => (string) Str::uuid(), 'summary_id' => $id, 'result' => 'provider_unavailable', 'created_at' => now()]);
        });

        return response()->json($payload + ['id' => $id, 'status' => 'BORRADOR', 'createdAt' => now()->toIso8601String(), 'approvedBy' => null, 'approvedAt' => null]);
    }

    public function approve(Request $r, string $summaryId)
    {
        $u = $r->user();
        $summary = DB::table('ai_summaries')->where('organization_id', $u->organization_id)->where('id', $summaryId)->first();
        abort_unless($summary, 404, 'Resumen no encontrado.');
        Requests::role($u, ['Coordinador']);
        abort_unless(count($r->all()) === 0, 422, 'Cuerpo adicional no permitido.');

        return DB::transaction(function () use ($u, $summaryId) {
            $row = DB::table('ai_summaries')->where('id', $summaryId)->lockForUpdate()->first();
            abort_unless($row->status === 'BORRADOR', 409, 'El resumen ya está aprobado.');
            DB::table('ai_summaries')->where('id', $summaryId)->update(['status' => 'APROBADO', 'approved_by' => $u->id, 'approved_at' => now()]);
            Audit::record($u, $summaryId, 'APPROVE_AI', 'BORRADOR', 'APROBADO');

            return response()->json(json_decode($row->payload, true) + ['id' => $summaryId, 'status' => 'APROBADO', 'createdAt' => Carbon::parse($row->created_at)->toIso8601String(), 'approvedBy' => (string) $u->id, 'approvedAt' => now()->toIso8601String()]);
        });
    }
}
