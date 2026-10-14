# Constitution — borrador v0.3

**Estado: pendiente de revisión humana G1.**

1. **Fuente verificable:** cada funcionalidad se aprueba mediante SPEC versionada antes de modificar código. Comprobación: fecha/commit G1 y PR asociado.
2. **Aislamiento:** organización y rol provienen de autenticación; solicitudes ajenas dan 404; pruebas de lecturas y escrituras cruzadas.
3. **Integridad del inventario:** disponible = físico − reservado; nunca negativo; transacción atómica y pruebas concurrentes sobre base real.
4. **Estados controlados:** únicamente transiciones RF04; rechazos 409 sin modificar estado, stock ni generar un evento de éxito. Conservar respuesta idempotente de un 409 de negocio puede ser válido según política explícita CL-04; no equivale a reservar.
5. **Trazabilidad:** cada RF/RNF conecta HU, SPEC, AC, módulo, prueba, PR y evidencia; registrar brechas honestamente.
6. **IA no privilegiada:** el asistente no modifica stock, estado ni prioridad; validar entrada/salida; aprobación de coordinador con auditoría y fallback.
7. **Seguridad:** cuentas ficticias, contraseñas con hash, secretos fuera de Git, logs estructurados sin secretos.
8. **Verificación:** checks reproducibles, pruebas de API, base real, E2E, regresión, rendimiento y accesibilidad manual; jamás reportar pruebas no ejecutadas.
9. **Revisión independiente:** cada PR lleva revisión de otro integrante y evidencia; discrepancias documentadas.
10. **Entrega:** tags `baseline-v1` y `release-examen`, SHA, instrucciones de clon limpio y PDF idéntico para todo el equipo.

## Controles de aplicación — ampliación 0.2-draft

| Principio | Evidencia requerida antes de declararlo cumplido |
|---|---|
| 1 y 9 | Fuente oficial, SHA revisado, revisor humano distinto, hallazgos y decisión G1/G2; no usar autoría asistida como aprobación |
| 2 y 7 | Pruebas de rol/sesión/aislamiento y revisión de logs, respuestas, seed ficticio y secretos |
| 3 y 4 | Snapshots de PostgreSQL antes/después, rollback, reservas concurrentes, cancelación y despacho |
| 5 | Matriz por AC con esperado, observado, módulo, test, PR, SHA y artefacto real |
| 6 | Whitelist, schema, pertenencia de IDs, pruebas maliciosas, timeout/fallback y origen de respuestas |
| 8 | Comandos reproducibles y salidas de ejecución; «pendiente» cuando no se ejecutó |
| 10 | Clon limpio, mismo SHA probado/tagueado, PDF con brechas y contribuciones verificadas |

No fusionar PR sujeto a revisión pendiente. No suplantar integrantes ni atribuirles commits/revisiones. Sin fuente oficial o aprobación, preparar documentación y verificadores de SPEC; no implementar funcionalidades.

## Motivo, evidencia y responsabilidad por regla

Corrección documental D-21 según anexo oficial `01_constitution.md`; preparada por Codex el 2026-10-14. Fuentes oficiales recibidas en las ramas del equipo; cotejo/revisión humana siguen pendientes. SHA revisado y fecha UTC de aprobación: **PENDIENTES**.

| ID | Regla | Motivo | Evidencia comprobable exigida | Responsable propuesto |
|---|---|---|---|---|
| P-01 | Fuente y SPEC aprobada antes de funcionalidad | Evitar implementar supuestos como requisitos | Fuente/página, revisión humana por SPEC, UTC/SHA y PR | Carolina; coordinación Conde José |
| P-02 | Aislamiento y autorización de servidor | Evitar lecturas/escrituras ajenas | ≥2 lecturas y ≥2 escrituras cruzadas denegadas con snapshots y respuestas sanitizadas | Vega José; implementación Edier/Conde José |
| P-03 | Disponible=físico−reservado; físico≥reservado≥0 | Evitar sobreasignación | PostgreSQL real: concurrencia, rollback y stock final independiente | Edier |
| P-04 | Transiciones oficiales y rechazo sin efectos de negocio | Mantener estado/stock/evento consistentes | Grafo RF04, cancelación por dueño, despacho Bodega y entrega Coordinador; eventos atómicos | Edier; Carolina coteja |
| P-05 | Traza requisito→AC→código→prueba→evidencia | Demostrar cumplimiento verificable | Matriz y enlaces reales a tarea, test, PR, SHA; gaps explícitos | Carolina |
| P-06 | IA no privilegiada y salida no confiable | Evitar decisiones automáticas o fuga | Whitelist, schema, IDs, timeout/fallback, aprobación sin cambios de stock/prioridad | Vega José |
| P-07 | Datos ficticios, hashes y secretos fuera de Git | Proteger identidad y evaluación | Ocho cuentas seed; inspección de hash/respuestas/logs y detección de secretos | Vega José y Conde José |
| P-08 | Solo declarar ejecuciones reales | Evitar evidencia inventada | Comando, entorno, UTC, SHA, esperado/observado y salida; no ejecutado visible | Todos; calidad Vega José |
| P-09 | Revisión independiente de cambios | Detectar defectos y acreditar colaboración | Cuenta real del revisor, comentario crítico, SHA y decisión; sin autoaprobación | Todos; coordinación Conde José |
| P-10 | Entrega reproducible y aceptación honesta | Permitir evaluar mismo artefacto | Clon limpio RNF04, tags/SHA reales, PDF idéntico y brechas; RNF03/05/06 comprobados | Conde José; calidad Vega José |

Los responsables orientan tareas; no acreditan contribuciones realizadas. CL-16/17/18 requieren docente; sin respuesta no ampliar permisos de inventario/auditoría como decisión confirmada.

## Criterio de excepción

Registrar cada excepción con ID, regla afectada, decisión propuesta, justificación, impacto RF/RNF/AC y riesgos, mitigación, responsable, aprobador humano independiente, UTC/SHA/enlace, caducidad y revisión de cierre. Sin aprobación sigue propuesta; al caducar se reevalúa. Ninguna excepción puede contradecir un valor explícito del examen, inventar evidencia, sustituir revisión humana o permitir implementar una SPEC no aprobada. Excepciones autorizadas: **ninguna registrada**.
