# Coordinación G1/G2 — estado y solicitudes concretas

Fecha: 2026-10-14. Preparación asistida por Codex a solicitud de Conde José. Ningún gate global se aprueba automáticamente.

## Decisión humana recibida

Carolina publicó [su revisión en PR #10](https://github.com/josemanuelcondeacosta-lang/nexoentrega-360-sdd-ia/pull/10#issuecomment-6176370904), desde `karollCantillo`, identificando SHA `c70e431a1099b47bdd1a0dc79802c7af54cf6109` y fecha declarada `2026-10-14T19:15:00Z`.

Alcance que declara: SPEC-04 0.3.2-draft aprobada; plan/tareas aprobados; verificó AC-04-11, CSRF IA, nueve tablas y reparto de tareas. No certifica aplicación ni cierra otros hallazgos. Sus palabras y enlace se conservan, no se añade firma ficticia. Los cambios posteriores de tasks deben revisarse; SPEC-04 no cambió por este paquete de coordinación.

## Correcciones restantes preparadas

| Hallazgo | Artefacto y cambio | Estado |
|---|---|---|
| H-V01 | SPEC-05 AC-05-03: corte proveedor 5s y tolerancia instrumental propuesta de 250ms para observar fallback, sin ampliar llamada | Preparado; revisión de Vega pendiente |
| H-V02 | SPEC-05 regla: formato 422 → pertenencia 404 → estado 409 | Preparado; revisión pendiente |
| H-V03 | AC-05-15: fallback aprobable, origen conservado y evento, sin efectos de negocio | Preparado; revisión pendiente |
| H-V04 | AC-05-16: repetir aprobación 409, un solo evento | Preparado; revisión pendiente |
| H-V05 | SPEC-01 AC-01-07 fija 403/404 en escenarios distintos | Preparado; revisión de Vega pendiente |
| H-V06 | AC-01-14: CSRF ausente/incorrecto 419, POST/PATCH/login/logout/IA; pruebas de identidad separadas con token válido | Preparado; revisión pendiente |
| H-V07 | AC-05-08: inyección inválida/fallback; AC-05-17: salida válida con marcado escapado, sin ejecución | Preparado; revisión pendiente |
| H-E01 | SPEC-03 AC-03-09 fija 500 sanitizado, rollback y reintento sin resultado almacenado | Preparado; revisión de Edier/Carolina pendiente |
| H-E02 | AC-03-17: carrera despacho/cancelación, un 200 y un 409, stock 3/0/3 o 10/0/10 y un evento | Preparado; revisión pendiente |

SPEC-01/03/05 pasan a 0.4-draft; ahora 73 AC totales. AC nuevos son resultados esperados, no pruebas de producto ejecutadas. Regenerados JSON, cobertura y traza. Versiones nuevas requieren renovación de aprobación; las aprobaciones de Vega sobre versiones previas no se transfieren a 0.4.

## Solicitudes de revisión y criterio de cierre

- Vega: SPEC-01/05 0.4, timeout/CSRF/validación/inyección, contrato corregido R-V01/02 y ADR-003.
- Edier: SPEC-03 0.4, invariantes/oráculos/concurrencia/rollback, ADR-001/002 y datos/contrato consolidado.
- Carolina: confirmar SPEC-02/03 según alcance realmente leído, revisar AC-03-17 y nuevos rangos tasks; su revisión SPEC-04 ya está registrada sobre c70e431.
- Conde José: revisión personal del PR ajeno; análisis asistido PR #7 preparado, pero no sustituye su decisión humana.

Cada revisión debe citar SHA completo actual, UTC real, archivos/AC, verificaciones y decisión. Solicitud publicada no equivale a respuesta obtenida. Registrar enlaces cuando existan. No declarar G1/G2 global cerrado hasta completar cobertura, decisiones y checks sobre candidato revisado. No iniciar funcionalidades no aprobadas ni fusionar automáticamente.

## Verificación ejecutada por Codex

`export_specs.py --check`: 5 SPEC/73 AC coherentes; `build_coverage.py --check`: matriz/traza coherentes; `validate_openapi.py`: OpenAPI válido, CSRF y política de auditoría comprobados documentalmente. Suite documental: 14 pruebas OK, última ejecución 1.076s. Primera ejecución tras reformatear JSON falló en dos pruebas que buscan arrays compactos en texto; se restauró ese formato de la fuente sin alterar significado ni debilitar tests y se repitió con éxito. No prueba de producto ejecutada.
