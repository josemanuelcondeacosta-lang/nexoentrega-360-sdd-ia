<?php

namespace App\Modules\Inventory;

use App\Modules\Audit\Audit;
use App\Modules\Requests\RequestService as Requests;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransitionController
{
    public function run(Request $r, string $requestId, string $action)
    {
        $u = $r->user();
        $found = Requests::find($u, $requestId);
        $roles = ['send' => 'Solicitante', 'reserve' => 'Coordinador', 'dispatch' => 'Bodega', 'deliver' => 'Coordinador', 'cancel' => 'Solicitante'];
        abort_unless(isset($roles[$action]), 404);
        Requests::role($u, [$roles[$action]]);
        if (in_array($action, ['send', 'cancel'])) {
            Requests::owner($u, $found);
        }
        if (count($r->all())) {
            throw ValidationException::withMessages(['body' => 'Se requiere cuerpo vacío.']);
        }
        $key = $r->header('Idempotency-Key');
        if ($action === 'reserve' && (! $key || strlen($key) > 128)) {
            throw ValidationException::withMessages(['Idempotency-Key' => 'Clave de 1 a 128 caracteres obligatoria.']);
        }
        $hash = hash('sha256', json_encode(['operation' => $action, 'requestId' => $requestId, 'body' => (object) []]));
        $error = fn ($code, $message) => ['code' => $code, 'message' => $message, 'correlationId' => $r->attributes->get('correlationId')];
        [$status,$body] = DB::transaction(function () use ($u, $requestId, $action, $key, $hash, $error) {
            $scope = DB::table('idempotency_records')->where('organization_id', $u->organization_id)->where('user_id', $u->id)->where('operation', $action)->where('idempotency_key', $key ?? '');
            if ($action === 'reserve') {
                if (DB::getDriverName() === 'pgsql') {
                    DB::select('SELECT pg_advisory_xact_lock(hashtextextended(?, 0))', [$u->organization_id.'|'.$u->id.'|reserve|'.$key]);
                }
                $previous = (clone $scope)->first();
                if ($previous && Carbon::parse($previous->expires_at)->gt(now())) {
                    return $previous->request_hash === $hash ? [(int) $previous->response_status, json_decode($previous->response_body, true)] : [409, $error('IDEMPOTENCY_CONFLICT', 'La clave ya se usó con otro contenido.')];
                }
                if ($previous) {
                    (clone $scope)->delete();
                }
            }
            $row = Requests::find($u, $requestId, true);
            $from = ['send' => ['BORRADOR'], 'reserve' => ['ENVIADA'], 'dispatch' => ['RESERVADA'], 'deliver' => ['DESPACHADA'], 'cancel' => ['ENVIADA', 'RESERVADA']];
            $to = ['send' => 'ENVIADA', 'reserve' => 'RESERVADA', 'dispatch' => 'DESPACHADA', 'deliver' => 'ENTREGADA', 'cancel' => 'CANCELADA'];
            $failure = null;
            if (! in_array($row->estado, $from[$action], true)) {
                $failure = $error('INVALID_STATE', 'La solicitud no permite esa transición.');
            }
            if (! $failure && $action === 'send' && Carbon::parse($row->fecha_requerida)->lt(now())) {
                throw ValidationException::withMessages(['fechaRequerida' => 'La fecha requerida no puede ser anterior al momento de envío.']);
            }
            $lines = DB::table('request_lines')->where('request_id', $requestId)->orderBy('sku')->get();
            $stocks = [];
            if (! $failure && in_array($action, ['reserve', 'dispatch', 'cancel']) && ! ($action === 'cancel' && $row->estado === 'ENVIADA')) {
                foreach ($lines as $l) {
                    $stock = DB::table('inventory_items')->where('organization_id', $u->organization_id)->where('sku', $l->sku)->lockForUpdate()->first();
                    $stocks[$l->sku] = $stock;
                    if ($action === 'reserve' && (! $stock || ! $stock->active || $stock->fisico - $stock->reservado < $l->cantidad)) {
                        $failure = $error('INSUFFICIENT_STOCK', 'Inventario insuficiente: no se reservó ninguna línea.');
                    }
                }
            }
            if ($failure) {
                $status = 409;
                $body = $failure;
            } else {
                foreach ($stocks as $sku => $stock) {
                    $quantity = (int) $lines->firstWhere('sku', $sku)->cantidad;
                    $physical = (int) $stock->fisico - ($action === 'dispatch' ? $quantity : 0);
                    $reserved = (int) $stock->reservado + ($action === 'reserve' ? $quantity : -$quantity);
                    if ($physical < 0 || $reserved < 0 || $reserved > $physical) {
                        throw new \RuntimeException('Stock invariant failed');
                    }
                    DB::table('inventory_items')->where('organization_id', $u->organization_id)->where('sku', $sku)->update(['fisico' => $physical, 'reservado' => $reserved]);
                }
                DB::table('requests')->where('id', $requestId)->update(['estado' => $to[$action], 'updated_at' => now()]);
                Audit::record($u, $requestId, strtoupper($action), $row->estado, $to[$action]);
                $status = 200;
                $body = Requests::serialize(Requests::find($u, $requestId));
            }
            if ($action === 'reserve') {
                DB::table('idempotency_records')->insert(['organization_id' => $u->organization_id, 'user_id' => $u->id, 'operation' => $action,
                    'idempotency_key' => $key, 'request_hash' => $hash, 'response_status' => $status, 'response_body' => json_encode($body), 'created_at' => now(), 'expires_at' => now()->addHours(24)]);
            }

            return [$status, $body];
        }, 3);

        return response()->json($body, $status);
    }
}
