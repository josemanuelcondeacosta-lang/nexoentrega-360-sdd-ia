# Carolina: revisar este paquete desde GitHub

No necesitas editar archivos para dejar una revisión personal. Codex preparó las correcciones; tú debes leerlas y decidir. Ningún texto de esta guía constituye tu aprobación.

1. Abre PR #10 y **Files changed**. Anota el SHA completo del último commit que vas a revisar (el coordinador te lo facilitará). Los SHA anteriores no aprueban cambios nuevos.
2. Lee `specs/SPEC-04.md`, AC-04-11: solicitante que pide historial de otra persona de su misma organización recibe 403; ID inexistente/interorganización recibe 404. Comprueba que contrato y enunciado §2 coincidan.
3. Lee `contracts/openapi-ia.yaml`: generar y aprobar resumen declaran header `X-XSRF-TOKEN` obligatorio y respuesta 419. En `contracts/openapi.yaml` las mismas operaciones conservan ese contrato.
4. Comprueba `docs/plan.md` y `docs/tasks.md`: nombres de tablas coinciden con datos.md; migración/seed preceden identidad, auditoría precede transiciones, frontend corresponde a Conde José e IA/CI a Vega. Todas las tareas siguen pendientes, con tests futuros claramente identificados.
5. Lee la evidencia `docs/evidence/correcciones-RV01-RV02-plan.md`. Puedes comprobar los resultados documentales registrados por Codex sin atribuirte su ejecución. Si ejecutas algo tú, añade tus comandos y resultados reales.
6. Publica un comentario desde tu cuenta en PR #10. Usa la plantilla y completa solo lo comprobado:

```text
Revisora: Carolina Cantillo / karollCantillo
SHA completo que leí:
Fecha UTC real de mi revisión:
Archivos y criterios comprobados:
Qué comprobé personalmente:
Evidencia consultada (indicar si la ejecución fue de Codex):
Hallazgos o pendientes:
Decisión SPEC-04 0.3.2-draft: pendiente / requiere cambios / aprobada
Decisión plan/tareas: pendiente / requiere cambios / aprobado
Alcance: esta revisión no certifica aplicación ni cierra otros hallazgos G1/G2.
```

En caso de duda, usa «requiere cambios» o «pendiente» y explica cuál. No hace falta inventar un defecto para demostrar revisión. Para fecha UTC puedes usar PowerShell `[DateTime]::UtcNow.ToString('yyyy-MM-ddTHH:mm:ssZ')`, anotando el momento real de tu revisión, no una fecha sugerida por Codex.

Si confirmas revisión real, el coordinador puede incorporar **fielmente** el enlace/decisión en el registro; no necesitas manipular Git para publicar el comentario. G2 del contrato requiere además revisión de Vega/Edier. No fusionar ni declarar listo todo el examen por este comentario.
