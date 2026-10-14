# Frontend e integración provisional con PostgreSQL

Permite entrar/salir con ocho cuentas ficticias, crear/editar/enviar solicitudes, reservar, despachar, registrar entrega, cancelar y consultar listados, inventario e historial con límites comprobados por servidor. Incluye sesiones y CSRF, reserva transaccional e idempotencia, auditoría, interfaz responsive y asistente visible de fallback sin proveedor LLM real.

Implementación adelantada por instrucción expresa del coordinador. No convierte G1/G2 en aprobados. No fusionar ni presentar como entrega final sin revisión independiente y cierre de brechas.

Verificación ejecutada sobre candidato f33183aa72c261bb513cf8aada1113605b461262: PostgreSQL 7 pruebas/57 aserciones, Chromium 4 E2E a 390/1366, reserva HTTP concurrente 200/409 con un evento, Pint 35 archivos, auditorías de dependencias y escáner limitado sin hallazgos. GitHub Actions 38631036099 y 38631043185 terminaron success, incluyendo navegador y concurrencia. Consultar docs/evidence/runtime/verification.md y reportes adjuntos al repositorio.

Ejecución reproducible: docs/delivery/runbook-provisional.md. PDF consolidado provisional y hash: deliverables/provisional/manifest.json. Tag provisional-ui-v0.2 identifica el paquete con protección binaria de artefactos; reemplaza provisional-ui-v0.1 para descarga. No se crea release-examen. Código y pruebas generados/ejecutados con Codex; no se atribuye revisión personal a integrantes. Pendientes: cierre humano, LLM real/validador completo, carga y cobertura exhaustiva del examen.

Revisión propuesta: Carolina compara formularios y criterios; Edier revisa transacciones, inventario e idempotencia; Vega revisa seguridad, pruebas, CI y la brecha IA; Conde ejecuta personalmente el recorrido y registra su decisión sobre el SHA. Cada decisión debe publicarse desde la cuenta de su autor y señalar el SHA.
