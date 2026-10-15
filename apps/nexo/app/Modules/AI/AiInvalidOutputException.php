<?php

namespace App\Modules\AI;

use RuntimeException;

/** Salida del modelo rechazada. El mensaje es un código sanitizado, nunca el texto del modelo. */
class AiInvalidOutputException extends RuntimeException {}
