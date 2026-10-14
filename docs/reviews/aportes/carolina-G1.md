# Revisión Independiente G1 — Carolina Cantillo

* **Autora:** Carolina Cantillo
* **Rol:** Especificaciones y Documentación
* **Fecha:** 2026-10-14T16:30:00Z
* **SHA de la revisión anterior publicada:** `cf9d5f5d905dee3a3774c623283c7045271dbe60`, rama `docs/g1-edier-cotejo-oficial`.
* **SHA candidato actualizado para revisión:** `b1dbbf93d9800b69d32daec352aacf754f876ebe`, rama `docs/g2-architecture` (incluye SPEC-01/04 0.3.1-draft y evidencia de aclaraciones docentes).
* **Confirmación de revisión del candidato por Carolina:** PENDIENTE; registrar fecha UTC y enlace personal después de realizarla.
* **Corrección documental:** Codex a solicitud de Conde José. La referencia anterior `3b8c9d1` no pudo verificarse en Git ni en GitHub; no se sustituye automáticamente por una aprobación sobre otro SHA.

## 1. Alcance y Fuentes Consultadas
* specs/SPEC-02.md (Solicitudes, RF03, RF04)
* specs/SPEC-03.md (Inventario y Reservas, RF04–RF07)
* specs/SPEC-04.md (Consultas y Auditoría, RF08, RF09, RNF03–RNF06)
* Enunciado oficial, aclaraciones docentes y anexos (docs/examen-oficial/anexos/acceptance-oracles.md, docs/examen-oficial/anexos/fixture-scenarios.json)

## 2. Comprobaciones Realizadas
1. **SPEC-02:** Flujos del ciclo de vida (BORRADOR → ENVIADA → RESERVADA → DESPACHADA → ENTREGADA) y reglas de cancelación por el solicitante verificados contra el enunciado y aclaraciones docentes.
2. **SPEC-03:** Consistencia de reserva atómica de stock e idempotencia (Idempotency-Key) en transacciones concurrentes.
3. **SPEC-04:** Aislamiento de datos entre organizaciones (ORG-A y ORG-B), roles de acceso y registro de auditoría revisados con las últimas actualizaciones.
4. **Comandos de Verificación:**
   - python scripts/export_specs.py --check
   - python scripts/build_coverage.py --check

## 3. Decisiones por Especificación
* **SPEC-02:** aprobación registrada por Carolina para la revisión anterior; confirmar vigencia al consolidar el candidato final.
* **SPEC-03:** aprobación registrada por Carolina para la revisión anterior; no declara cerrados los hallazgos de Edier ni cambios futuros.
* **SPEC-04:** nueva revisión PENDIENTE sobre el candidato actualizado, porque los permisos y AC cambiaron tras CL-16/17/18.

## 4. Hallazgos e Integración
La afirmación anterior de ausencia de hallazgos bloqueantes pertenece al registro previo de Carolina. Esta corrección no verifica ni cierra H-V01 a H-V07 o los hallazgos de Edier. Las aclaraciones docentes y la versión actualizada deben cotejarse en el SHA candidato antes de registrar una nueva aprobación. Fecha y resultado real de esa revisión: PENDIENTES.

Para confirmar: Carolina debe abrir el candidato indicado, revisar las diferencias de SPEC-04 y sus AC, ejecutar las comprobaciones documentales que declare, y publicar desde su cuenta una revisión con SHA completo, fecha UTC, observaciones y decisión. Ninguna firma o comprobación personal se agrega por Codex.

Actualización del candidato: el PR #10 incluye ahora SPEC-04 **0.3.2-draft** (R-V02), contrato CSRF y plan/tareas corregidos. El SHA `b1dbbf9` anterior conserva validez como referencia histórica, pero no aprueba estos cambios posteriores. Revisar el SHA del commit de correcciones del PR #10, facilitado por el coordinador, usando `docs/reviews/carolina-revision-sencilla.md`. Aprobación de esta versión: **PENDIENTE**.
