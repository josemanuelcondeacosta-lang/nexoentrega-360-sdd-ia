# Intervención de IA

Formato del anexo oficial `08_ai-log-template.md`.

- **ID y fecha UTC:** AI-002, 2026-10-14T05:15Z
- **Autor:** Castro Edier (202311254074)
- **Etapa:** revisión crítica de especificación y diseño de pruebas (G1)
- **Herramienta y modelo conocido:** Claude (Anthropic), modelo Claude Opus 5.5, en claude.ai
- **RF y AC:** RF02, RF04–RF07, RF09, RNF02; AC-03-01 a AC-03-16, AC-01-09
- **Contexto y versiones de archivos:** commit `560c9cbe7972fcfe2258406ca7ed5f094b5a50e3` (SPEC-03 0.3-draft); anexos `acceptance-oracles.md` y `fixture-scenarios.json`; guía `docs/team/instrucciones-edier.md`

## Objetivo

Preparar el aporte individual de Edier: revisar SPEC-03 contra las invariantes de inventario y redactar los escenarios previstos de reserva, concurrencia, cancelación, despacho, entrega, fallo antes del commit e idempotencia, con datos iniciales, esperado y efectos persistidos.

## Prompt enviado

> terminemos con lo mio

Contexto dado en la misma conversación: la guía `instrucciones-edier.md` (tabla de escenarios con AC, datos iniciales, actor, acción, respuesta esperada, estado, stock, eventos y fuente; observado «no ejecutado»; registrar ambigüedades como preguntas).

## Salida relevante

- `docs/reviews/aportes/edier-G1.md`: seis comprobaciones documentales, 17 escenarios previstos (E-01 a E-17) con oráculos del anexo y cuatro hallazgos (H-E01 a H-E04).
- Hallazgos principales: falta el código HTTP del fallo inducido antes del commit (H-E01) y no hay AC para la carrera entre despacho y cancelación de la misma reserva (H-E02).

## Decisión humana

**PENDIENTE.** Edier debe revisar cada escenario y hallazgo y anotar aquí si los acepta, corrige o descarta, con su motivo y fecha UTC.

## Verificación

- Las cantidades esperadas se contrastaron con `acceptance-oracles.md` y con el seed de `fixture-scenarios.json`.
- `python scripts/export_specs.py --check` y `python scripts/build_coverage.py --check` sin errores sobre el SHA revisado.
- Verificación humana pendiente: Edier debe comprobar los escenarios contra la p. 4 del enunciado.

## Límites

Los escenarios son previstos; ninguno se ejecutó. Los hallazgos son propuestas para el equipo, no decisiones del cliente. Sin datos reales ni secretos.
