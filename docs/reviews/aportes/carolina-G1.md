# Revisión Independiente G1 — Carolina Cantillo

* **Autora:** Carolina Cantillo
* **Rol:** Especificaciones y Documentación
* **Fecha del registro anterior:** 2026-10-14T16:30:00Z
* **SHA de la revisión anterior publicada:** `cf9d5f5d905dee3a3774c623283c7045271dbe60`, rama `docs/g1-edier-cotejo-oficial`. La referencia `3b8c9d1` que figuraba antes no existe en Git ni en GitHub y se retira.
* **SHA revisado (revisión vigente):** `b1dbbf93d9800b69d32daec352aacf754f876ebe`
* **Rama Origen:** `docs/g2-architecture` (PR #5; incluye SPEC-01/04 0.3.1-draft y evidencia de aclaraciones docentes).
* **Fecha UTC de la revisión vigente:** 2026-10-14T18:11:39Z (ejecución de comprobaciones); publicación: __FECHA_PUBLICACION__
* **Revisión publicada desde mi cuenta:** __ENLACE_REVISION__
* **Corrección documental previa:** Codex a solicitud de Conde José (PR #9); no atribuyó aprobación. Los datos de la revisión vigente los registra Carolina.

## 1. Alcance y Fuentes Consultadas
* specs/SPEC-02.md (Solicitudes, RF03, RF04): sin cambios entre `cf9d5f5` y `b1dbbf93` (`git diff`).
* specs/SPEC-03.md (Inventario y Reservas, RF04–RF07): sin cambios entre `cf9d5f5` y `b1dbbf93` (`git diff`).
* specs/SPEC-04.md 0.3.1-draft (Consultas y Auditoría): actores, openQuestions, AC-04-01, AC-04-02, AC-04-10, AC-04-11, notas e historial.
* specs/SPEC-01.md 0.3.1-draft: solo cotejo de la matriz de permisos; no se aprueba (fuera de mi alcance).
* contracts/openapi.yaml (`/api/audit`), docs/evidence/respuesta-docente-CL16-18.md, docs/clarifications/envio-docente-CL16-18.md.

## 2. Comprobaciones Realizadas (Python 3.11.3, Windows, sobre `b1dbbf93`)
| Comando | Resultado |
|---|---|
| `python scripts/export_specs.py --check` | `5 SPEC y 68 AC validados; JSON coherentes` (exit 0) |
| `python scripts/build_coverage.py --check` | `Matriz y traza coherentes con las SPEC` (exit 0) |
| `python -m unittest discover -s scripts -p "test_*.py" -v` | `Ran 14 tests … OK` (exit 0) |

Cotejo CL-16/17/18 en SPEC-04:
* Coordinador: lectura de inventario (AC-04-10) e historial (AC-04-11) de su organización — reflejado.
* Solicitante: tablero solo con conteos propios y 403 en inventario (AC-04-10); historial solo de sus solicitudes (AC-04-11) — reflejado.
* Bodega: sin acceso a auditoría (403 en AC-04-11; actores) — reflejado.

## 3. Decisiones por Especificación (sobre `b1dbbf93`)
* **SPEC-02:** APROBADA (sin cambios desde la revisión anterior), sujeta al ajuste que resulte del hallazgo C-01.
* **SPEC-03:** APROBADA (sin cambios desde la revisión anterior). No declara cerrados los hallazgos de Edier.
* **SPEC-04:** CAMBIOS SOLICITADOS por el hallazgo C-01.

## 4. Hallazgos e Integración
* **C-01 (bloqueante):** AC-04-11 y `openapi.yaml /api/audit` devuelven 404 al solicitante que consulta el historial de una solicitud ajena. SPEC-01 (edgeCases) y SPEC-02 devuelven 403 si la solicitud es de otro solicitante de la misma organización. Hay que alinear SPEC-01/02/04 y el contrato. Según la precedencia del enunciado §2, correspondería 403 dentro de ORG-A y 404 para ORG-B.
* **C-02:** la cabecera "Estado y aprobación" de SPEC-01/04 sigue diciendo "versión 0.3".
* **C-03:** la entrada `0.3-draft` del historial se renombró a `0.3.1-draft` en lugar de conservarse.
* **C-04:** en los actores de SPEC-04, el Solicitante no menciona su historial ni su tablero propios.
* **C-05:** la evidencia original de CL-16/17/18 (captura y zona horaria) sigue pendiente.

Esta revisión no verifica ni cierra H-V01 a H-V07 ni los hallazgos de Edier. Tampoco aprueba SPEC-01, SPEC-05, los ADR ni el contrato completo. G1 no queda cerrado.
