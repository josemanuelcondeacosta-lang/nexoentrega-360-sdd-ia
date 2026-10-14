<?php

namespace App\Modules\Queries;

use App\Modules\Requests\RequestService as Requests;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QueryController
{
    private function scope(Request $r)
    {
        $q = DB::table('requests')->where('organization_id', $r->user()->organization_id);
        if ($r->user()->role === 'Solicitante') {
            $q->where('solicitante_id', $r->user()->id);
        }

        return $q;
    }

    private function filters(Request $r, $q)
    {
        $data = $r->validate(['page' => 'sometimes|integer|min:1', 'pageSize' => 'sometimes|integer|min:1|max:100',
            'estado' => 'sometimes|in:BORRADOR,ENVIADA,RESERVADA,DESPACHADA,ENTREGADA,CANCELADA', 'prioridad' => 'sometimes|in:NORMAL,ALTA', 'zona' => 'sometimes|in:NORTE,SUR,CENTRO']);
        foreach (['estado', 'prioridad', 'zona'] as $field) {
            if (isset($data[$field])) {
                $q->where($field, $data[$field]);
            }
        }

        return $q;
    }

    private function page(Request $r, $q, $map = null)
    {
        $r->validate(['page' => 'sometimes|integer|min:1', 'pageSize' => 'sometimes|integer|min:1|max:100']);
        $page = (int) $r->input('page', 1);
        $size = (int) $r->input('pageSize', 20);
        $total = (clone $q)->count();
        $items = $q->offset(($page - 1) * $size)->limit($size)->get();

        return response()->json(['items' => $map ? $items->map($map)->all() : $items, 'page' => $page, 'pageSize' => $size, 'total' => $total]);
    }

    public function index(Request $r)
    {
        return $this->page($r, $this->filters($r, $this->scope($r))->orderByDesc('created_at')->orderBy('id'), fn ($row) => Requests::serialize($row));
    }

    public function show(Request $r, string $requestId)
    {
        $row = Requests::find($r->user(), $requestId);
        if ($r->user()->role === 'Solicitante') {
            Requests::owner($r->user(), $row);
        }

        return response()->json(Requests::serialize($row));
    }

    public function inventory(Request $r)
    {
        Requests::role($r->user(), ['Coordinador', 'Bodega', 'Auditor']);

        return $this->page($r, DB::table('inventory_items')->where('organization_id', $r->user()->organization_id)->orderBy('sku'),
            fn ($s) => ['sku' => $s->sku, 'activo' => (bool) $s->active, 'fisico' => (int) $s->fisico, 'reservado' => (int) $s->reservado, 'disponible' => (int) $s->fisico - (int) $s->reservado]);
    }

    public function catalog(Request $r)
    {
        return response()->json(DB::table('inventory_items')->where('organization_id', $r->user()->organization_id)->where('active', true)->orderBy('sku')->pluck('sku'));
    }

    public function dashboard(Request $r)
    {
        $counts = array_fill_keys(['BORRADOR', 'ENVIADA', 'RESERVADA', 'DESPACHADA', 'ENTREGADA', 'CANCELADA'], 0);
        foreach ($this->filters($r, $this->scope($r))->selectRaw('estado, count(*) AS amount')->groupBy('estado')->get() as $row) {
            $counts[$row->estado] = (int) $row->amount;
        }

        return response()->json(['cantidadesPorEstado' => $counts]);
    }

    public function audit(Request $r, ?string $requestId = null)
    {
        $u = $r->user();
        if ($requestId) {
            $row = Requests::find($u, $requestId);
            if ($u->role === 'Solicitante') {
                Requests::owner($u, $row);
            }
        }
        Requests::role($u, ['Solicitante', 'Coordinador', 'Auditor']);
        $q = DB::table('audit_events')->where('organization_id', $u->organization_id);
        if ($requestId) {
            $q->where('resource_id', $requestId);
        }
        if ($u->role === 'Solicitante') {
            $q->whereIn('resource_id', DB::table('requests')->where('solicitante_id', $u->id)->where('organization_id', $u->organization_id)->select('id'));
        }

        return $this->page($r, $q->orderByDesc('occurred_at')->orderBy('id'), fn ($e) => ['id' => $e->id, 'actorId' => (string) $e->actor_id, 'organizationId' => $e->organization_id,
            'resourceId' => $e->resource_id, 'action' => $e->action, 'occurredAt' => Carbon::parse($e->occurred_at)->utc()->toIso8601String(), 'correlationId' => $e->correlation_id]);
    }
}
