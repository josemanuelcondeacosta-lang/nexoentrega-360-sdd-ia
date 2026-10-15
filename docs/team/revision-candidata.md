# Revisión del paquete de integración

PR para revisar: [#16, candidata en borrador](https://github.com/josemanuelcondeacosta-lang/nexoentrega-360-sdd-ia/pull/16). Conserva los commits de los PR originales. No fusionar ni aprobar automáticamente.

## Pasos comunes en GitHub y VS Code

1. Abrir el PR y consultar `Files changed` y el último commit. Copiar su SHA completo; no reutilizar el SHA de una revisión anterior.
2. En VS Code, guardar el trabajo propio antes de cambiar de rama. Ejecutar `git fetch origin` y abrir una rama de revisión desde `origin/integration/examen-candidate`. No sobrescribir cambios locales.
3. Leer `docs/evidence/runtime/candidate/verification.md` y los archivos de su responsabilidad. Distinguir el SHA del código probado de los commits documentales posteriores.
4. Ejecutar solo las comprobaciones que cada persona pueda realizar. Las pruebas con datos reinicializados requieren un proyecto/base propios, nunca la demo compartida. Si no se ejecuta algo, escribirlo expresamente.
5. Publicar desde la cuenta personal un comentario o revisión con SHA, UTC real, archivos/AC leídos, comandos ejecutados y resultado, observaciones y decisión. Si hay errores, pedir cambios concretos. No aprobar un cambio propio ni publicar texto atribuido a otra persona.

## Carolina

- Revisar SPEC-02 y SPEC-03 completas contra los anexos y decisiones del cliente; prestar atención a límites, transiciones, rollback y carrera despacho/cancelación. Registrar su decisión independiente si efectivamente las revisa.
- Comprobar constitution 401/403/404 y CL-16/17/18; cotejar plan/tareas con la matriz generada y confirmar que los responsables coinciden.
- Revisar el complemento de ADR-003 como cambio técnico nuevo, sin darlo por aprobado por ser autora del documento inicial.
- Entregable: revisión personal enlazada, con hallazgos o decisión por artefacto. Sus aprobaciones previas de SPEC-04 se conservan con su SHA original.

## Edier

- Revisar ADR-001: PHP 8.5.0, Laravel 12.70.0, PostgreSQL 17.6, estructura y rutas reales; comprobar R-E02 y decidir sobre la corrección.
- Contrastar los resultados de reserva, TTL, rollback y carreras con sus oráculos. La ejecución asistida conservada no acredita que él la ejecutó personalmente.
- Revisar el cambio ajeno de ReplayProvider si tiene alcance para hacerlo, sin aprobar como revisor independiente las partes de SPEC-03/ADR-002/modelo que coescribió.
- Entregable: decisión con SHA y revisión cruzada real; resolver los hallazgos que correspondan en su rama o PR.

## Vega

- Revisar las correcciones ajenas de contexto replay, los cinco casos añadidos y el comportamiento fallback.
- Revisar ADR-003, seguridad/CSRF y matriz de permisos. Realizar una revisión independiente del modelo/ADR-002 si puede comprobarlos; de lo contrario dejarlo pendiente.
- Comprobar CI, carga y límites de la captura real: 18,9s no cumple el plazo de producto y se identifica como replay; no presentar reproducción como llamada nueva.
- Entregable: decisión personal y evidencia de las comprobaciones que efectivamente ejecute.

## Conde José

- Leer y publicar su revisión de un aporte ajeno, indicando lo que comprobó personalmente. El análisis de Codex es apoyo, no una revisión personal por sí mismo.
- Coordinar correcciones, inspección manual de interfaz y las brechas de `ac-evidence.csv` antes del cierre.
- Confirmar que todos descargan el mismo PDF únicamente cuando el paquete haya sido aceptado. El Word/PDF candidato no acredita la entrega final ni autoriza `release-examen`.

## Formato de comentario personal

```text
Revisor: [cuenta/nombre de quien publica]
Fecha UTC: [fecha/hora reales]
SHA completo revisado: [...]
Archivos y criterios comprobados: [...]
Comandos ejecutados y resultados: [...] / No ejecutados: [...]
Hallazgos y observaciones: [...]
Decisión por alcance: solicitar cambios / aprobar los artefactos indicados
Límites: esta decisión no aprueba otros archivos ni versiones posteriores.
```

Completar únicamente después de realizar la revisión. No copiar una decisión de aprobación antes de comprobar el paquete.
