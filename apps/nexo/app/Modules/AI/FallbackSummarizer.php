<?php

namespace App\Modules\AI;

use Illuminate\Support\Carbon;

/**
 * Resumen determinista alternativo (RF12, CL-08): conteos por prioridad y zona y la fecha requerida
 * más próxima, sin recomendaciones. La misma selección con los mismos datos produce el mismo texto.
 */
class FallbackSummarizer
{
    /** @param  array<int, array{prioridad: string, zona: string, fechaRequerida: string}>  $rows */
    public function summarize(array $rows): string
    {
        $alta = count(array_filter($rows, fn ($r) => $r['prioridad'] === 'ALTA'));
        $zonas = array_values(array_unique(array_map(fn ($r) => $r['zona'], $rows)));
        sort($zonas);
        $fechas = array_map(fn ($r) => Carbon::parse($r['fechaRequerida'])->utc()->toDateString(), $rows);
        sort($fechas);
        $zonasTexto = count($zonas) === 1 ? 'zona '.$zonas[0] : 'zonas '.implode(', ', array_slice($zonas, 0, -1)).' y '.end($zonas);

        return count($rows).' solicitudes: '.$alta.' ALTA, '.(count($rows) - $alta).' NORMAL; '.$zonasTexto.'; fecha más próxima '.$fechas[0].'.';
    }
}
