# Intervención de IA

Formato del anexo oficial `08_ai-log-template.md`.

- **ID y fecha UTC:** AI-004, 2026-10-14T16:04Z (aprox.)
- **Autor:** Vega José (202321256198)
- **Etapa:** diseño (G2)
- **Herramienta y modelo conocido:** Claude (Anthropic), modelo Claude Opus 5.5, en claude.ai
- **RF y AC:** RF02, RF09–RF12, RNF01, RNF06; AC-05-01 a AC-05-14
- **Contexto y versiones de archivos:** rama `docs/g1-vega-review` en `1b3bb37` (SPEC 0.3-draft y revisión G1 de Vega con hallazgos H-V01 a H-V07); anexos `ai-output.schema.json` y `ai-provider-fixtures.json`; enunciado §6, §9, §10 y §11

## Objetivo

Iniciar la parte de Vega en G2: ADR de integración de IA y degradación, secuencia de consulta de IA con fallo, contrato del asistente y estrategia de pruebas.

## Prompt enviado

> Ayudame a empezar con el dia dos

Decisiones dadas por Vega antes de redactar, ante dos preguntas de la IA: tecnología del equipo **Laravel** (después el equipo eligió **PostgreSQL**); salida de LLM real mediante **API con plan gratuito**.

## Salida relevante

- `docs/adr/ADR-004-integracion-ia-degradacion.md`: compara tres arquitecturas y tres orígenes de LLM real; decide adaptador + validador + fallback con proveedor configurable (`AI_PROVIDER`) y clave solo en `.env`.
- `docs/architecture/secuencia-ia-con-fallo.md`: diagrama Mermaid con fronteras de confianza y ramas válida, inválida y timeout/error.
- `contracts/openapi-ia.yaml`: fragmento OpenAPI 3.1.1 con `POST /api/ai/summaries` y `POST /api/ai/summaries/{summaryId}/approve`, para integrar en el contrato base.
- `contracts/ai-output.schema.json`: copia de trabajo del anexo oficial (SHA-256 idéntico al original).
- `docs/testing/estrategia-pruebas.md`: niveles, herramientas en Laravel, CI y tabla de resultados de los siete fixtures.

## Decisión humana

Registrada por Vega tras comprobar cada archivo:

| Cambio | Decisión de Vega (aceptada / corregida / descartada) | Motivo | Fecha UTC |
|---|---|---|---|
| ADR-004 (decisión y alternativas) | Aceptada | Se elige la opción B (adaptador + validador + fallback); la C se descarta por seguridad porque expone la clave y el servidor no valida la pertenencia | 2026-10-14 |
| Secuencia de IA con fallo | Aceptada | `FallbackSummarizer` se invoca en las dos ramas exigidas, salida inválida y timeout o error (líneas 41 y 48) | 2026-10-14 |
| Contrato `openapi-ia.yaml` | Aceptada | Límites comparados con RF10 y RF11 (p. 5): `maxItems` 20 (l. 155), `maxLength` 240 (l. 171), `maxItems` 3 (l. 175), `maxLength` 600 (l. 211) y `maxItems` 5 (l. 214); coinciden | 2026-10-14 |
| Estrategia de pruebas | Aceptada | AI-FOREIGN-ID y AI-INJECTION pasan el esquema y se rechazan por pertenencia; la estrategia prohíbe SQLite y mocks de inventario (§11) | 2026-10-14 |

## Verificación

Ejecutada por el asistente en su espacio de trabajo el 2026-10-14:

- `openapi-spec-validator` sobre `contracts/openapi-ia.yaml`: válido, OpenAPI 3.1.1, dos rutas.
- Los dos ejemplos de respuesta cumplen el esquema `BorradorResumen` y sus `summary` y `recommendations` cumplen `ai-output.schema.json`.
- Los siete fixtures oficiales se evaluaron contra el esquema y contra la selección {A-001, A-002}: AI-VALID válido; AI-JSON-BAD JSON roto; AI-EXTRA falla el esquema; AI-FOREIGN-ID y AI-INJECTION **pasan el esquema** y solo los rechaza la pertenencia; AI-TIMEOUT y AI-ERROR son fallos de transporte.
- `contracts/ai-output.schema.json` tiene el mismo SHA-256 que el anexo oficial (`93365b77…4c53`).

Verificación humana pendiente: Vega debe repetir la revisión del diagrama (vista previa Mermaid) y del contrato.

## Límites

- El equipo acordó Laravel con PostgreSQL el 2026-10-14; falta registrarlo en ADR-001.
- Los límites del plan gratuito de Gemini o Groq cambian; se verifican al implementar.
- Nada se implementó ni se ejecutó contra un proveedor real. Sin claves ni datos reales.
