# Revisión editorial del informe

Fecha: 2026-10-14. Edición realizada por Codex a petición del coordinador. No es aprobación humana.

Problema de la primera edición: 123 páginas formadas por concatenación de fuentes, tablas Markdown sin maquetar, contrato YAML completo y referencias históricas mezcladas con resultados actuales. No existía un archivo Word editable en el repositorio.

Resultado: edición Word y PDF en deliverables/revision-editorial, con nueve apartados del anexo 09, portada, índice, títulos jerárquicos, tablas, numeración, enlaces y capturas reales. Se conservan cinco historias y 73 AC, cuatro ADR y siete registros IA. El contrato se desarrolla con tabla de sus 22 operaciones y explicación de seguridad; no se copia el YAML completo al cuerpo. Los seis diagramas siguen enlazados como fuentes Mermaid, con renderizado pendiente, sin fingir figuras incorporadas.

Verificación: PDF de 65 páginas, índice paginado y 38 marcadores; sin páginas vacías ni texto fuera del área revisada por coordenadas. Revisadas visualmente portada, entrada de especificación y criterios de aceptación. Word reabierto con python-docx y XML válido, con 38 tablas reales, dos imágenes, títulos y campo TOC. Su paginación final depende de Word; actualizar índice al abrir. Ambos formatos se generan desde el mismo modelo editorial, no por conversión de Word a PDF.

La edición original y su manifiesto permanecen sin modificar. El nuevo manifiesto identifica el código dd806db9b47abff9dd30fb393761941245fed007 y los hashes de los documentos. El informe distingue la nueva regresión PostgreSQL (8 pruebas/67 aserciones) de los E2E y CI ejecutados en f33183a. No se aprueban gates, criterios completos ni contribuciones humanas.
