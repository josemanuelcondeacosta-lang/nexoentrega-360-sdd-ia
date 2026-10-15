<?php

namespace App\Modules\AI;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Orquesta el resumen (ADR-004): proyecta los seis campos autorizados, llama al proveedor con plazo de 5 s,
 * valida la salida y guarda un borrador; ante salida inválida, timeout o fallo usa el resumen alternativo.
 * No escribe solicitudes ni inventario (RF12). La selección ya llega autorizada y validada (403, 422, 404, 409).
 */
class AiSummaryService
{
    private const MESSAGES = [
        'invalid' => 'La respuesta del asistente no pasó la validación; se muestra un resumen automático.',
        'timeout' => 'El asistente no respondió a tiempo; se muestra un resumen automático.',
        'error' => 'El asistente no está disponible en este momento; se muestra un resumen automático.',
        'unavailable' => 'Proveedor de IA no configurado; se muestra un resumen automático.',
    ];

    public function __construct(
        private PromptBuilder $prompt = new PromptBuilder,
        private AiOutputValidator $validator = new AiOutputValidator,
        private FallbackSummarizer $fallback = new FallbackSummarizer,
    ) {}

    /** @param  array<int, object>  $rows  filas de requests de la organización, en ENVIADA */
    public function summarize(object $user, array $rows, array $requestIds): array
    {
        $projected = array_map(fn ($row) => [
            'requestId' => $row->codigo,
            'zona' => $row->zona,
            'prioridad' => $row->prioridad,
            'fechaRequerida' => Carbon::parse($row->fecha_requerida)->utc()->toIso8601String(),
            'estado' => $row->estado,
            'observaciones' => $row->observaciones,
        ], $rows);
        $idByCode = [];
        foreach ($rows as $row) {
            $idByCode[$row->codigo] = $row->id;
        }

        $attemptOrigin = null;
        $reason = null;
        try {
            $provider = AiProviderFactory::make();
            $attemptOrigin = $provider->origin()['type'];
            $raw = $provider->complete($this->prompt->instructions(), $this->prompt->data($projected), config('ai.timeout_ms'));
            $valid = $this->validator->validate($raw, array_keys($idByCode));
            $result = 'valid';
            $origin = $provider->origin();
            $message = null;
            $summary = $valid['summary'];
            $recommendations = array_map(fn ($rec) => ['requestId' => $idByCode[$rec['requestId']]] + $rec, $valid['recommendations']);
        } catch (AiTimeoutException $e) {
            [$result, $reason] = ['timeout', $e->getMessage()];
        } catch (AiProviderException $e) {
            [$result, $reason] = [$e->getMessage() === 'proveedor_no_configurado' ? 'unavailable' : 'error', $e->getMessage()];
        } catch (AiInvalidOutputException $e) {
            [$result, $reason] = ['invalid', $e->getMessage()];
        }
        if ($result !== 'valid') {
            $origin = ['type' => 'fallback', 'provider' => null, 'model' => null, 'fixtureId' => null];
            $message = self::MESSAGES[$result];
            $summary = $this->fallback->summarize($projected);
            $recommendations = [];
        }

        $payload = ['origin' => $origin, 'aiAssisted' => true, 'message' => $message, 'summary' => $summary,
            'recommendations' => $recommendations, 'requestIds' => array_values($requestIds)];
        $id = (string) Str::uuid();
        $now = now();
        DB::transaction(function () use ($user, $id, $payload, $result, $reason, $attemptOrigin, $now) {
            DB::table('ai_summaries')->insert(['id' => $id, 'organization_id' => $user->organization_id, 'coordinador_id' => $user->id,
                'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE), 'created_at' => $now]);
            // Intento sin prompt, sin clave y sin texto del modelo: solo origen, resultado y motivo sanitizado.
            DB::table('ai_attempts')->insert(['id' => (string) Str::uuid(), 'summary_id' => $id, 'result' => $result,
                'origin_type' => $attemptOrigin, 'reason' => $reason, 'created_at' => $now]);
        });

        return $payload + ['id' => $id, 'status' => 'BORRADOR', 'createdAt' => $now->toIso8601String(), 'approvedBy' => null, 'approvedAt' => null];
    }
}
