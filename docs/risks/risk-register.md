# Registro inicial de riesgos — BORRADOR

| ID | Riesgo | Impacto | Probabilidad | Control propuesto | Responsable | Evidencia de control | Residual |
|---|---|---|---|---|---|---|---|
| R-01 | Reserva duplicada por concurrencia | Alta | Alta | Bloqueo/transacción y test con dos usuarios simultáneos | Por asignar | Pendiente | Por evaluar |
| R-02 | Acceso interorganización | Alta | Alta | Filtro de servidor y 2 lecturas + 2 escrituras cruzadas | Por asignar | Pendiente | Por evaluar |
| R-03 | Reutilización indebida de idempotencia | Alta | Media | Scope por usuario/organización/operación/hash y tests | Por asignar | Pendiente | Por evaluar |
| R-04 | Respuesta IA con ID ajeno/inyección | Alta | Media | Schema estricto, whitelist IDs, texto no ejecutable | Por asignar | Pendiente | Por evaluar |
| R-05 | Fallo de proveedor de IA | Media | Alta | Timeout 5 s, resumen alternativo determinista | Por asignar | Pendiente | Por evaluar |
| R-06 | Entrega irreproducible | Alta | Media | Lockfiles, migraciones y clon limpio verificado | Por asignar | Pendiente | Por evaluar |
