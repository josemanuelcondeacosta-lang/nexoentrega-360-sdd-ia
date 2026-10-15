# Intervención de IA

Formato del anexo oficial `08_ai-log-template.md`.

- **ID y fecha UTC:** AI-011, 2026-10-15T00:41Z a 01:20Z (aprox.)
- **Autor:** Vega José (josevegalago2006-cpu)
- **Etapa:** revisión (PR #10) e implementación del asistente de IA (TSK-05)
- **Herramienta y modelo conocido:** Claude (Anthropic), modelo Claude Opus 5.5, en claude.ai
- **RF y AC:** RF10, RF11, RF12, RF09, RNF01, RNF06; AC-05-01 a AC-05-17
- **Contexto y versiones de archivos:** PR #10 (`0886219`: SPEC-01 y SPEC-05 0.4); rama `feat/provisional-frontend-integration` (`8ff8018`, PR #12) con `AiController.php` que solo generaba el resumen alternativo; ADR-004 propio; anexos `ai-provider-fixtures.json`, `ai-output.schema.json` y `untrusted-observation.txt`; guía `docs/team/continuar-ia.md`

## Objetivo

Guiar la revisión de Vega a SPEC-01 y SPEC-05 0.4 e implementar el asistente según su ADR-004: proveedor intercambiable, validación de esquema y pertenencia, corte a los 5 s, resumen alternativo y salida real reproducible.

## Prompt enviado

> Yo soy Jose Vega, que debo hacer, debo recortar tiempo, es indispensable agregar la parte de IA que me corresponde.

Decisiones tomadas por Vega con el selector de preguntas: tiene Docker Desktop y elige Gemini como proveedor.

## Salida relevante

- **Revisión del PR #10:** tabla de búsquedas para comprobar H-V01 a H-V07 y R-V02 en el diff, y plantilla de comentario.
- **Corrección señalada a Vega:** su comentario tenía en la línea de la fecha el comando de PowerShell en lugar de su resultado; lo corrigió antes de publicarlo.
- **Código** en `apps/nexo/app/Modules/AI/`:
  - `AiProvider` y sus implementaciones `GeminiProvider` (clave en el encabezado `x-goog-api-key`, salida JSON, temperatura 0, corte a 5 s), `FixtureProvider` (siete fixtures oficiales, sin red), `ReplayProvider` (captura real declarada) y `NullProvider`; `AiProviderFactory` según `AI_PROVIDER`.
  - `AiOutputValidator`: JSON estricto, reglas del esquema oficial y pertenencia de cada `requestId` a la selección, con motivos sanitizados.
  - `FallbackSummarizer`, `PromptBuilder` (seis campos autorizados; observaciones como datos) y `AiSummaryService`.
  - `AiController`: orden 403 → 422 → 404 → 409 sin invocar al proveedor; los identificadores deben ser UUID.
  - `CaptureReplay` y comando `php artisan ai:capture`: una llamada real con datos ficticios fijos, guardada sin la clave en `apps/nexo/resources/ai/replay/`.
  - Migración `2026_10_15_000001_add_ai_attempt_details.php` (origen y motivo del intento) y `/ready` comprueba esa columna.
  - `public/app.js`: muestra origen, mensaje, resumen y recomendaciones escapados.
- **Pruebas** en `apps/nexo/tests/Feature/AiAssistantTest.php` (AC-05-01, 02, 03, 05, 06, 07, 08, 09, 12, 13, 14 y 17).
- ADR-004 pasa a v0.2 con la ubicación real de las piezas y la tolerancia fijada.
- Ajuste tras la primera captura real: con `gemini-2.5-flash` y `gemini-flash-lite-latest` la llamada superó los 5 s (`gemini_timeout`) y `gemini-2.5-flash-lite` respondió 404. Una medición con `curl` desde el contenedor dio 0,52 s de red, así que la demora era del modelo. `GeminiProvider` desactiva el «pensamiento» en los modelos 2.5 Flash (`thinkingBudget: 0`) y admite `AI_FORCE_IPV4` como opción de red, desactivada por defecto.
- Segundo ajuste: `gemini-2.5-flash` respondió 404 (ya no disponible para la clave), `gemini-3.5-flash-lite` y `gemini-3.8-flash` superaron los 5 s y `gemini-3.5-flash` respondió 503. Se añadió `AI_THINKING_LEVEL` para modelos 3.x y, solo en `ai:capture`, las opciones `--thinking` y `--timeout` (máximo 60 s); la captura registra `latencyMs`, `captureTimeoutMs` y `withinProductDeadline` para declarar si la respuesta real habría llegado dentro del plazo del producto. El error de la API se muestra en la consola sin la clave.

## Decisión humana

| Cambio | Decisión de Vega (aceptada / corregida / descartada) | Motivo | Fecha UTC |
|---|---|---|---|
| Revisión del PR #10 (comentario `#issuecomment-6181833055`) | PENDIENTE | | |
| Implementación del asistente y pruebas | PENDIENTE | | |
| Captura real con Gemini (`ai:capture`) | PENDIENTE | | |

## Verificación

Ejecutada por el asistente en su espacio de trabajo el 2026-10-15 (UTC), sobre `8ff8018` más estos cambios, con PHP 8.3.6 y PostgreSQL 16 locales (el proyecto usa PHP 8.5 y PostgreSQL 17.6 en Docker):

- `php artisan test`: 37 pruebas correctas (398 aserciones) y 1 omitida, la reproducción de AC-05-13, porque todavía no hay captura real.
- AC-05-03: con un servidor local que acepta la conexión y no responde, el resumen alternativo llegó en 5,17 s (límite 5,25 s).
- `ai:capture` probado contra un servidor local que imita a Gemini: guardó la captura sin la clave y la reproducción pasó el validador. Esas capturas falsas se borraron; no forman parte del cambio.
- Prueba en navegador (Chromium, 1366 px) con el fixture AI-VALID: se ven el origen, el resumen y la recomendación de A-001; la aprobación funciona y `window.__unsafe` no existe.
- `vendor/bin/pint --test` y `node --check public/app.js`: correctos.

La ejecución válida para el proyecto es la de Vega con Docker, la captura real con su clave y el CI del PR.

## Límites

Sin la captura real, AC-05-13 queda omitido. El escape en pantalla se comprobó en navegador y con una verificación del código fuente, no con una prueba E2E automatizada. La clave no se guarda en Git ni en la captura; Gemini puede usar los datos enviados en el plan gratuito, por eso solo se envían datos ficticios.
