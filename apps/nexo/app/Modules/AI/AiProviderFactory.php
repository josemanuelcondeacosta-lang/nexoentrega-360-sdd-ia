<?php

namespace App\Modules\AI;

/** Elige el proveedor según AI_PROVIDER (ADR-004). Las pruebas pueden registrar otro en el contenedor. */
class AiProviderFactory
{
    public static function make(): AiProvider
    {
        if (app()->bound(AiProvider::class)) {
            return app(AiProvider::class);
        }

        return match (config('ai.provider')) {
            'gemini' => new GeminiProvider,
            'replay' => new ReplayProvider,
            'fixture' => new FixtureProvider,
            default => new NullProvider,
        };
    }
}
