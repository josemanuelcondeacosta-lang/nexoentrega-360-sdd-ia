# Preguntas CL-16, CL-17 y CL-18 — mensaje listo para enviar

Estado: **NO ENVIADO POR CODEX**. Destinatario/canal no proporcionados y sin respuesta registrada. Este archivo preserva las preguntas publicadas en el clarification-log v0.3; no añade decisiones del cliente.

Actualización 2026-10-14: el coordinador comunica respuesta afirmativa de la profesora a las tres propuestas. El estado anterior describe la preparación del mensaje; actualmente existe una **respuesta reportada por el coordinador**, sin fecha/canal original ni evidencia adjunta. Codex no realizó envío. Registro vigente: `clarification-log.md`, sección «Respuesta comunicada por el coordinador». No volver a enviar automáticamente.

Asunto sugerido: NexoEntrega 360 — aclaración de permisos RF08/RF09 (CL-16 a CL-18)

Profesor/a:

Al cotejar el enunciado, necesitamos confirmar estos permisos para cerrar G1:

1. **CL-16 (RF08, §2):** La matriz permite consultar inventario a Bodega y Auditor, pero no al Coordinador, que reserva. ¿Puede el Coordinador consultar el inventario de su organización? Riesgo: conceder acceso no autorizado o reservar sin disponibilidad visible. Propuesta del equipo por confirmar: permitir solo lectura al Coordinador, sin escritura.
2. **CL-17 (RF09, §1):** El recorrido incluye consultar su auditoría, pero la matriz da auditoría solo al Auditor. ¿Qué otros roles pueden consultar historial? Riesgo: recorrido incompleto o exposición indebida. Propuesta por confirmar: Coordinador consulta historial de su organización y Solicitante el de sus propias solicitudes. Mientras tanto, el contrato conserva acceso literal solo para Auditor.
3. **CL-18 (RF08):** ¿El Solicitante puede acceder al tablero limitado a sus solicitudes? Riesgo: exponer conteos de toda la organización. Propuesta por confirmar: tablero con conteos propios, sin inventario.

Registraremos su respuesta como decisión del cliente, con fecha y canal, y actualizaremos SPEC, contrato y pruebas afectados. Gracias.

## Seguimiento de envío real

| Campo | Valor |
|---|---|
| Responsable del envío observado | Conde José, coordinador |
| Profesor / dirección / canal | Contacto «PROSEFORA ARQUITECTURA», WhatsApp; nombre completo no visible |
| Fecha UTC de envío | Fecha/zona no visibles; captura muestra 12:17 p. m.; no enviado por Codex |
| Enlace o identificador del mensaje | Captura adjunta en conversación; inspección en `docs/evidence/respuesta-docente-CL16-18.md` |
| Respuesta textual y autoridad | «Las respuesta es sí», 12:22 p. m., citando las consultas; respaldo visual del reporte de José |
| SPEC / AC / contrato corregidos | SPEC-01/04 0.3.1-draft y contrato, commit f9106a5; revisión independiente pendiente |

La captura recibida el 2026-10-14 respalda envío y respuesta; esta fecha es recepción de evidencia, no fecha inferida del chat WhatsApp. El texto inicial y estado previo de este archivo se conservan como historial de preparación.

Enviar requiere destinatario y canal accesible (o envío por el coordinador). No marcar «enviado» por preparar el texto ni «resuelto» por respuesta de IA.
