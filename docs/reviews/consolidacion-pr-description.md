# Reunir y corregir el paquete documental G1/G2

Reúne cuatro ADR, seis diagramas, contratos, plan/tareas y evidencia de revisión en rutas canónicas, con procedencia de Edier/Vega/Carolina. Conserva original histórico de Carolina y registro corregido sin atribuir aprobación nueva.

Corrige R-V01 integrando fragmento IA v0.2.0 de Vega (`833e9b9`) con CSRF en las dos escrituras; R-V02 alinea historial con 403 misma organización/acción prohibida y 404 inexistente/interorganización; SPEC-04 pasa a 0.3.2-draft. Mantiene corrección de estrategia concurrente de Vega. Plan usa nombres de datos.md y tareas con gates, dependencias de migración/seed/auditoría, responsables y pruebas futuras; no funcionalidad implementada.

RF01–12/RNF01–06; AC-04-11 modificado y demás AC solo como alcance de revisión documental. Tareas: organización, contratos, trazabilidad y preparación de revisión independiente. Evidencia: `docs/reviews/revision-completa.md` y `docs/evidence/correcciones-RV01-RV02-plan.md`.

Validación ejecutada: 14 tests documentales OK, SPEC/matriz coherentes, OpenAPI 3.1.1 válido, 2 ejemplos válidos, 3 negativos de datos y regresión de omisión CSRF detectada. No tests de aplicación/PostgreSQL ejecutados. Guía `docs/reviews/carolina-revision-sencilla.md` permite revisión personal desde GitHub sin editar el proyecto.

Codex a solicitud de Conde José; no atribuye contribuciones o aprobación a otro integrante. Revisión personal de Carolina y validación de contrato por Vega/Edier: pendientes. Los restantes H-V/H-E no se cierran automáticamente. PR sigue borrador; no merge ni declaración de G1/G2 global aprobado.

Coordinación posterior: se registra comentario real de Carolina en c70e431 que aprueba SPEC-04/plan/tareas. SPEC-01/03/05 0.4-draft corrigen H-V01…H-V07 y H-E01/02 con cinco AC nuevos (73 en total); nuevos rangos tareas/matriz/JSON regenerados. 14 pruebas documentales pasan tras conservar formato compacto de arrays; renovaciones humanas de versiones 0.4 pendientes. Análisis asistido PR #7 preparado y solicitudes concretas de revisión; no aprobación en nombre del coordinador.
