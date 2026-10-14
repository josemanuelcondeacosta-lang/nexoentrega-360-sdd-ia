<?php

namespace App\Modules\Requests;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RequestService
{
    public static function find($user, string $id, bool $lock = false)
    {
        $q = DB::table('requests')->where('organization_id', $user->organization_id)->where('id', $id);
        $row = ($lock ? $q->lockForUpdate() : $q)->first();
        abort_unless($row, 404, 'Solicitud no encontrada.');

        return $row;
    }

    public static function role($user, array $roles): void
    {
        abort_unless(in_array($user->role, $roles, true), 403, 'Esta operación no está permitida para tu rol.');
    }

    public static function owner($user, $row): void
    {
        abort_unless((int) $row->solicitante_id === (int) $user->id, 403, 'Solo el dueño puede realizar esta operación.');
    }

    public static function serialize($row): array
    {
        return ['id' => $row->id, 'codigo' => $row->codigo, 'organizationId' => $row->organization_id, 'solicitanteId' => (string) $row->solicitante_id,
            'estado' => $row->estado, 'zona' => $row->zona, 'prioridad' => $row->prioridad, 'fechaRequerida' => Carbon::parse($row->fecha_requerida)->utc()->toIso8601String(),
            'observaciones' => $row->observaciones, 'createdAt' => Carbon::parse($row->created_at)->utc()->toIso8601String(), 'updatedAt' => Carbon::parse($row->updated_at)->utc()->toIso8601String(),
            'lineas' => DB::table('request_lines')->where('request_id', $row->id)->get()->map(fn ($l) => ['sku' => $l->sku, 'cantidad' => (int) $l->cantidad])->all()];
    }

    public function save(Request $r, ?string $id = null)
    {
        $user = $r->user();
        self::role($user, ['Solicitante']);
        $data = $r->validate(['zona' => 'required|in:NORTE,SUR,CENTRO', 'prioridad' => 'required|in:NORMAL,ALTA', 'fechaRequerida' => 'required|date',
            'observaciones' => 'nullable|string|max:1000', 'lineas' => 'required|array|min:1|max:10', 'lineas.*' => 'array:sku,cantidad', 'lineas.*.sku' => 'required|string|distinct',
            'lineas.*.cantidad' => 'required|integer|min:1|max:500']);
        foreach ($data['lineas'] as $i => $l) {
            if (! is_int($l['cantidad']) || ! DB::table('inventory_items')->where('organization_id', $user->organization_id)->where('sku', $l['sku'])->where('active', true)->exists()) {
                throw ValidationException::withMessages(["lineas.$i" => 'SKU activo de tu organización y cantidad entera obligatorios.']);
            }
        }
        foreach (array_diff(array_keys($r->all()), ['zona', 'prioridad', 'fechaRequerida', 'observaciones', 'lineas']) as $extra) {
            throw ValidationException::withMessages([$extra => 'Campo no permitido.']);
        }
        $result = DB::transaction(function () use ($data, $user, $id) {
            $row = $id ? self::find($user, $id, true) : null;
            if ($row) {
                self::owner($user, $row);
                abort_unless($row->estado === 'BORRADOR', 409, 'Solo se editan borradores.');
            }
            $rid = $id ?? (string) Str::uuid();
            $values = ['zona' => $data['zona'], 'prioridad' => $data['prioridad'], 'fecha_requerida' => Carbon::parse($data['fechaRequerida'])->utc(),
                'observaciones' => $data['observaciones'] ?? null, 'updated_at' => now()];
            if ($row) {
                DB::table('requests')->where('id', $rid)->update($values);
            } else {
                DB::table('requests')->insert($values + ['id' => $rid, 'organization_id' => $user->organization_id, 'solicitante_id' => $user->id,
                    'codigo' => substr($user->organization_id, -1).'-'.strtoupper(substr($rid, 0, 8)), 'estado' => 'BORRADOR', 'created_at' => now()]);
            }
            DB::table('request_lines')->where('request_id', $rid)->delete();
            foreach ($data['lineas'] as $line) {
                DB::table('request_lines')->insert(['request_id' => $rid, 'organization_id' => $user->organization_id] + $line);
            }

            return self::serialize(self::find($user, $rid));
        });

        return response()->json($result, $id ? 200 : 201);
    }
}
