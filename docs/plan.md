# Plan de Arquitectura y Desarrollo — NexoEntrega 360

* **Autores:** Equipo de Desarrollo (Carolina Cantillo, Edier Castro, José Conde, José Vega)
* **Stack Principal:** Laravel (PHP) + PostgreSQL + JavaScript / TailwindCSS
* **Estrategia:** Spec Driven Development (SDD) con trazabilidad completa de requisitos.

## 1. Visión General de Arquitectura
Monolito modular MVC con aislamiento multitenant estricto por organization_id y alcance global en el ORM Eloquent.

## 2. Componentes Clave
- **Seguridad y Acceso:** Autenticación de usuarios y aislamiento multitenant especificados en ADR-003.
- **Garantía de Transacciones:** Transacciones atómicas en PostgreSQL para control de stock y cabeceras Idempotency-Key contra peticiones duplicadas.
- **Servicio de Asistencia IA:** Integración desacoplada documentada en ADR-004.

## 3. Base de Datos, Migraciones y Seeding
- **Migraciones:** Creación de tablas estructuradas para users, organizations, kits, requests, request_items y audit_logs.
- **Seeders:** Datos sembrados para usuarios base de ORG-A y ORG-B con roles predeterminados (Solicitante, Coordinador, Bodega, Auditor).

## 4. Estrategia de Pruebas, CI y Calidad
- Pruebas unitarias y de integración según docs/testing/estrategia-pruebas.md.
- Integración Continua (CI) mediante GitHub Actions para validación automática de pruebas y cotejo de especificaciones.
- Verificación del recorrido completo (baseline-v1) planificada para la fase final del día 3.

## 5. Documentación y Flujo de Revisión
- Documentación técnica mantenida mediante especificaciones SDD en Markdown.
- Revisiones cruzadas obligatorias mediante Pull Requests con asignación de revisor humano antes de integrar a la rama principal.