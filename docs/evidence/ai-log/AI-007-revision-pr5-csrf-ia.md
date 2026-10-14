# Intervención de IA

Formato del anexo oficial `08_ai-log-template.md`.

- **ID y fecha UTC:** AI-007, 2026-10-14T18:13Z a 18:25Z (aprox.)
- **Autor:** Vega José (202321256198)
- **Etapa:** revisión crítica y diseño (G2)
- **Herramienta y modelo conocido:** Claude (Anthropic), modelo Claude Opus 5.5, en claude.ai
- **RF y AC:** RF01, RF02, RF08, RF09, RF10–RF12, RNF01; AC-01-02, AC-01-07, AC-04-11, AC-05-11
- **Contexto y versiones de archivos:** PR #5 de José Conde (`b1dbbf9`: ADR-001, OpenAPI integrado, SPEC-01 y SPEC-04 0.3.1-draft tras la respuesta docente a CL-16/17/18); `contracts/openapi-ia.yaml` v0.1.0 (`a13aa17`); `scripts/build_openapi.py` de la rama de José

## Objetivo

Guiar la revisión de Vega al PR #5 y corregir el hallazgo R-V01 (falta de CSRF en las rutas de IA) en su propio fragmento de contrato.

## Prompt enviado

> Soy Vega, tengo que revisar el PR #8 de Edier y demas cosas, guiame en la revisión. Si me ves equivocado en algo corrigeme.

Continuación en la misma sesión: «Ya, hagamos lo otro que me falta».

## Salida relevante

- Cuatro comprobaciones para el PR #5 con comandos `git show … | Select-String`: CSRF en las escrituras de IA, 403 frente a 404 para el Solicitante, cambio de SPEC-01 a 0.3.1 y copia desactualizada de la estrategia de pruebas.
- Corrección señalada a Vega: en su primera respuesta afirmó que el nuevo texto de SPEC-01 incluía la regla 403/404; el diff no lo dice (la regla viene de la versión anterior). Vega lo comprobó y rehízo su respuesta.
- Hallazgos publicados por Vega en el PR #5: R-V01 (CSRF en IA), R-V02 (404 en lugar de 403 para solicitudes ajenas de la misma organización) y R-V03 (estrategia sin R-E01). Renovó su aprobación de SPEC-01 0.3.1-draft.
- `contracts/openapi-ia.yaml` v0.2.0: ambas escrituras de IA declaran `X-XSRF-TOKEN` (parámetro `CsrfToken`, idéntico al del contrato base) y la respuesta 419.

## Decisión humana

| Cambio | Decisión de Vega (aceptada / corregida / descartada) | Motivo | Fecha UTC |
|---|---|---|---|
| Revisión del PR #5 | Aceptada | Hallazgos R-V01 a R-V03 comprobados con los comandos y publicados en GitHub | 2026-10-14 |
| Corrección R-V01 en `openapi-ia.yaml` | PENDIENTE | | |

## Verificación

Ejecutada por el asistente en su espacio de trabajo el 2026-10-14:

- `openapi-spec-validator` sobre `contracts/openapi-ia.yaml` v0.2.0: válido, OpenAPI 3.1.1.
- Simulación de la regeneración en una copia de la rama de José (`b1dbbf9`) con el fragmento nuevo: `build_openapi.py` → «Contrato integrado coherente con base y fragmento IA»; `build_openapi.py --check` sin diferencias; `validate_openapi.py` → «OpenAPI 3.1.1 válido: 19 rutas, 21 operaciones; IA preservada».
- En el contrato regenerado, las 11 escrituras (POST y PATCH) declaran CSRF y 419, incluidas `crearResumenIA` y `aprobarResumenIA`.

## Límites

José debe regenerar `contracts/openapi.yaml` en su rama y actualizar `x-source-ia` con el nuevo commit. R-V02 y R-V03 los corrige José. Sin datos reales ni secretos.
