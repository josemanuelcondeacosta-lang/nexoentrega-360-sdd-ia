# Frontend e integración provisional con PostgreSQL

Permite entrar/salir con ocho cuentas ficticias, crear/editar/enviar solicitudes, reservar, despachar, registrar entrega, cancelar y consultar listados, inventario e historial con límites comprobados por servidor. Incluye sesiones y CSRF, reserva transaccional e idempotencia, auditoría, interfaz responsive y asistente visible de fallback sin proveedor LLM real.

Implementación adelantada por instrucción expresa del coordinador. No convierte G1/G2 en aprobados. No fusionar ni presentar como entrega final sin revisión independiente y cierre de brechas.

Verificación: consultar docs/evidence/runtime/verification.md y reportes adjuntos al repositorio. Ejecución reproducible: docs/delivery/runbook-provisional.md. Código y pruebas generados/ejecutados con Codex; no se atribuye revisión personal a integrantes.

Revisión propuesta: Carolina compara formularios y criterios; Edier revisa transacciones, inventario e idempotencia; Vega revisa seguridad, pruebas, CI y la brecha IA; Conde ejecuta personalmente el recorrido y registra su decisión sobre el SHA. Cada decisión debe publicarse desde la cuenta de su autor y señalar el SHA.
