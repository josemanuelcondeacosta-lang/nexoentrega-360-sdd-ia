<?php

namespace App\Modules\AI;

use Carbon\Carbon;

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
        if (! $file || basename($file) !== $file || ! is_file($dir.'/'.$file)) {
            throw new AiProviderException('replay_sin_captura');
        }
        $this->name = pathinfo($file, PATHINFO_FILENAME);
        try {
            $capture = json_decode(file_get_contents($dir.'/'.$file), true, 64, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new AiProviderException('replay_captura_invalida');
        }
        if (! is_array($capture) || ! is_string($capture['rawOutput'] ?? null)
            || ! is_string($capture['input']['instructions'] ?? null) || ! is_array($capture['input']['data'] ?? null)) {
            throw new AiProviderException('replay_captura_invalida');
        }
        $this->capture = $capture;
    }

    public static function latest(string $dir): ?string
    {
        $files = glob($dir.'/*.json') ?: [];
        sort($files);

        return $files ? basename(end($files)) : null;
    }

    public function complete(string $instructions, string $data, int $timeoutMs): string
    {
        try {
            $start = strpos($data, '{');
            $decoded = json_decode($start === false ? '' : substr($data, $start), true, 32, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new AiProviderException('replay_contexto_invalido');
        }
        $normalizeText = fn (string $text) => trim(str_replace("\r\n", "\n", $text));
        if ($normalizeText($instructions) !== $normalizeText($this->capture['input']['instructions'])
            || $this->context($decoded['solicitudes'] ?? null) !== $this->context($this->capture['input']['data'])) {
            throw new AiProviderException('replay_contexto_distinto');
        }

        return $this->capture['rawOutput'];
    }

    private function context(mixed $rows): array
    {
        if (! is_array($rows) || ! array_is_list($rows) || count($rows) < 1 || count($rows) > 20) {
            throw new AiProviderException('replay_contexto_invalido');
        }
        $result = [];
        foreach ($rows as $row) {
            if (! is_array($row) || ! is_string($row['requestId'] ?? null)
                || array_diff(array_keys($row), PromptBuilder::FIELDS) || array_diff(PromptBuilder::FIELDS, array_keys($row))) {
                throw new AiProviderException('replay_contexto_invalido');
            }
            $normalized = [];
            foreach (PromptBuilder::FIELDS as $field) {
                $normalized[$field] = $row[$field];
            }
            try {
                $normalized['fechaRequerida'] = Carbon::parse($row['fechaRequerida'])->utc()->format('Y-m-d\TH:i:s.uP');
            } catch (\Throwable) {
                throw new AiProviderException('replay_contexto_invalido');
            }
            $result[] = $normalized;
        }
        usort($result, fn (array $a, array $b) => strcmp($a['requestId'], $b['requestId']));

        return $result;
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
