# ADR-001 Estructura y límites: Laravel y PostgreSQL

- Estado: **propuesto; revisión G2 pendiente**. No autoriza implementación mientras G1 esté pendiente.
- Fecha y versión: 2026-10-14, v0.2 propuesta; actualización técnica por R-E02, sin aprobación humana nueva.
- Elaboración: Codex a solicitud de Conde José; no acredita trabajo o aprobación humana.
- Fuentes: examen oficial §§2–6, 8–12; SPEC-01 a SPEC-05 0.3-draft; ADR-004 y contrato IA de la rama `docs/g2-vega-ia`, commit `a13aa17`.

## Contexto y restricciones

Dos organizaciones, cuatro roles y ocho cuentas ficticias. El sistema debe mantener consistencia de stock, reservas, estados, resultado idempotente y auditoría; demostrar aislamiento y transacciones con base real. La IA es opcional para operar, sin herramientas privilegiadas, y su aprobación no ejecuta operaciones de negocio. El usuario solicita Laravel + PostgreSQL y monolito modular. Esto sustituye la propuesta tecnológica inicial TypeScript/Express para este diseño; no se presenta como exigencia del docente.

## Alternativas consideradas

| Alternativa | Beneficio | Coste / restricción |
|---|---|---|
| Laravel + PostgreSQL, monolito modular | Un despliegue, transacción local compartida, autenticación/validación del framework | Requiere disciplina de dependencias y pruebas PostgreSQL; revisar competencia PHP del equipo |
| React/Express/TypeScript + PostgreSQL | Coincide con propuesta inicial, tipos compartidos | Dos builds y mayor coordinación de sesión/frontend; alternativa válida si el equipo la prefiere |
| Microservicios | Despliegue independiente | Consistencia distribuida y complejidad innecesaria para examen y equipo |
| Laravel + MySQL | Compatible con Laravel y transacciones | Requiere adaptar oráculos/entorno; se elige un solo motor real para CI y demo |

## Criterios de elección

Consistencia transaccional verificable, mínima complejidad operativa, límites claros, autorización centralizada, integración IA reemplazable y arranque reproducible RNF04. Evitar servicios pagados obligatorios y funcionalidades fuera de alcance.

## Decisión y justificación

Monolito modular Laravel, PostgreSQL único y UI Blade del mismo origen, sin SPA adicional. La implementación provisional conserva esa elección; las versiones reales se registran para corregir R-E02, sin presentar la fijación tardía como revisión previa.

| Componente | Versión reproducible | Fuente comprobable |
|---|---|---|
| PHP | 8.5.0, imagen php:8.5.0-cli-bookworm; requisito ~8.5.0 | apps/nexo/Dockerfile, composer.json y composer.lock |
| Laravel | framework 12.70.0, familia 12 | apps/nexo/composer.lock |
| PostgreSQL | 17.6, imagen postgres:17.6-bookworm | compose.yaml |
| Composer | 2.8.12 | apps/nexo/Dockerfile |
| Node | 22.22.2 | .github/workflows/aplicacion-provisional.yml |
| Playwright | 1.56.1 | package-lock.json |

PHP ^8.2 era más permisivo que las dependencias del lockfile (requieren al menos 8.4.1). El requisito pasa a ~8.5.0 para coincidir con el runtime fijado. Regenerar metadatos del lock sin actualizar paquetes y comprobar composer validate/check-platform-reqs; el objetivo no es actualizar dependencias silenciosamente.

| Módulo previsto | Responsabilidad y dueño de datos | Dependencias permitidas |
|---|---|---|
| Identity | Sesiones, hash, identidad/rol/org; ocho cuentas ficticias | Infraestructura de persistencia; ADR-003 |
| Requests | Cabecera, líneas y validaciones; crear/editar/enviar borrador propio | Identity, catálogo Inventory, Audit |
| Inventory | Existencia física/reservada, reserva, despacho y liberación | Requests, Identity, Idempotency, Audit; ADR-002 |
| Queries | Listados, filtros, tablero, inventario e historial autorizado | Lecturas con alcance del servidor; sin mutación |
| Audit | Eventos append-only de transición/aprobación | Transacción compartida; sin API de edición/borrado |
| Idempotency | Ámbito usuario/org/operación, hash y resultado ≥24h | Autorización previa y transacción Inventory |
| AI | Selección, whitelist, adaptador, validación, fallback y aprobación | Identity, lectura Requests, Audit; ADR-004 |
| Operations | Health, readiness y logs estructurados sanitizados | Readiness comprueba PostgreSQL; no depende del proveedor IA |

Ubicación real del paquete provisional: apps/nexo/app/Modules/<Module> con clases de aplicación y controladores por módulo; apps/nexo/routes/web.php para la API con sesiones/CSRF y vistas Blade del mismo origen. Las capas Application/Domain/Infrastructure/Http y routes/api.php del diseño inicial no se materializaron; esta versión registra la diferencia para revisión, sin fingir que se implementaron. Queries usa proyecciones; AI no obtiene una interfaz de mutación de solicitudes/inventario. Solo introducir interfaces en fronteras que deben sustituirse/probarse, como proveedor IA.

Una única transacción PostgreSQL confirma estado, inventario, auditoría y resultado idempotente. Bloquear solicitud y filas de stock en orden estable; ADR-002 debe concretar aislamiento, deadlocks, hash y retención. No invocar al proveedor IA dentro de una transacción de inventario. Aprobar resumen usa su propia transacción de resumen+evento sin escribir stock.

## Fronteras de confianza y alcance

Navegador y observaciones no confiables; API resuelve identidad y filtra organización antes de exponer datos. PostgreSQL no es accesible al navegador. Credencial del proveedor solo en backend, sin nombres/secretos/datos ajenos en payload. Respuesta IA se valida y se presenta escapada. Sesión revocable/cookie y controles CSRF son provisionales hasta ADR-003; contrato IA conserva su esquema original.

Fuera de alcance: usuarios masivos, recuperación de contraseña, GPS/rutas, devoluciones, ajustes de stock por API y decisiones automáticas de IA. El coordinador reporta respuesta afirmativa de la profesora a CL-16/17/18: Coordinador también lee inventario e historial de su organización; Solicitante lee historial y tablero de sus propias solicitudes sin inventario. Bodega no recibe acceso a auditoría. Fecha/canal y evidencia original pendientes; la decisión se registra como reportada y las SPEC afectadas requieren nueva revisión.

## Consecuencias y riesgos

Un único despliegue simplifica evaluación; el mismo motor de base se usa en demo y pruebas. Una caída del proceso afecta toda la aplicación, aceptable para este alcance y documentada en runbook futuro. El acoplamiento de modelos y escrituras cruzadas se controla mediante revisión de módulos. Las transacciones no aseguran por sí solas concurrencia/idempotencia: deben ejecutarse los oráculos. G1 pendiente y permisos aún abiertos impiden considerar final el diseño.

## RF y AC afectados

RF01–RF12, RNF01–RNF06; todos los AC de SPEC-01–05 según matriz. Contrato `contracts/openapi.yaml`; diagramas `docs/architecture/contexto.md` y `componentes.md`. ADR-002/003 aún pendientes; ADR-004 existente no se aprueba por referencia.

## Verificación de la decisión

Pruebas previstas, **no ejecutadas**: aislamiento 2 lecturas/2 escrituras; reserva parcial rechazada y rollback real; dos reservas competidoras PostgreSQL; replay/conflicto/TTL; evento atómico; fallo IA sin impedir transiciones; clon limpio ≤15min (descargas medidas aparte); carga RNF03 y UI RNF05. Revisar dependencias entre módulos y permisos por ruta en G2.

Apoyo técnico: [transacciones Laravel](https://laravel.com/framework/docs/database/config), [bloqueos pesimistas](https://laravel.com/framework/docs/13.x/queries). Consultados 2026-10-14; versión del producto aún por fijar. La decisión sigue siendo del equipo.

## Autor y revisión humana

Revisor independiente, fecha UTC, SHA y decisión: **PENDIENTES**. Edier revisa límites/datos/transacción y Vega fronteras IA/seguridad, como propuestas de revisión; no se registra participación realizada.
