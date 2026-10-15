<?php

namespace App\Modules\Operations;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * php artisan nexo:seed-carga — siembra 2.000 solicitudes ficticias para la prueba de carga RNF03 (AC-04-09).
 * Solo BORRADOR y ENVIADA: no reserva ni toca inventario. Cada ENVIADA lleva su evento SEND, de modo que la
 * auditoría sigue siendo coherente. Es idempotente: si las solicitudes de carga ya existen, no repite.
 */
class LoadSeed
{
    public const PREFIX = 'L';

    public function run(Command $cmd, int $count): int
    {
        $existing = DB::table('requests')->where('observaciones', 'Carga ficticia RNF03')->count();
        if ($existing >= $count) {
            $cmd->info("Ya hay $existing solicitudes de carga; no se siembra de nuevo.");

            return 0;
        }

        $users = DB::table('users')->where('role', 'Solicitante')->pluck('id', 'organization_id');
        $zonas = ['NORTE', 'SUR', 'CENTRO'];
        $start = now()->utc();
        $created = 0;
        foreach (array_chunk(range($existing + 1, $count), 500) as $chunk) {
            [$requests, $lines, $events] = [[], [], []];
            foreach ($chunk as $i) {
                // 90 % en ORG-A y 10 % en ORG-B, para que el aislamiento también participe en los conteos.
                $org = $i % 10 === 0 ? 'ORG-B' : 'ORG-A';
                $id = (string) Str::uuid();
                $estado = intdiv($i, 10) % 5 < 3 ? 'BORRADOR' : 'ENVIADA';
                $createdAt = $start->copy()->subMinutes($i);
                $requests[] = ['id' => $id, 'organization_id' => $org, 'solicitante_id' => $users[$org],
                    'codigo' => substr($org, -1).'-'.self::PREFIX.str_pad((string) $i, 5, '0', STR_PAD_LEFT),
                    'estado' => $estado, 'zona' => $zonas[$i % 3], 'prioridad' => $i % 4 === 0 ? 'ALTA' : 'NORMAL',
                    'fecha_requerida' => $start->copy()->addDays(1 + $i % 30), 'observaciones' => 'Carga ficticia RNF03',
                    'created_at' => $createdAt, 'updated_at' => $createdAt];
                $lines[] = ['request_id' => $id, 'organization_id' => $org, 'sku' => 'KIT-AGUA', 'cantidad' => 1 + $i % 5];
                if ($estado === 'ENVIADA') {
                    $events[] = ['id' => (string) Str::uuid(), 'organization_id' => $org, 'actor_id' => $users[$org],
                        'resource_id' => $id, 'action' => 'SEND', 'before_state' => 'BORRADOR', 'after_state' => 'ENVIADA',
                        'occurred_at' => $createdAt, 'correlation_id' => (string) Str::uuid()];
                }
            }
            DB::transaction(function () use ($requests, $lines, $events) {
                DB::table('requests')->insert($requests);
                DB::table('request_lines')->insert($lines);
                DB::table('audit_events')->insert($events);
            });
            $created += count($chunk);
        }
        $cmd->info("Sembradas $created solicitudes de carga (total ".($existing + $created).').');

        return 0;
    }
}
