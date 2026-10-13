# Registro de aclaraciones — borrador v0.1

**Estado:** todas PENDIENTES de respuesta docente o aprobación de supuesto por equipo. Las propuestas no son decisiones del cliente.

| ID | RF | Pregunta para docente | Riesgo | Propuesta provisional | Estado | Responsable |
|---|---|---|---|---|---|---|
| CL-01 | RF03 | ¿Cuáles son las zonas válidas y cómo se rechaza una zona desconocida? | Datos inválidos o reportes inconsistentes | Catálogo configurable por organización; desconocida → 422. | Pendiente | Por asignar |
| CL-02 | RF03 | ¿La fecha requerida es fecha local sin hora o instante UTC? ¿Qué zona horaria muestra la interfaz? | Solicitudes válidas rechazadas por diferencias horarias | Almacenar fecha calendario YYYY-MM-DD; mostrar America/Bogota; comparar con fecha local al enviar. | Pendiente | Por asignar |
| CL-03 | RF03/RF10 | ¿Cuál es la longitud máxima de observaciones y se permiten saltos de línea? | Cargas excesivas y salida IA incontrolable | Máximo 1000 caracteres, texto plano y escape al mostrar. | Pendiente | Por asignar |
| CL-04 | RF07 | ¿Qué errores de reserva se guardan en la clave idempotente y con qué política se reintentan? | Respuestas contradictorias y doble reserva | Guardar respuestas completadas (incluido 409 de negocio), no 401/403/500; TTL ≥24h; reintentar falla temporal con la misma clave. | Pendiente | Por asignar |
| CL-05 | RF10-RF12 | ¿Deben persistirse intentos fallidos de IA y borradores alternativos? ¿Cuánto tiempo? | Ausencia de trazabilidad y almacenamiento innecesario | Guardar metadatos sanitizados de intento y borradores válidos; nunca prompts con secretos. | Pendiente | Por asignar |
| CL-06 | RF05/RF06 | ¿Puede existir ajuste externo de existencia física o solo seed durante el examen? | Cambios de stock fuera de transacciones previstas | Sin ajuste por API; stock inicial por seed; operaciones de reserva/despacho/cancelación. | Pendiente | Por asignar |
| CL-07 | RF03 | ¿Qué formato y obligatoriedad tiene el campo zona por organización? | Validación ambigua | String de catálogo por organización, obligatorio. | Pendiente | Por asignar |
| CL-08 | RF12 | ¿Debe el fallback producir recommendations vacías o deterministas? | Resultado no verificable en fallo de proveedor | Resumen determinista, recommendations vacías, origen fallback explícito. | Pendiente | Por asignar |

**Registro de respuesta:** fecha UTC, autor de respuesta, canal/enlace, decisión, SPEC/contrato/prueba actualizados.
