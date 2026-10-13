# Constitution — borrador v0.1

**Estado: pendiente de revisión humana G1.**

1. **Fuente verificable:** cada funcionalidad se aprueba mediante SPEC versionada antes de modificar código. Comprobación: fecha/commit G1 y PR asociado.
2. **Aislamiento:** organización y rol provienen de autenticación; solicitudes ajenas dan 404; pruebas de lecturas y escrituras cruzadas.
3. **Integridad del inventario:** disponible = físico − reservado; nunca negativo; transacción atómica y pruebas concurrentes sobre base real.
4. **Estados controlados:** únicamente transiciones RF04, y rechazos 409 sin efectos persistidos.
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
