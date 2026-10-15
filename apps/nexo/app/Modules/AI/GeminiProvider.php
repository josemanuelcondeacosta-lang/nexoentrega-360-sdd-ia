<?php

namespace App\Modules\AI;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * Proveedor real: API de Gemini (Google AI Studio, plan gratuito). La clave viaja en el encabezado
 * x-goog-api-key, nunca en la URL ni en logs. Se pide salida JSON y temperatura 0.
 */
class GeminiProvider implements AiProvider
{
    public function __construct(private ?string $key = null, private ?string $model = null, private ?string $baseUrl = null)
    {
        $this->key ??= config('ai.key');
        $this->model ??= config('ai.model');
        $this->baseUrl ??= config('ai.gemini_base_url');
    }

    public function complete(string $instructions, string $data, int $timeoutMs): string
    {
        if (! $this->key || ! $this->model) {
            throw new AiProviderException('gemini_sin_clave_o_modelo');
        }
        $seconds = $timeoutMs / 1000;
        try {
            $response = Http::timeout($seconds)->connectTimeout($seconds)->acceptJson()
                ->withHeaders(['x-goog-api-key' => $this->key])
                ->post(rtrim($this->baseUrl, '/').'/v1beta/models/'.rawurlencode($this->model).':generateContent', [
                    'systemInstruction' => ['parts' => [['text' => $instructions]]],
                    'contents' => [['role' => 'user', 'parts' => [['text' => $data]]]],
                    'generationConfig' => ['responseMimeType' => 'application/json', 'temperature' => 0],
                ]);
        } catch (ConnectionException $e) {
            throw str_contains(strtolower($e->getMessage()), 'timed out') || str_contains($e->getMessage(), 'cURL error 28')
                ? new AiTimeoutException('gemini_timeout')
                : new AiProviderException('gemini_conexion');
        }
        if (! $response->successful()) {
            throw new AiProviderException('gemini_http_'.$response->status());
        }
        $text = $response->json('candidates.0.content.parts.0.text');
        if (! is_string($text)) {
            throw new AiProviderException('gemini_sin_texto');
        }

        return $text;
    }

    public function origin(): array
    {
        return ['type' => 'provider', 'provider' => 'gemini', 'model' => $this->model, 'fixtureId' => null];
    }

    /** Modelos Flash de la clave que admiten generateContent (para ai:capture). */
    public function flashModels(): array
    {
        $response = Http::timeout(10)->acceptJson()->withHeaders(['x-goog-api-key' => $this->key])
            ->get(rtrim($this->baseUrl, '/').'/v1beta/models', ['pageSize' => 200]);
        if (! $response->successful()) {
            throw new AiProviderException('gemini_http_'.$response->status());
        }

        return collect($response->json('models', []))
            ->filter(fn ($m) => in_array('generateContent', $m['supportedGenerationMethods'] ?? [], true) && str_contains($m['name'] ?? '', 'flash'))
            ->map(fn ($m) => str_replace('models/', '', $m['name']))
            ->values()->all();
    }
}
