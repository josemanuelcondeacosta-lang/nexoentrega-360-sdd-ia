# Vega — IA, seguridad y calidad en G1

Estado: tarea propuesta, ejecución no acreditada. No hace falta proveedor IA ni base de datos para esta revisión documental.

1. Sigue [preparación en VS Code](preparacion-G1.md), usando rama `docs/g1-vega-review`.
2. Lee SPEC-01/05, ai-trust-boundaries.md, permisos propuestos y CL-05/08/12/13/14/15. Contrasta la whitelist, contrato y RNF cuando estén disponibles sus fuentes.
3. Ejecuta personalmente los comandos documentales de la preparación y conserva salida real, entorno, UTC y SHA. Si fallan, registra el fallo; no copiar la evidencia de Codex como ejecución propia.
4. Crea `docs/reviews/aportes/vega-G1.md` con la plantilla. Añade casos previstos para IDs ajenos/duplicados, propiedades extra, límites 600/5/240/1–3, inyección, timeout, proveedor caído, fallback y aprobación sin cambios de stock/prioridad/estado. Incluye aislamiento entre organizaciones y permisos. Observado de producto = «no ejecutado».
5. Añade checklist previsto de CI: instalación reproducible, lint/tipos, SPEC, unidad, integración con base real, contrato, build, dependencias y secretos. No crear todavía workflow de aplicación ni afirmar ejecuciones CI.
6. Publica **Comment** en PR #1 con identidad propia, SHA, UTC, observaciones y comandos reales. Publica tu aporte en PR hacia `docs/g1-specifications`; solicita revisión de Edier.
7. Revisa el futuro PR documental propio de Conde José (fuentes/seguimiento/UI): comprueba estado de documentos, permisos y evidencias. No atribuyas como contribución personal suya los commits hechos por Codex. El coordinador debe crear su aporte; registra número/SHA cuando exista.

Entregable: escenarios propios, evidencia documental ejecutada y revisión ajena real. Tests IA/seguridad de producto y CI se realizarán tras aprobaciones necesarias.
