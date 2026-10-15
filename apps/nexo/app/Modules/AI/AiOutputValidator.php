<?php

namespace App\Modules\AI;

/**
 * Valida la salida del modelo (RF11, ADR-004):
 * 1) JSON estricto; 2) esquema oficial ai-output.schema.json; 3) en código, cada requestId pertenece
 * a la selección y no se repite. El esquema se implementa aquí de forma explícita para no depender de
 * una librería: las reglas son exactamente las de resources/ai/ai-output.schema.json.
 */
class AiOutputValidator
{
    private const ROOT_KEYS = ['summary', 'recommendations'];

    private const ITEM_KEYS = ['requestId', 'reason', 'evidenceFields'];

    private const EVIDENCE = ['zona', 'prioridad', 'fechaRequerida', 'observaciones'];

    /**
     * @param  string[]  $selectedCodes  códigos de las solicitudes seleccionadas por el coordinador
     * @return array{summary: string, recommendations: array<int, array{requestId: string, reason: string, evidenceFields: string[]}>}
     *
     * @throws AiInvalidOutputException con un código sanitizado
     */
    public function validate(string $raw, array $selectedCodes): array
    {
        try {
            $data = json_decode($raw, false, 32, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new AiInvalidOutputException('json_invalido');
        }
        if (! $data instanceof \stdClass) {
            throw new AiInvalidOutputException('raiz_no_objeto');
        }
        $this->exactKeys($data, self::ROOT_KEYS, 'raiz');
        $this->text($data->summary, 1, 600, 'summary');
        if (! is_array($data->recommendations) || ! array_is_list($data->recommendations)) {
            throw new AiInvalidOutputException('recommendations_no_lista');
        }
        if (count($data->recommendations) > 5) {
            throw new AiInvalidOutputException('recommendations_mas_de_5');
        }

        $seen = [];
        $recommendations = [];
        foreach ($data->recommendations as $item) {
            if (! $item instanceof \stdClass) {
                throw new AiInvalidOutputException('recomendacion_no_objeto');
            }
            $this->exactKeys($item, self::ITEM_KEYS, 'recomendacion');
            $this->text($item->requestId, 1, null, 'requestId');
            $this->text($item->reason, 1, 240, 'reason');
            $fields = $item->evidenceFields;
            if (! is_array($fields) || ! array_is_list($fields) || count($fields) < 1 || count($fields) > 3) {
                throw new AiInvalidOutputException('evidenceFields_cantidad');
            }
            foreach ($fields as $field) {
                if (! is_string($field) || ! in_array($field, self::EVIDENCE, true)) {
                    throw new AiInvalidOutputException('evidenceFields_no_permitido');
                }
            }
            if (count(array_unique($fields)) !== count($fields)) {
                throw new AiInvalidOutputException('evidenceFields_repetido');
            }
            // Pertenencia en código: el esquema no puede saber qué se seleccionó.
            if (! in_array($item->requestId, $selectedCodes, true)) {
                throw new AiInvalidOutputException('id_no_seleccionado');
            }
            if (isset($seen[$item->requestId])) {
                throw new AiInvalidOutputException('id_repetido');
            }
            $seen[$item->requestId] = true;
            $recommendations[] = ['requestId' => $item->requestId, 'reason' => $item->reason, 'evidenceFields' => $fields];
        }

        return ['summary' => $data->summary, 'recommendations' => $recommendations];
    }

    private function exactKeys(\stdClass $object, array $keys, string $where): void
    {
        $actual = array_keys(get_object_vars($object));
        if (array_diff($actual, $keys)) {
            throw new AiInvalidOutputException($where.'_propiedad_extra');
        }
        if (array_diff($keys, $actual)) {
            throw new AiInvalidOutputException($where.'_falta_propiedad');
        }
    }

    private function text(mixed $value, int $min, ?int $max, string $field): void
    {
        if (! is_string($value)) {
            throw new AiInvalidOutputException($field.'_no_texto');
        }
        // minLength/maxLength de JSON Schema cuentan caracteres Unicode, no bytes.
        $length = mb_strlen($value, 'UTF-8');
        if ($length < $min || ($max !== null && $length > $max)) {
            throw new AiInvalidOutputException($field.'_longitud');
        }
    }
}
