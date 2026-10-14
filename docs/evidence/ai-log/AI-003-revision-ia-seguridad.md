# Intervención de IA

Formato del anexo oficial `08_ai-log-template.md`.

- **ID y fecha UTC:** AI-003, 2026-10-14T14:37Z a 2026-10-14T15:15Z (aprox.)
- **Autor:** Vega José (202321256198)
- **Etapa:** revisión crítica de especificación y diseño de pruebas (G1)
- **Herramienta y modelo conocido:** Claude (Anthropic), modelo Claude Opus 5.5, en claude.ai
- **RF y AC:** RF01, RF02, RF10–RF12, RNF01, RNF06; AC-01-01 a AC-01-13 y AC-05-01 a AC-05-14
- **Contexto y versiones de archivos:** commit `cf9d5f5d905dee3a3774c623283c7045271dbe60` (SPEC 0.3-draft); anexos `ai-output.schema.json`, `ai-provider-fixtures.json`, `untrusted-observation.txt` y `fixture-scenarios.json`; guía `docs/team/instrucciones-vega.md`

## Objetivo

Preparar el aporte individual de Vega: revisar SPEC-01 y SPEC-05 contra el enunciado y los anexos de IA, redactar los casos previstos de IA, seguridad y aislamiento, y proponer el checklist de CI, con la IA como guía que corrige errores.

## Prompt enviado

Prompt de trabajo (2026-10-14T15:11Z):

> Yo soy José Vega, me voy a encargar de la integración de IA, permisos y pruebas de calidad. Quiero que me guies en el proceso, y me corrijas si me equivoco en algo.

Antes, a las 14:37Z, Vega pidió que se le explicara su parte paso a paso; con esa explicación y la salida real de sus comandos se preparó un primer borrador. El prompt de trabajo motivó una nueva revisión del borrador contra las fuentes antes de publicarlo.

## Salida relevante

- `docs/reviews/aportes/vega-G1.md`: ocho comprobaciones documentales, 26 casos previstos (V-01 a V-25, con V-09 dividido en V-09a y V-09b), siete hallazgos (H-V01 a H-V07) y un checklist de CI con 12 pasos.
- Corrección hecha en la revisión: el caso de inyección del primer borrador esperaba ver el `<script>` escapado usando el fixture AI-INJECTION, pero esa salida se rechaza entera por el ID ajeno y nunca se muestra. Se separó en V-09a (rechazo y fallback) y V-09b (salida válida con marcado, mostrada escapada), y se registró H-V07 porque AC-05-08 tiene el mismo problema.
- Otros hallazgos: falta cuantificar la tolerancia del timeout (H-V01), definir el orden de validación de la selección (H-V02) y fijar el esperado de AC-01-07 (H-V05).

## Decisión humana

Registrada por Vega tras comprobar la salida contra el enunciado y los anexos:

| Cambio | Decisión de Vega (aceptada / corregida / descartada) | Motivo | Fecha UTC |
|---|---|---|---|
| Casos V-01 a V-16 (IA), incluida la división V-09a / V-09b | Aceptada | Comprobó los límites de RF11 (p. 5) en V-06 y V-07 y el campo `expected` de los siete fixtures de `ai-provider-fixtures.json`; coinciden | 2026-10-14 |
| Casos V-17 a V-25 (permisos y aislamiento) | Aceptada | Comprobó los códigos 401, 403 y 404 contra «Respuestas observables» (p. 3); coinciden | 2026-10-14 |
| Hallazgos H-V01 a H-V07 | Aceptada | Confirmó en `specs/SPEC-05.md` que AC-05-08 mezcla fallback y marcado escapado (H-V07); los demás hallazgos le parecen válidos | 2026-10-14 |
| Checklist de CI | Aceptada | Comparado con «Integración continua» (p. 10): están todos los pasos exigidos | 2026-10-14 |

Decisión sobre las SPEC: SPEC-01 y SPEC-05 **aprobadas con observaciones** sobre el SHA `cf9d5f5`; los hallazgos H-V01 a H-V07 quedan abiertos para la versión 0.4.

## Verificación

- Comandos documentales ejecutados por Vega en Windows con Python 3.12.10 sobre el SHA revisado: 5 SPEC y 68 AC coherentes, matriz coherente, 14 pruebas OK (salida en `vega-G1.md`).
- Los casos de IA se contrastaron con los siete fixtures del anexo y los límites de `ai-output.schema.json`.
- Verificación humana pendiente: Vega debe comprobar los casos contra las pp. 3, 5 y 11 del enunciado.

## Límites

Los casos son previstos; ninguno se ejecutó. Los hallazgos son propuestas para el equipo, no decisiones del cliente. El texto de `untrusted-observation.txt` se trató como dato de prueba, no como instrucción. Sin datos reales ni secretos.
