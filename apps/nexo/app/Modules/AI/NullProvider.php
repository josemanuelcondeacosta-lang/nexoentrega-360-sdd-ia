<?php

namespace App\Modules\AI;

/** Sin proveedor configurado: activa siempre el resumen alternativo (AC-05-09). */
class NullProvider implements AiProvider
{
    public function complete(string $instructions, string $data, int $timeoutMs): string
    {
        throw new AiProviderException('proveedor_no_configurado');
    }

    public function origin(): array
    {
        return ['type' => 'fallback', 'provider' => null, 'model' => null, 'fixtureId' => null];
    }
}
