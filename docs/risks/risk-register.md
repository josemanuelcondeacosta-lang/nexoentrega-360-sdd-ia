# Registro inicial de riesgos — BORRADOR

| ID | Riesgo | Impacto | Probabilidad | Control propuesto | Responsable | Evidencia de control | Residual |
|---|---|---|---|---|---|---|---|
| R-01 | Reserva duplicada por concurrencia | Alta | Alta | Bloqueo/transacción y test con dos usuarios simultáneos | Por asignar | Pendiente | Por evaluar |
| R-02 | Acceso interorganización | Alta | Alta | Filtro de servidor y 2 lecturas + 2 escrituras cruzadas | Por asignar | Pendiente | Por evaluar |
| R-03 | Reutilización indebida de idempotencia | Alta | Media | Scope por usuario/organización/operación/hash y tests | Por asignar | Pendiente | Por evaluar |
| R-04 | Respuesta IA con ID ajeno/inyección | Alta | Media | Schema estricto, whitelist IDs, texto no ejecutable | Por asignar | Pendiente | Por evaluar |
| R-05 | Fallo de proveedor de IA | Media | Alta | Timeout 5 s, resumen alternativo determinista | Por asignar | Pendiente | Por evaluar |
| R-06 | Entrega irreproducible | Alta | Media | Lockfiles, migraciones y clon limpio verificado | Por asignar | Pendiente | Por evaluar |

| R-07 | Ausencia de enunciado y anexos | Alta | Alta | Solicitar fuente; marcar todo cierre de RF/RNF pendiente | Carolina (propuesto) | Fuente no recibida | Alto hasta cotejo |
| R-08 | Implementar antes de revisión humana | Alta | Media | Gate con SHA y autor/revisor distintos; no merge automático | Equipo (propuesto) | G1 no aprobado | Alto hasta revisión |
| R-09 | Métricas o contribuciones ficticias | Alta | Media | Evidencia real, observado separado de esperado; no suplantar identidades | Equipo (propuesto) | Matriz preparada sin resultados | Por evaluar |
| R-10 | Accesibilidad y rendimiento insuficientes | Media | Media | Teclado, 390/1366px y carga 20VU/60s con 2.000 solicitudes | José Vega (propuesto) | Pruebas no ejecutadas | Por evaluar |
| R-11 | Auditoría y negocio divergen | Alta | Media | Misma transacción y fallo inducido antes del commit | Edier (propuesto) | AC-03-09 previsto | Por evaluar |

Los responsables son propuestas de coordinación. Ningún control de aplicación se ha verificado aún ni se atribuye trabajo real a esas personas.
