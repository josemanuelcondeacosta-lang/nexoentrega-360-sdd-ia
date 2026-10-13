# Edier — inventario, transacciones e idempotencia en G1

Estado: tarea propuesta, ejecución no acreditada. En esta etapa revisas especificaciones y diseñas escenarios; no implementas backend ni pruebas de producto.

1. Sigue [preparación en VS Code](preparacion-G1.md), usando rama `docs/g1-edier-review`.
2. Lee SPEC-03 completa y sus vínculos con SPEC-02/04, permisos propuestos y CL-04/09/11. Contrasta con texto oficial cuando llegue.
3. Crea `docs/reviews/aportes/edier-G1.md` desde la plantilla. Añade una tabla de escenarios: AC, datos iniciales, actor, acción, respuesta esperada, estado/stock/reservas/eventos finales y fuente. Incluye reserva exacta, línea insuficiente, dos reservas competidoras, cancelación ENVIADA/RESERVADA, doble despacho, entrega, fallo antes del commit, replay/conflicto/expiración de clave. Son casos previstos: observado = «no ejecutado».
4. Comprueba que disponible=físico−reservado, que ningún rechazo de negocio deje cambios parciales y que la auditoría confirme o revierta con el cambio. Registra cualquier ambigüedad como pregunta, no como decisión del cliente.
5. En PR #1 publica **Comment** desde tu cuenta: SHA, UTC, AC revisados y observaciones concretas. Publica tu documento en PR con base `docs/g1-specifications`; solicita revisión de Carolina.
6. Revisa el futuro PR documental de Vega: que los casos adversariales tengan resultados comprobables, separación entre esperado/observado y ninguna prueba de producto declarada ejecutada. Registra número/SHA cuando exista.

Entregable: escenarios propios y revisión ajena real. Backend, migraciones y pruebas concurrentes reales de PostgreSQL quedan condicionados a SPEC aprobada y diseño revisado.
