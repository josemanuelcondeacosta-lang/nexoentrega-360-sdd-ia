# AI-007 — Frontend e integración provisional

Fecha: 2026-10-14. Herramienta: Codex. Solicitante: coordinador del proyecto; instrucción «haz esto con lo que hay después corrijo», frontend/API y verificación real. No se atribuye revisión humana a una persona distinta del solicitante.

Entradas: SPECS y contratos consolidados, arquitectura Laravel + PostgreSQL y requisitos publicados en docs/examen-oficial. Salida: aplicación apps/nexo, Docker, pruebas PostgreSQL/E2E y flujo CI, informes y PDF provisional.

Decisión técnica propuesta: conservar monolito modular y permisos en servidor, sesiones con CSRF, bloqueos de filas e idempotencia transaccional. Interfaz consume API y presenta acciones por rol. El fallback IA está marcado explícitamente y no reemplaza la evidencia de un LLM real.

Verificación ejecutada por el agente: consultar docs/evidence/runtime/verification.md, JUnit, navegador y enlaces CI. No equivale a firma, aceptación ni revisión humana independiente. Se corrigieron conexión de runtime, refresco del formulario, arnés E2E y dependencia de fuentes externas observados en ejecución.

Riesgos: adelantar implementación no subsana el gate previo del examen. Pendientes: revisor humano sobre SHA, proveedor IA y validador, carga, cobertura completa, aceptación manual y aportes personales verificables. No se crea release-examen.

Validación humana de esta salida: pendiente de publicación por cada autor desde su cuenta. Decisión: propuesta provisional; no aprobado por el equipo.
