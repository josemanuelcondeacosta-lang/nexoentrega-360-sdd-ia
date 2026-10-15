# Tareas G2 alineadas — plan y dependencias

Origen: tabla de Carolina en `7637056`; alineación preparada por Codex a solicitud de Conde José. Esta tabla conserva el plan original y los nombres de pruebas previstos; los archivos ejecutados pueden tener otros nombres. Los aportes de producto existen en PR #12–15 y sus resultados se registran en `evidence/runtime`. Existencia de código o pruebas no significa tarea aceptada ni revisión previa cumplida. No sustituye revisión humana ni la matriz por AC.

| ID | RF/RNF y AC | Resultado / módulos y archivos previstos | Dependencias | Prueba y condición de término | Responsable propuesto |
|---|---|---|---|---|---|
| TSK-01 | RF01/02, RNF01; AC-01-01..14 | Identity: sesión, login/logout, rol/org y CSRF; `app/Modules/Identity` | TSK-07/08; SPEC-01 y ADR-003 revisados | `tests/Feature/AuthTest.php`: sesión persistida, revocación, 401/403/404, CSRF negativo y revisión ajena | Edier; Carolina aporta/revisa tests |
| TSK-02 | RF03/04/09; AC-02-01..11 | Requests: crear/editar/enviar; `app/Modules/Requests` | TSK-01/08/11; SPEC-02 revisada | `tests/Feature/RequestsTest.php`: límites, snapshots cabecera/líneas/evento, rechazo sin efectos | Edier; Carolina tests de aceptación |
| TSK-03 | RF04..07/09, RNF02; AC-03-01..17 | Reserva, despacho, entrega, cancelación e idempotencia; Inventory/Idempotency | TSK-01/02/11; SPEC-03 y ADR-002 revisados | `tests/Concurrency/ReserveTest.php` y `tests/Feature/TransitionsTest.php`: PostgreSQL real, rollback, replay/conflicto/TTL, stock final | Edier |
| TSK-04 | RF08/09, RNF03; AC-04-01..14 | Consultas, filtros, inventario, tablero e historial; Queries | TSK-01/02/11; SPEC-04 revisada | `tests/Feature/QueriesTest.php`: comparar base, 403 misma organización/otro dueño, 404 inexistente/interorganización, alcance antes de conteos | Edier; José integra UI |
| TSK-05 | RF10/11/12, RNF01/06; AC-05-01..17 | Adaptador, schema, fallback y aprobación; AI | TSK-01/02/11; SPEC-05 y ADR-004 revisados | `tests/Feature/AiTest.php`: siete fixtures, IDs, CSRF, timeout, origen y aprobación sin efectos de negocio | Vega José |
| TSK-06 | RF01..12, RNF05; AC-01-13/02-11/03-16/04-14 | Blade, formularios y flujo integrado de cuatro roles; vistas/JS | TSK-01..05 por slice y contrato revisado | `tests/e2e/roles.spec.ts`: flujo completo/rechazo, teclado, etiquetas, 390/1366px sin desbordamiento | Conde José |
| TSK-07 | RF02..09, RNF02/04; AC-03-09/04-12 | Migraciones de nueve tablas de dominio y restricciones según datos.md | TSK-10; esquema/ADR-002 revisados | `tests/Feature/SchemaTest.php`: migración repetible, FK/CHECK y stock imposible rechazado | Edier |
| TSK-08 | RF01/03, RNF04; AC-01-05/04-12 | Seed ficticio pequeño y carga independiente | TSK-07 | `tests/Feature/SeedTest.php`: ocho cuentas hash, dos org, datos/oráculos; 2.000 solicitudes para carga separada | Edier; Carolina verifica oráculos |
| TSK-09 | RNF01/02/04/06, §9; AC-01-12/05-14 | `.github/workflows/ci.yml` y calidad | TSK-10 para base; TSK-01..05 para pipeline completo | Lockfiles, lint/tipos cuando aplique, SPEC sin regenerar antes, unidad, PostgreSQL, contrato, build, dependencias/secretos; ejecución real verde y omisiones visibles | Vega José |
| TSK-10 | RNF04; AC-04-12 | Compose, versiones, lockfiles y comando de arranque | Diseño G2 revisado; funcionalidad solo con SPEC aprobada | Clon limpio ≤15min, descarga inicial medida aparte; configuración reproducible | Conde José; Edier revisa DB |
| TSK-11 | RF09; AC-04-03/04-04/04-08 y AC-03-09 | Audit: evento transaccional y API inmutable | TSK-07; SPEC-04 revisada | `tests/Feature/AuditTest.php`: actor/org/recurso/UTC/correlationId, fallo inducido revierte negocio, sin editar/borrar por API | Edier |
| TSK-12 | RNF03/05/06; AC-04-09/04-13/04-14 | Carga, móvil/teclado, health/ready y logs; Operations | TSK-04/06/08/10; SPEC-04 revisada | k6 20VU, 5s calentamiento+60s medidos, 2.000 solicitudes, p95≤700ms y errores<1%; revisión manual y logs sanitizados | Vega José; Conde José UI |
| TSK-13 | RNF04/06, §12/13; AC-04-12 | Runbook, rollback, trazabilidad, aceptación y PDF final | TSK-09/12/14; versión probada | Clon limpio, mismo SHA probado/tagueado, contribuciones y brechas reales, mismo PDF; no PDF en esta etapa | Conde José; Carolina revisión documental |
| TSK-14 | Todos RF/RNF y AC afectados | Revisión G1/G2 y cada PR, con autor/revisor distintos | Artefactos preparados y hallazgos atendidos | Inspección personal, comandos reales, UTC/SHA/enlaces y decisión; cambios nuevos renuevan revisión | Todos |

## Reglas de ejecución y evidencia

- Estado de cierre de las tareas: pendiente de la verificación completa por criterio y las revisiones correspondientes. Consultar los PR reales y la evidencia del corte candidato; nunca interpretar el plan como resultado ejecutado.
- Toda funcionalidad requiere su SPEC aprobada y diseño revisado aunque la fila no repita el gate. TSK-14 se aplica antes de implementar y antes de integrar; no es un paso aplazable hasta entrega.
- Auditoría TSK-11 debe estar disponible desde la primera transición. La base TSK-07 y seed TSK-08 preceden autenticación y pruebas de dominio persistido.
- Para cerrar: comando, entorno, UTC, SHA, esperado/observado, snapshots de base cuando corresponda, reporte y revisión ajena. Tests previstos no son resultados ejecutados.
- Todos aportan código/pruebas propios y revisión ajena: Carolina aceptación/trazabilidad; Edier backend/transacciones; Conde José frontend/integración; Vega IA/CI/calidad. Esta asignación no acredita contribución realizada.
- Fuente de vínculos completos por AC: `coverage-matrix.md` y `evidence/traceability.csv`.
