# Proponer estructura Laravel y contrato HTTP integrado

No existía ADR-001 ni contrato base que uniera las operaciones de negocio con IA. El paquete propone Laravel + PostgreSQL como monolito modular, contexto/componentes Mermaid y OpenAPI 3.1.1 con 19 rutas y 21 operaciones, conservando el fragmento IA de `a13aa17` sin alterar rutas/componentes. Corrige formato de constitución D-21 y prepara texto CL-16/17/18 para el docente, todavía no enviado por falta de destinatario/canal.

Rama dependiente de `docs/g2-vega-ia`: no fusionar las ramas ni declarar G1 aprobado. G2 parcial: ADR-002/003 y otros diagramas/plan pendientes. Sesión/CSRF y decisiones abiertas quedan documentadas explícitamente.

RF01–12, RNF01–06; AC de SPEC-01–05 versión 0.3-draft según matriz. Tareas del paquete: límites del monolito, diagramas contexto/componentes, contratos base/IA, corrección documental G1 y preparación de preguntas. No código de funcionalidades.

Validación real: generación, check de coherencia y validator OpenAPI; 2 ejemplos y 3 negativos de schema. Ver `docs/evidence/g2-arquitectura-validacion.md`. No pruebas de producto ejecutadas. IA: elaboración asistida por Codex a solicitud de Conde José; no atribución de trabajo personal ni firma humana.

Revisión requerida: Edier (estructura/base/transacción), Vega (IA/seguridad) y Carolina (constitución); propuestas, no revisiones efectuadas. Completar UTC, SHA, hallazgos y decisión en `gate-review-G2.md`. PR #1 debe continuar en borrador.

Actualización: Conde José reporta que la profesora respondió afirmativamente a CL-16/17/18. SPEC-01/04 0.3.1-draft, AC-04-10/11, matriz/traza y contrato incorporan permisos de lectura acordes al reporte; ninguna función implementada. Fecha/canal y evidencia original pendientes. Las revisiones previas no aprueban esta nueva versión. Validación realizada tras cambios: SPEC/matriz coherentes, OpenAPI válido con fragmento IA preservado y 14 pruebas documentales aprobadas.
