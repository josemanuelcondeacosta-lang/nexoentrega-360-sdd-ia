<?php

namespace App\Modules\AI;

use RuntimeException;

/** Fallo del proveedor o proveedor no configurado: activa el resumen alternativo. */
class AiProviderException extends RuntimeException
{
    /** Mensaje de error devuelto por la API (sin la clave); solo se muestra en ai:capture, no al navegador. */
    public ?string $detail = null;
}
