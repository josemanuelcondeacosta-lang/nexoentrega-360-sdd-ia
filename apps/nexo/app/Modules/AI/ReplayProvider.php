<?php

namespace App\Modules\AI;

/**
 * Reproducción declarada de una respuesta real capturada con ai:capture (§10, AC-05-13). Devuelve el
 * texto original sin modificarlo; pasa por el mismo validador y se muestra como reproducción, nunca
 * como invocación en vivo.
 */
class ReplayProvider implements AiProvider
{
    private array $capture;

    private string $name;

    public function __construct(?string $file = null)
    {
        $dir = config('ai.replay_dir');
        $file ??= config('ai.replay_file') ?: self::latest($dir);
        if (! $file || ! is_file($dir.'/'.$file)) {
            throw new AiProviderException('replay_sin_captura');
        }
        $this->name = pathinfo($file, PATHINFO_FILENAME);
        $this->capture = json_decode(file_get_contents($dir.'/'.$file), true, 64, JSON_THROW_ON_ERROR);
    }

    public static function latest(string $dir): ?string
    {
        $files = glob($dir.'/*.json') ?: [];
        sort($files);

        return $files ? basename(end($files)) : null;
    }

    public function complete(string $instructions, string $data, int $timeoutMs): string
    {
        return $this->capture['rawOutput'];
    }

    public function origin(): array
    {
        return ['type' => 'replay', 'provider' => $this->capture['provider'] ?? null, 'model' => $this->capture['model'] ?? null, 'fixtureId' => $this->name];
    }

    public function capture(): array
    {
        return $this->capture;
    }
}
