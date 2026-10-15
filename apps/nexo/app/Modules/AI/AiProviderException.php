<?php

namespace App\Modules\AI;

use RuntimeException;

/** Fallo del proveedor o proveedor no configurado: activa el resumen alternativo. */
class AiProviderException extends RuntimeException {}
