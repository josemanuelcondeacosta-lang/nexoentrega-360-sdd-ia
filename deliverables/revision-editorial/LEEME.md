# Informe reorganizado: Word y PDF

- Examen_SDD_IA_Equipo_NexoEntrega360.docx: versión editable con títulos jerárquicos, índice de Word, tablas reales, encabezado, numeración y enlaces.
- Examen_SDD_IA_Equipo_NexoEntrega360.pdf: edición de lectura con índice paginado y marcadores, generada con el mismo contenido editorial.
- manifest.json: SHA del código fuente y hashes de ambos documentos.

El PDF anterior en deliverables/provisional se conserva. Esta edición sigue siendo provisional: no crea aprobaciones, firmas ni resultados inexistentes. El Word/PDF desarrolla los nueve apartados del anexo 09, conserva las cinco historias y 73 AC y separa resultados de versiones diferentes. No es una exportación literal del repositorio ni incluye el volcado YAML completo.

En Word actualizar el índice si es necesario: Ctrl+A, F9; elegir actualizar toda la tabla. Si se modifica el Word manualmente, el PDF no cambia automáticamente. Los dos archivos aquí se generan desde el mismo modelo editorial; para actualizarlo de forma reproducible modificar las fuentes o scripts/build_report.py, no afirmar sincronización después de editar solo un formato.

Generación desde la raíz: instalar contracts/requirements-docs.lock, docs/delivery/requirements-pdf.lock y docs/delivery/requirements-editorial.lock; ejecutar `.venv-docs/Scripts/python.exe scripts/build_report.py --sha <SHA>`. La generación utiliza Arial de Windows. La revisión final debe incluir diagramas renderizados, alineación de metadatos históricos y todos los pendientes del examen.
