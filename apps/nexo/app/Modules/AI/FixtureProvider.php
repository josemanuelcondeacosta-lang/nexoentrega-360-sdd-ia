<?php

namespace App\Modules\AI;

/**
 * Proveedor determinista con los siete fixtures oficiales (ai-provider-fixtures.json). Lo usa el CI:
 * sin red, sin clave y sin costo (RNF06, AC-05-14). El timeout se simula comparando la latencia
 * declarada con el plazo, sin dormir.
 */
class FixtureProvider implements AiProvider
{
    private array $fixture;

    public function __construct(?string $fixtureId = null)
    {
        $fixtureId ??= config('ai.fixture_id');
        $all = json_decode(file_get_contents(config('ai.fixtures_file')), true, 32, JSON_THROW_ON_ERROR)['fixtures'];
        $found = array_values(array_filter($all, fn ($f) => $f['id'] === $fixtureId));
        if (! $found) {
            throw new \InvalidArgumentException('Fixture desconocido: '.$fixtureId);
        }
        $this->fixture = $found[0];
    }

    public function complete(string $instructions, string $data, int $timeoutMs): string
    {
        return match ($this->fixture['transport']) {
            'success' => $this->fixture['rawOutput'],
            'timeout' => ($this->fixture['simulatedLatencyMs'] ?? PHP_INT_MAX) > $timeoutMs
                ? throw new AiTimeoutException('fixture_timeout')
                : throw new AiProviderException('fixture_latencia_menor_al_plazo'),
            default => throw new AiProviderException('fixture_error_proveedor'),
        };
    }

    public function origin(): array
    {
        return ['type' => 'fixture', 'provider' => null, 'model' => null, 'fixtureId' => $this->fixture['id']];
    }
}
