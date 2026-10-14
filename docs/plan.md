# Plan de Arquitectura y Desarrollo — NexoEntrega 360

* **Procedencia:** propuesta de Carolina; alineación por Codex a solicitud de Conde José. Responsables del equipo no equivalen a autoría personal acreditada.
* **Stack Principal:** Laravel (PHP) + PostgreSQL + JavaScript / TailwindCSS
* **Estrategia:** Spec Driven Development (SDD) con trazabilidad completa de requisitos.

## 1. Visión General de Arquitectura
Monolito modular MVC con aislamiento multitenant estricto por organization_id y alcance global en el ORM Eloquent.

## 2. Componentes Clave
- **Seguridad y Acceso:** Autenticación de usuarios y aislamiento multitenant especificados en ADR-003.
- **Garantía de Transacciones:** Transacciones atómicas en PostgreSQL para control de stock y cabeceras Idempotency-Key contra peticiones duplicadas.
- **Servicio de Asistencia IA:** Integración desacoplada documentada en ADR-004.

## 3. Base de Datos, Migraciones y Seeding
- **Migraciones:** `organizations`, `users`, `inventory_items`, `requests`, `request_lines`, `audit_events`, `idempotency_records`, `ai_summaries`, `ai_attempts`, según `docs/architecture/datos.md`. No usar nombres alternativos kits/request_items/audit_logs.
- **Seeders:** Datos sembrados para usuarios base de ORG-A y ORG-B con roles predeterminados (Solicitante, Coordinador, Bodega, Auditor).

## 4. Estrategia de Pruebas, CI y Calidad
- Pruebas unitarias y de integración según docs/testing/estrategia-pruebas.md.
- Integración Continua (CI) mediante GitHub Actions para validación automática de pruebas y cotejo de especificaciones.
- Verificación del recorrido completo (baseline-v1) planificada para la fase final del día 3.

## 5. Documentación y Flujo de Revisión
- Documentación técnica mantenida mediante especificaciones SDD en Markdown.
- Revisiones cruzadas obligatorias mediante Pull Requests con asignación de revisor humano antes de integrar a la rama principal.

## 6. Dependencias, módulos y término

Orden: entorno reproducible TSK-10 → migraciones TSK-07 → seed TSK-08 → identidad TSK-01. Auditoría TSK-11 se integra antes de solicitudes TSK-02 y transiciones/inventario TSK-03. Consultas TSK-04 e IA TSK-05 dependen de identidad, solicitudes y auditoría. Frontend TSK-06 integra cada slice por rol, con CI TSK-09 desde la primera funcionalidad y validación TSK-12 antes de cierre. Documentación/entrega TSK-13 depende de pruebas y revisión TSK-14. Gates G1/G2 obligatorios; ninguna funcionalidad se inicia sin SPEC aprobada y diseño revisado.

Módulos: `app/Modules/Identity`, `Requests`, `Inventory`, `Idempotency`, `Queries`, `Audit`, `AI`, `Operations` según ADR-001. Archivos/tests futuros, dependencias y responsables están en `docs/tasks.md`. No son archivos de producto implementados.

## 7. Contratos y sesión comunes

ADR-003 propone Bcrypt costo 12 y sesión revocable de 120min con cookie `nexo_session`, HttpOnly, SameSite=Lax y Secure bajo HTTPS. Bootstrap `/api/auth/csrf` prepara el token. Todas las escrituras, login/logout e IA incluidos, declaran `X-XSRF-TOKEN` y 419. Revisar precedencia de middleware en ADR-003: casos de 401 se prueban con CSRF válido; casos CSRF inválido se prueban separadamente. No presentar middleware de Laravel como implementado.

Auditoría: 401 sin identidad; 404 para ID inexistente/interorganización; 403 para rol/dueño prohibido sobre recurso de su organización. Listados filtran alcance antes de conteo/paginación. CL-16/17/18 habilitan lecturas, sin escritura adicional.

## 8. Evidencia

Cerrar tarea requiere comando, entorno, UTC, SHA, esperado/observado, efectos de base, reporte y revisión independiente. CI instala lockfiles, lint/tipos cuando aplique, SPEC sin regenerar antes del check, unidad, PostgreSQL, contrato, build, dependencias y secretos; IA por fixtures sin claves/pago. Carga/E2E/clon limpio se ejecutan sobre candidato final. La revisión y los resultados reales permanecen pendientes.
