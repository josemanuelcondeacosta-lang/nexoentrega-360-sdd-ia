# Plan de Arquitectura y Desarrollo — NexoEntrega 360

* **Autores:** Equipo de Desarrollo (Carolina Cantillo, Edier Castro, José Conde, José Vega)
* **Stack Principal:** Laravel (PHP) + PostgreSQL + JavaScript / TailwindCSS
* **Estrategia:** Spec Driven Development (SDD) con trazabilidad completa de requisitos.

## 1. Visión General de Arquitectura
Monolito modular MVC con aislamiento multitenant estricto por organization_id y alcance global en el ORM.

## 2. Componentes Clave
- **Seguridad y Acceso:** Autenticación de usuarios y aislamiento multitenant especificados en ADR-003.
- **Garantía de Transacciones:** Transacciones atómicas en PostgreSQL para control de stock y cabeceras Idempotency-Key contra peticiones duplicadas.
- **Asistente de IA:** Integración desacoplada documentada en ADR-004.

## 3. Plan de Pruebas y Operación
- Pruebas unitarias y de integración según docs/testing/estrategia-pruebas.md.
- Verificación del recorrido completo (baseline-v1) planificada para la fase final del día 3.