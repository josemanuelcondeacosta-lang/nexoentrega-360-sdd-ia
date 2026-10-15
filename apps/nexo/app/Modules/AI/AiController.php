<?php

namespace App\Modules\AI;

use App\Modules\Audit\Audit;
use App\Modules\Requests\RequestService as Requests;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AiController
{
    public function create(Request $r)
    {
        $u = $r->user();
        Requests::role($u, ['Coordinador']);
        $data = $r->validate(['requestIds' => 'required|array|min:1|max:20', 'requestIds.*' => 'required|string|uuid|distinct']);
        abort_unless(count($r->all()) === 1, 422, 'Campos adicionales no permitidos.');
        $rows = [];
        foreach ($data['requestIds'] as $id) {
            $rows[] = Requests::find($u, $id);
        }
        foreach ($rows as $row) {
            abort_unless($row->estado === 'ENVIADA', 409, 'Solo se resumen solicitudes ENVIADA.');
        }

        // Selección validada y autorizada: el proveedor nunca se invoca con 403, 422, 404 ni 409 (H-V02).
        return response()->json((new AiSummaryService)->summarize($u, $rows, $data['requestIds']));
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
