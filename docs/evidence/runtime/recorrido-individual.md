# Recorrido individual y cronológico

Solicitud del coordinador: abrir el recorrido de cada reserva/solicitud y ver los eventos en orden. Implementación y verificación asistidas por Codex; no aprobación G1/G2.

Interfaz: botón Ver recorrido en cada tarjeta autorizada de Solicitudes y cada evento de negocio en Auditoría. Ventana identifica el código y el estado de la solicitud; muestra una línea temporal numerada en español con fecha y actor. Borradores sin eventos tienen mensaje explícito. Cierre con botón o Escape. Respuestas tardías no sustituyen el recorrido de otra solicitud.

API: se conserva el endpoint GET /api/requests/{requestId}/audit y sus límites de dueño, organización y rol; Bodega no recibe permiso nuevo. El historial individual ordena por UTC ascendente; empates de segundo se resuelven por la secuencia de transición SEND, RESERVE, DISPATCH, DELIVER o CANCEL, y luego ID. El listado global conserva primero los eventos recientes. No se reescriben eventos ni fechas.

Verificación real en PostgreSQL: 8 pruebas y 74 aserciones correctas, 1,32 s; incluye orden de los cuatro eventos y rechazo de Bodega. Pint Linux: 35 archivos correctos. OpenAPI: 20 rutas y 22 operaciones válidas, descripción de orden actualizada.

Navegador Chromium: primera suite completa de cuatro pruebas correcta, 22,9 s. Tras añadir protección frente a respuestas tardías se repitieron los dos recorridos completos, desktop 1366 y mobile 390: dos correctos, 14,9 s. Abren la misma solicitud desde Auditoría y desde Solicitudes, verifican el código, cuatro eventos ordenados, Escape y ausencia de desbordamiento. Reportes phpunit-history.xml y playwright-history.json; captura recorrido-individual-mobile.png. No equivale a revisión humana, prueba de carga ni aceptación completa del examen.
