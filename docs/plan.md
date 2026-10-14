# Plan de Arquitectura y Estructura — NexoEntrega 360

* **Autores:** Equipo de Desarrollo (Carolina Cantillo, Edier Castro, José Conde, José Vega)
* **Stack Principal:** Laravel (PHP) + PostgreSQL + TailwindCSS / JS
* **Enfoque de Trabajo:** Spec Driven Development (SDD) con trazabilidad de requisitos.

## 1. Visión General de Arquitectura
El proyecto está concebido como un monolito modular bajo patrón MVC, priorizando la claridad del dominio, la separación por multicapas y el aislamiento multitenant a través del identificador organization_id.

## 2. Componentes Fundamentales
- **Seguridad y Acceso:** Autenticación de usuarios y aislamiento de organizaciones detallados en el ADR-003.
- **Garantía de Transacciones:** Manejo de transacciones atómicas en PostgreSQL para control de stock e integración de cabeceras Idempotency-Key para evitar peticiones duplicadas.
- **Servicio de Asistencia IA:** Integración desacoplada documentada en ADR-004 con mecanismos de respaldo (*fallback*) y validación de respuestas.

## 3. Plan de Calidad y Pruebas
- Desarrollo e integración de pruebas unitarias y funcionales siguiendo la guía descrita en docs/testing/estrategia-pruebas.md.
- Verificación del flujo completo del sistema (*baseline-v1*) planificada para la fase final del día 3.
