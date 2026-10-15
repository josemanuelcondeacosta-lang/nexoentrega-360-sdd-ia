# Registro de riesgos — candidato pendiente de aceptación

| ID | Riesgo | Impacto | Probabilidad | Control propuesto | Responsable | Evidencia de control | Residual |
|---|---|---|---|---|---|---|---|
| R-01 | Reserva duplicada por concurrencia | Alta | Alta | Bloqueo/transacción y test con dos usuarios simultáneos | Por asignar | Pendiente | Por evaluar |
| R-02 | Acceso interorganización | Alta | Alta | Filtro de servidor y 2 lecturas + 2 escrituras cruzadas | Por asignar | Pendiente | Por evaluar |
| R-03 | Reutilización indebida de idempotencia | Alta | Media | Scope por usuario/organización/operación/hash y tests | Por asignar | Pendiente | Por evaluar |
| R-04 | Respuesta IA con ID ajeno/inyección | Alta | Media | Schema estricto, whitelist IDs, texto no ejecutable | Por asignar | Pendiente | Por evaluar |
| R-05 | Fallo de proveedor de IA | Media | Alta | Timeout 5 s, resumen alternativo determinista | Por asignar | Pendiente | Por evaluar |
| R-06 | Entrega irreproducible | Alta | Media | Lockfiles, migraciones y clon limpio verificado | Por asignar | Pendiente | Por evaluar |
| R-07 | Ausencia de enunciado y anexos | Alta | Baja (fuente recibida 2026-10-14) | Originales en `docs/examen-oficial/` con SHA-256; cotejo en `cotejo-oficial-G1.md` | Carolina (propuesto) | Inventario y cotejo 0.3; revisión humana pendiente | Bajo; vigilar versiones nuevas del docente |
| R-08 | Implementar antes de revisión humana | Alta | Media | Gate con SHA y autor/revisor distintos; no merge automático | Equipo (propuesto) | G1 no aprobado | Alto hasta revisión |
| R-09 | Métricas o contribuciones ficticias | Alta | Media | Evidencia real, observado separado de esperado; no suplantar identidades | Equipo (propuesto) | Matriz preparada sin resultados | Por evaluar |
| R-10 | Accesibilidad y rendimiento insuficientes | Media | Media | Teclado, 390/1366px y carga 20VU/60s con 2.000 solicitudes | José Vega (propuesto) | Pruebas no ejecutadas | Por evaluar |
| R-11 | Auditoría y negocio divergen | Alta | Media | Misma transacción y fallo inducido antes del commit | Edier (propuesto) | AC-03-09 previsto | Por evaluar |
| R-12 | Desalineación de permisos con las aclaraciones CL-16/17/18 | Media | Media | Aplicar el alcance de la respuesta docente registrada en clarification-log.md; cotejar SPEC, contrato y código | Conde José (propuesto) | Respuesta afirmativa y captura aportadas por el coordinador; conservar sus límites | Pendiente de revisión del paquete actualizado |

Las filas iniciales conservan controles previstos, no resultados del candidato actual. Los responsables siguientes son propuestas de coordinación, no atribuciones de trabajo realizado.

## Actualización de controles en la rama candidata

| Riesgos | Responsable propuesto | Evidencia disponible y límite |
|---|---|---|
| R-01/R-03/R-11 | Edier | Pruebas PostgreSQL de reserva, idempotencia, TTL y rollback por fallo de auditoría integradas del PR #13; las carreras HTTP se registran por separado al ejecutarse |
| R-02/R-04/R-05 | Vega | Suite de aislamiento, siete fixtures IA, validación estricta, corte real de 5s contra un servidor local sin respuesta y regresión de replay con contexto distinto; no acredita una invocación LLM nueva |
| R-06 | Conde José | Lockfile corregido para PHP 8.5 y construcción Docker ejecutada; falta documentar clon limpio cronometrado y recuperación antes de cerrar |
| R-08/R-09 | Equipo | Gates por alcance/SHA y enlaces de decisiones reales; persiste el incumplimiento de secuencia por implementación adelantada. Una revisión posterior no lo borra |
| R-10 | Vega y Conde José | PR #15 aporta carga y sanitización; comprobar resultados del corte candidato y registrar aceptación manual pendiente |

No reducir el residual a cero por una suite verde: quedan criterios sin verificación completa y decisiones humanas pendientes.
