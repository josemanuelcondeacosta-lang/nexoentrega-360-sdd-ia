<?php

namespace App\Modules\AI;

/**
 * Proveedor intercambiable del asistente (ADR-004). No recibe herramientas ni acceso a datos:
 * solo un texto de instrucciones, un bloque de datos y un plazo.
 */
interface AiProvider
{
    /**
     * Devuelve el texto crudo del modelo.
     *
     * @throws AiTimeoutException si se supera el plazo
     * @throws AiProviderException si el proveedor falla o no está configurado
     */
    public function complete(string $instructions, string $data, int $timeoutMs): string;

    /** Origen para mostrar y auditar: type, provider, model y fixtureId. */
    public function origin(): array;
}
