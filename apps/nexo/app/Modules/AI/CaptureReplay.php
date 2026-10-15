<?php

namespace App\Modules\AI;

use Illuminate\Console\Command;

/**
 * php artisan ai:capture — una llamada real a Gemini con datos ficticios fijos (A-001 y A-002) y el mismo
 * prompt del producto. Guarda entrada, salida cruda, modelo, fecha y resultado de validación en
 * resources/ai/replay/ para reproducirla después (§10, AC-05-13). Nunca guarda la clave.
 */
class CaptureReplay
{
    public const DATA = [
        ['requestId' => 'A-001', 'zona' => 'NORTE', 'prioridad' => 'ALTA', 'fechaRequerida' => '2030-01-11T15:00:00+00:00', 'estado' => 'ENVIADA', 'observaciones' => 'Familias desplazadas; entregar antes del mediodía.'],
        ['requestId' => 'A-002', 'zona' => 'SUR', 'prioridad' => 'NORMAL', 'fechaRequerida' => '2030-01-12T15:00:00+00:00', 'estado' => 'ENVIADA', 'observaciones' => 'Reposición mensual de un comedor comunitario.'],
    ];

    public function run(Command $cmd, ?string $model, ?int $timeoutMs = null, ?string $thinking = null): int
    {
        $productTimeout = config('ai.timeout_ms');
        $timeoutMs = max(1000, min($timeoutMs ?: $productTimeout, 60000));
        if ($thinking) {
            config(['ai.thinking_level' => $thinking]);
        }
        if (! config('ai.key')) {
            $cmd->error('Falta AI_API_KEY. Pásala solo al ejecutar: docker compose exec -e AI_API_KEY=... app php artisan ai:capture');

            return 1;
        }
        $model = $model ?: config('ai.model');
        if (! $model) {
            $models = (new GeminiProvider(model: 'none'))->flashModels();
            if (! $models) {
                $cmd->error('La clave no tiene modelos Flash con generateContent. Indica uno con --model=...');

                return 1;
            }
            $cmd->info('Modelos Flash disponibles: '.implode(', ', $models));
            $model = $models[0];
        }
        $cmd->info('Modelo: '.$model.($thinking ? ", pensamiento: $thinking" : '').", plazo de la captura: $timeoutMs ms");

        $prompt = new PromptBuilder;
        $provider = new GeminiProvider(model: $model);
        $start = hrtime(true);
        try {
            $raw = $provider->complete($prompt->instructions(), $prompt->data(self::DATA), $timeoutMs);
        } catch (AiProviderException $e) {
            $cmd->error('La llamada falló: '.$e->getMessage().($e->detail ? ' — '.$e->detail : '').'. No se guardó captura.');

            return 1;
        }
        $latency = (int) round((hrtime(true) - $start) / 1e6);
        try {
            (new AiOutputValidator)->validate($raw, array_column(self::DATA, 'requestId'));
            $validation = 'valid';
        } catch (AiInvalidOutputException $e) {
            $validation = 'invalid:'.$e->getMessage();
        }

        $capturedAt = now()->utc();
        $name = 'gemini-'.$capturedAt->format('Ymd\THis\Z').'.json';
        $capture = [
            'declaration' => 'Respuesta real de la API de Gemini obtenida con php artisan ai:capture. Se reproduce sin modificar; no es una invocación en vivo.',
            'capturedAt' => $capturedAt->toIso8601String(),
            'provider' => 'gemini',
            'model' => $model,
            'latencyMs' => $latency,
            'captureTimeoutMs' => $timeoutMs,
            // El producto corta a los 5 s; si la captura tardó más, en vivo se habría usado el resumen alternativo.
            'withinProductDeadline' => $latency <= $productTimeout,
            'thinkingLevel' => config('ai.thinking_level'),
            'input' => ['instructions' => $prompt->instructions(), 'data' => self::DATA],
            'rawOutput' => $raw,
            'validation' => $validation,
        ];
        $path = config('ai.replay_dir').'/'.$name;
        file_put_contents($path, json_encode($capture, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n");

        $cmd->info("Captura guardada: resources/ai/replay/$name ($latency ms, validación: $validation)");
        if ($latency > $productTimeout) {
            $cmd->warn("Tardó más que el plazo del producto ($productTimeout ms): en vivo se habría usado el resumen alternativo. Queda registrado en la captura.");
        }
        $cmd->line('Salida del modelo:');
        $cmd->line($raw);
        $cmd->line('Cópiala a tu carpeta con:');
        $cmd->line("docker compose cp app:/app/resources/ai/replay/$name apps/nexo/resources/ai/replay/$name");

        return $validation === 'valid' ? 0 : 2;
    }
}
