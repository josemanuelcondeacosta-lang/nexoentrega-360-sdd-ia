# Corrección de auditoría del Solicitante

Fecha: 2026-10-14. Ejecución y corrección por Codex a partir de captura aportada por el coordinador.

Síntoma: GET /api/audit devolvía HTTP 500 para Solicitante. El log sanitizado registraba QueryException con correlationId 76e883cf-411d-4497-a03f-508adb626704.

Causa: audit_events.resource_id es texto y requests.id es UUID; la subconsulta IN comparaba tipos distintos en PostgreSQL. Se convierte el ID de la subconsulta a texto, manteniendo el filtro de dueño y organización. No se modifica el esquema ni se amplían permisos.

Verificación real: suite PostgreSQL en nexo_test, 8 pruebas correctas y 67 aserciones, 1,47 s. Nueva regresión: historial propio devuelve 200 y un evento; el de otro dueño de la misma organización devuelve 403; otro Solicitante de ORG-B obtiene lista vacía. Reporte phpunit-audit.xml. Pint Linux correcto en 35 archivos.

Esta corrección posterior no convierte las pruebas anteriores en pruebas de esta versión ni aprueba G1/G2.
