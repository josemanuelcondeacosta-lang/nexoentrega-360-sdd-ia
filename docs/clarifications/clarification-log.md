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

## Ampliación G1 — preguntas adicionales pendientes

Las propuestas de ambas tablas son supuestos provisionales, no decisiones del cliente. Responsables propuestos: Carolina para requisitos; Edier para stock/idempotencia; José Conde para permisos/UI; José Vega para IA/verificación. No acredita aportes reales.

| ID | Requisito | Pregunta | Riesgo | Propuesta / supuesto provisional | Estado |
|---|---|---|---|---|---|
| CL-09 | RF01/02/04/08/09 | ¿Cuáles son los nombres exactos de los cuatro roles y permisos de cancelación, entrega, tablero e inventario? ¿Qué precedencia aplica entre 403 y 404? | Autorizar de más o incumplir examen | Matriz en requirements-source.md; alcance por organización antes de entregar recursos | Pendiente docente y revisión equipo |
| CL-10 | RF03 | ¿Catálogo exacto de prioridades y formato/únicidad de código? | Valores incompatibles | ALTA/MEDIA/BAJA; código único generado por servidor, formato por confirmar | Pendiente |
| CL-11 | RF07 | ¿La clave expira exactamente a 24h? ¿Cómo se conservan claves vencidas, qué operaciones cubre y límite de longitud? | Doble ejecución o borrado indebido | Reserva; 1–128 caracteres; ámbito usuario/org/operación; frontera >=24h y archivo histórico separado | Pendiente |
| CL-12 | RF10/11/12 | ¿Cuáles son los seis campos autorizados, claves evidenceFields y nombres exactos del contrato? ¿Qué orden selecciona las 20 solicitudes? | Filtración y contrato inválido | requestId/zone/priority/requiredDate/observations/lines; fecha+ID; contrato SPEC-05 | Pendiente anexos |
| CL-13 | RNF01–RNF06 | ¿Pueden facilitar los textos exactos de todos los RNF y criterios de rendimiento/accesibilidad? | Cobertura nominal sin cumplimiento real | Asignación provisional de coverage-matrix.md, no cerrar requisitos | Pendiente enunciado |
| CL-14 | RF01/RNF seguridad | ¿Política exigida de sesión, CSRF, expiración y contraseñas? | Autenticación incompleta | Sesión revocable de servidor; parámetros y controles en ADR-003 después de aclaración | Pendiente |
| CL-15 | RF09 | ¿Auditar login, rechazos e intentos IA además de transiciones? ¿Retención obligatoria? | Evidencia insuficiente o exceso de datos | Eventos de negocio atómicos; logs sanitizados de rechazos separados; retención por confirmar | Pendiente |

Ninguna pregunta ha sido enviada al docente por el asistente. Registrar la respuesta real y quién la recibió antes de cambiar estado a resuelta.
