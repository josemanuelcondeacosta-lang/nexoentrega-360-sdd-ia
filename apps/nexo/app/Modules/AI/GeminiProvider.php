<?php

namespace App\Modules\AI;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
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
            $generation = ['responseMimeType' => 'application/json', 'temperature' => 0];
            // Los modelos 2.5 Flash «piensan» por defecto y pueden pasar del plazo; un resumen corto no lo necesita.
            if (str_starts_with($this->model, 'gemini-2.5-flash')) {
                $generation['thinkingConfig'] = ['thinkingBudget' => 0];
            }
            $response = $this->http($seconds)->post(rtrim($this->baseUrl, '/').'/v1beta/models/'.rawurlencode($this->model).':generateContent', [
                'systemInstruction' => ['parts' => [['text' => $instructions]]],
                'contents' => [['role' => 'user', 'parts' => [['text' => $data]]]],
                'generationConfig' => $generation,
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

    private function http(float $seconds): PendingRequest
    {
        $request = Http::timeout($seconds)->connectTimeout($seconds)->acceptJson()->withHeaders(['x-goog-api-key' => $this->key]);

        // Docker Desktop puede intentar IPv6 sin ruta y gastar el plazo en conectar; AI_FORCE_IPV4=true lo evita.
        return config('ai.force_ipv4') ? $request->withOptions(['force_ip_resolve' => 'v4']) : $request;
    }

    public function origin(): array
    {
        return ['type' => 'provider', 'provider' => 'gemini', 'model' => $this->model, 'fixtureId' => null];
    }

    /** Modelos Flash de la clave que admiten generateContent (para ai:capture). */
    public function flashModels(): array
    {
        $response = $this->http(10)->get(rtrim($this->baseUrl, '/').'/v1beta/models', ['pageSize' => 200]);
        if (! $response->successful()) {
            throw new AiProviderException('gemini_http_'.$response->status());
        }

        return collect($response->json('models', []))
            ->filter(fn ($m) => in_array('generateContent', $m['supportedGenerationMethods'] ?? [], true) && str_contains($m['name'] ?? '', 'flash'))
            ->map(fn ($m) => str_replace('models/', '', $m['name']))
            ->values()->all();
    }
}
