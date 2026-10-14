# Control y Asignación de Tareas (G2)

| ID | Cobertura RF / AC | Módulo / Funcionalidad | Dependencias | Pruebas | Responsable | Estado | Condición de Término | PR |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **TSK-01** | RF01, RF02, RNF01, AC-01-01..13 | Autenticación, Sesiones y Middleware Tenant (ADR-003) | Ninguna | 	est_auth.php | Carolina Cantillo | Pendiente | Login/Logout y aislamiento por Org validados | PR G2 |
| **TSK-02** | RF03, RF04, SPEC-02, AC-02-01..12 | Flujo de Solicitudes y Cancelación por Propietario | TSK-01 | 	est_solicitudes.php | Carolina Cantillo | Pendiente | Peticiones BORRADOR→ENVIADA y cancelación probadas | PR G2 |
| **TSK-03** | RF05, RF06, SPEC-03, AC-03-01..15 | Control de Inventario, Transacciones e Idempotencia | TSK-01 | 	est_inventario.php | Edier Castro | Pendiente | Reserva atómica de stock con Idempotency-Key | PR G2 |
| **TSK-04** | RF08, RF09, RNF03, AC-04-01..10 | Consultas, Filtro por Organización y Auditoría | TSK-01, TSK-02 | 	est_auditoria.php | José Conde | Pendiente | Logs de auditoría y consultas cross-tenant con 404 | PR G2 |
| **TSK-05** | RF11, RNF05, SPEC-05, AC-05-01..18 | Módulo de Asistente de IA y Respuestas de Respaldo | TSK-01 | 	est_ia_fallback.php | José Vega | Pendiente | Respuestas sanitizadas y fallback activo | PR G2 |