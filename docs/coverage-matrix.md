# Matriz de cobertura y verificación — G1

Estado: **BORRADOR / SIN APROBACIÓN**. Cobertura nominal de RF01–RF12 y RNF01–RNF06; no demuestra cumplimiento del examen. Todas las asignaciones RNF son provisionales hasta CL-13. RNF03/RNF04 comparten rendimiento como propuesta de cotejo, no como interpretación oficial.

## Requisito → historia → SPEC → criterios

| Requisito | Historia | SPEC | AC propuestos | Cotejo oficial |
|---|---|---|---|---|
| RF01 | HU-01 | SPEC-01 | AC-01-01, AC-01-04, AC-01-05, AC-01-06, AC-01-08, AC-01-10 | Pendiente |
| RF02 | HU-01 | SPEC-01 | AC-01-02, AC-01-03, AC-01-07, AC-01-09 | Pendiente |
| RNF01 | HU-01 | SPEC-01 | AC-01-01, AC-01-02, AC-01-03, AC-01-05, AC-01-06, AC-01-07, AC-01-08, AC-01-09, AC-01-10 | Pendiente |
| RNF06 | HU-01 | SPEC-01 | AC-01-10 | Pendiente |
| RF03 | HU-02 | SPEC-02 | AC-02-01, AC-02-02, AC-02-04, AC-02-05, AC-02-06, AC-02-07 | Pendiente |
| RF04 | HU-02 | SPEC-02 | AC-02-03, AC-02-08, AC-02-09, AC-02-10 | Pendiente |
| RNF05 | HU-02 | SPEC-02 | AC-02-11 | Pendiente |
| RF04 | HU-03 | SPEC-03 | AC-03-05, AC-03-06, AC-03-07, AC-03-13 | Pendiente |
| RF05 | HU-03 | SPEC-03 | AC-03-01, AC-03-02, AC-03-03, AC-03-08, AC-03-09 | Pendiente |
| RF06 | HU-03 | SPEC-03 | AC-03-05, AC-03-06, AC-03-13 | Pendiente |
| RF07 | HU-03 | SPEC-03 | AC-03-04, AC-03-10, AC-03-11, AC-03-12 | Pendiente |
| RNF02 | HU-03 | SPEC-03 | AC-03-01, AC-03-02, AC-03-03, AC-03-05, AC-03-06, AC-03-07, AC-03-08, AC-03-09, AC-03-13 | Pendiente |
| RF08 | HU-04 | SPEC-04 | AC-04-01, AC-04-02, AC-04-05, AC-04-06, AC-04-10 | Pendiente |
| RF09 | HU-04 | SPEC-04 | AC-04-03, AC-04-04, AC-04-07, AC-04-08 | Pendiente |
| RNF03 | HU-04 | SPEC-04 | AC-04-09 | Pendiente |
| RNF04 | HU-04 | SPEC-04 | AC-04-09 | Pendiente |
| RNF06 | HU-04 | SPEC-04 | AC-04-03, AC-04-07 | Pendiente |
| RF10 | HU-05 | SPEC-05 | AC-05-01, AC-05-05, AC-05-10 | Pendiente |
| RF11 | HU-05 | SPEC-05 | AC-05-02, AC-05-04, AC-05-06, AC-05-07, AC-05-08, AC-05-11, AC-05-12 | Pendiente |
| RF12 | HU-05 | SPEC-05 | AC-05-03, AC-05-09, AC-05-13 | Pendiente |
| RNF01 | HU-05 | SPEC-05 | AC-05-05, AC-05-08, AC-05-11 | Pendiente |
| RNF06 | HU-05 | SPEC-05 | AC-05-04, AC-05-12, AC-05-13 | Pendiente |

## Plan de verificación por criterio
El resultado esperado se fija antes de ejecución. IDs T-AC identifican pruebas PLANIFICADAS; no son archivos de test existentes. Módulos y tareas son propuestas para G2. Diseño/contrato pendiente G2 en todas las filas. No hay PR de implementación, SHA probado ni evidencia de producto.
| AC | Requisitos propuestos | Tarea / módulo previsto | Prueba prevista | Resultado esperado | Observado / cumple | PR / SHA / evidencia |
|---|---|---|---|---|---|---|
| AC-01-01 | RF01, RNF01 | TASK-01 / auth / policy | T-AC-01-01 / API + PostgreSQL + seguridad | Sin credenciales, consultar solicitudes retorna HTTP 401. | No ejecutado / pendiente | Pendiente |
| AC-01-02 | RF02, RNF01 | TASK-01 / auth / policy | T-AC-01-02 / API + PostgreSQL + seguridad | Un usuario autenticado de ORG-A consulta ID de ORG-B y recibe 404 sin revelar datos. | No ejecutado / pendiente | Pendiente |
| AC-01-03 | RF02, RNF01 | TASK-01 / auth / policy | T-AC-01-03 / API + PostgreSQL + seguridad | Auditor intenta mutar solicitud de su organización y obtiene 403 sin modificación. | No ejecutado / pendiente | Pendiente |
| AC-01-04 | RF01 | TASK-01 / auth / policy | T-AC-01-04 / API + PostgreSQL + seguridad | Iniciar sesión con contraseña sembrada válida permite operar según el rol; cerrar sesión revoca acceso. | No ejecutado / pendiente | Pendiente |
| AC-01-05 | RF01, RNF01 | TASK-01 / auth / policy | T-AC-01-05 / API + PostgreSQL + seguridad | El seed produce ocho cuentas distintas, cuatro por organización; la base contiene hashes y ninguna contraseña en claro. | No ejecutado / pendiente | Pendiente |
| AC-01-06 | RF01, RNF01 | TASK-01 / auth / policy | T-AC-01-06 / API + PostgreSQL + seguridad | Credenciales erróneas o cuenta inexistente retornan el mismo error 401, sin crear sesión ni revelar existencia de cuenta. | No ejecutado / pendiente | Pendiente |
| AC-01-07 | RF02, RNF01 | TASK-01 / auth / policy | T-AC-01-07 / API + PostgreSQL + seguridad | Alterar rol u organización en el cuerpo no concede permisos: identidad del servidor prevalece y no cambia el recurso. | No ejecutado / pendiente | Pendiente |
| AC-01-08 | RF01, RNF01 | TASK-01 / auth / policy | T-AC-01-08 / API + PostgreSQL + seguridad | Tras logout, reutilizar la sesión retorna 401; comprobar revocación persistida y ausencia de cambios de negocio. | No ejecutado / pendiente | Pendiente |
| AC-01-09 | RF02, RNF01 | TASK-01 / auth / policy | T-AC-01-09 / API + PostgreSQL + seguridad | Realizar dos lecturas y dos escrituras cruzadas ORG-A/ORG-B: 404 sin filtrar datos ni alterar solicitudes, reservas o eventos de éxito. | No ejecutado / pendiente | Pendiente |
| AC-01-10 | RF01, RNF01, RNF06 | TASK-01 / auth / policy | T-AC-01-10 / API + PostgreSQL + seguridad | Inspeccionar respuestas y logs de login, errores y consultas: no contienen contraseña, hash ni token. | No ejecutado / pendiente | Pendiente |
| AC-02-01 | RF03 | TASK-02 / requests / frontend | T-AC-02-01 / dominio + API + PostgreSQL + E2E | Se guarda borrador con 1–10 SKU activos distintos y cantidades enteras 1–500. | No ejecutado / pendiente | Pendiente |
| AC-02-02 | RF03 | TASK-02 / requests / frontend | T-AC-02-02 / dominio + API + PostgreSQL + E2E | Enviar línea con cantidad 0 retorna 422 sin solicitud enviada ni cambio de inventario. | No ejecutado / pendiente | Pendiente |
| AC-02-03 | RF04 | TASK-02 / requests / frontend | T-AC-02-03 / dominio + API + PostgreSQL + E2E | Solo el dueño edita BORRADOR; edición tras envío retorna 409 sin cambios. | No ejecutado / pendiente | Pendiente |
| AC-02-04 | RF03 | TASK-02 / requests / frontend | T-AC-02-04 / dominio + API + PostgreSQL + E2E | Enviar con fecha anterior a la fecha del envío retorna 422 sin transición. | No ejecutado / pendiente | Pendiente |
| AC-02-05 | RF03 | TASK-02 / requests / frontend | T-AC-02-05 / dominio + API + PostgreSQL + E2E | Crear borrador válido retorna 201: cabecera y todas las líneas persisten con código único y dueño/organización del servidor; inventario no cambia. | No ejecutado / pendiente | Pendiente |
| AC-02-06 | RF03 | TASK-02 / requests / frontend | T-AC-02-06 / dominio + API + PostgreSQL + E2E | Para 0 y 11 líneas, SKU duplicado/inactivo/ajeno, cantidades 0/501/decimal/string: 422, sin cabecera o líneas parciales ni evento de éxito. | No ejecutado / pendiente | Pendiente |
| AC-02-07 | RF03 | TASK-02 / requests / frontend | T-AC-02-07 / dominio + API + PostgreSQL + E2E | Aceptar límites 1 y 10 líneas y cantidades 1 y 500; rechazar propiedades privilegiadas sin reasignar dueño/organización/código. | No ejecutado / pendiente | Pendiente |
| AC-02-08 | RF04 | TASK-02 / requests / frontend | T-AC-02-08 / dominio + API + PostgreSQL + E2E | Enviar hoy o fecha futura válida retorna 200 y persiste ENVIADA con un evento; existencia y reservas no cambian. | No ejecutado / pendiente | Pendiente |
| AC-02-09 | RF04 | TASK-02 / requests / frontend | T-AC-02-09 / dominio + API + PostgreSQL + E2E | Editar solicitud de otro dueño de la misma organización retorna 403 según propuesta CL-09; conservar cabecera, líneas y estado. | No ejecutado / pendiente | Pendiente |
| AC-02-10 | RF04 | TASK-02 / requests / frontend | T-AC-02-10 / dominio + API + PostgreSQL + E2E | Reenviar ENVIADA o editar estado posterior retorna 409, sin segundo evento de envío ni cambios de inventario. | No ejecutado / pendiente | Pendiente |
| AC-02-11 | RNF05 | TASK-02 / requests / frontend | T-AC-02-11 / dominio + API + PostgreSQL + E2E | Formulario operable con teclado, etiquetas asociadas, errores vinculados a campos y foco visible a 390 y 1366 px; prueba manual/E2E pendiente. | No ejecutado / pendiente | Pendiente |
| AC-03-01 | RF05, RNF02 | TASK-03 / inventory / transitions / idempotency | T-AC-03-01 / PostgreSQL + concurrencia + reloj controlado | Reservar solicitud ENVIADA con todas las líneas disponibles reserva todas dentro de una transacción. | No ejecutado / pendiente | Pendiente |
| AC-03-02 | RF05, RNF02 | TASK-03 / inventory / transitions / idempotency | T-AC-03-02 / PostgreSQL + concurrencia + reloj controlado | Una línea insuficiente produce 409 y estado, reservas y stock quedan inalterados. | No ejecutado / pendiente | Pendiente |
| AC-03-03 | RF05, RNF02 | TASK-03 / inventory / transitions / idempotency | T-AC-03-03 / PostgreSQL + concurrencia + reloj controlado | Dos reservas concurrentes sobre stock insuficiente nunca se confirman ambas. | No ejecutado / pendiente | Pendiente |
| AC-03-04 | RF07 | TASK-03 / inventory / transitions / idempotency | T-AC-03-04 / PostgreSQL + concurrencia + reloj controlado | Repetir clave y contenido de reserva retorna resultado original sin efecto adicional; cambiar contenido con misma clave retorna 409. | No ejecutado / pendiente | Pendiente |
| AC-03-05 | RF04, RF06, RNF02 | TASK-03 / inventory / transitions / idempotency | T-AC-03-05 / PostgreSQL + concurrencia + reloj controlado | Cancelar RESERVADA libera reserva sin cambiar existencia; despachar descuenta físico y reservado; entregar no cambia ninguno. | No ejecutado / pendiente | Pendiente |
| AC-03-06 | RF04, RF06, RNF02 | TASK-03 / inventory / transitions / idempotency | T-AC-03-06 / PostgreSQL + concurrencia + reloj controlado | Cancelar ENVIADA persiste CANCELADA y evento sin cambiar físico o reservado; repetir cancelación retorna 409 sin efectos adicionales. | No ejecutado / pendiente | Pendiente |
| AC-03-07 | RF04, RNF02 | TASK-03 / inventory / transitions / idempotency | T-AC-03-07 / PostgreSQL + concurrencia + reloj controlado | Para cada transición no permitida retorna 409: comparar snapshots completos de solicitud, stock, reservas y auditoría de éxito antes/después. | No ejecutado / pendiente | Pendiente |
| AC-03-08 | RF05, RNF02 | TASK-03 / inventory / transitions / idempotency | T-AC-03-08 / PostgreSQL + concurrencia + reloj controlado | Con físico=10 y dos solicitudes de 7 del mismo SKU, reservas concurrentes dan una confirmación y un 409; físico=10, reservado=7 y un evento de reserva. | No ejecutado / pendiente | Pendiente |
| AC-03-09 | RF05, RNF02 | TASK-03 / inventory / transitions / idempotency | T-AC-03-09 / PostgreSQL + concurrencia + reloj controlado | Provocar fallo de auditoría antes del commit revierte solicitud, stock y resultado idempotente; no existe reserva sin su evento. | No ejecutado / pendiente | Pendiente |
| AC-03-10 | RF07 | TASK-03 / inventory / transitions / idempotency | T-AC-03-10 / PostgreSQL + concurrencia + reloj controlado | Repetir clave con mismo ámbito/contenido a 23h59m devuelve respuesta original y conserva un único evento; cambiar ID/cuerpo con esa clave retorna 409. | No ejecutado / pendiente | Pendiente |
| AC-03-11 | RF07 | TASK-03 / inventory / transitions / idempotency | T-AC-03-11 / PostgreSQL + concurrencia + reloj controlado | A las 24h la clave expira según CL-11; nueva ejecución revalida estado y no duplica reserva de solicitud ya RESERVADA. Conservación histórica no habilita replay activo. | No ejecutado / pendiente | Pendiente |
| AC-03-12 | RF07 | TASK-03 / inventory / transitions / idempotency | T-AC-03-12 / PostgreSQL + concurrencia + reloj controlado | La misma clave textual en distinta organización o usuario no filtra respuesta original ni comparte efectos; ausencia de clave de reserva retorna 422 sin escritura. | No ejecutado / pendiente | Pendiente |
| AC-03-13 | RF04, RF06, RNF02 | TASK-03 / inventory / transitions / idempotency | T-AC-03-13 / PostgreSQL + concurrencia + reloj controlado | Despachar dos veces retorna éxito y luego 409: físico y reservado disminuyen una sola vez; entrega cambia únicamente estado y auditoría. | No ejecutado / pendiente | Pendiente |
| AC-04-01 | RF08 | TASK-04 / queries / audit | T-AC-04-01 / API + PostgreSQL; rendimiento para AC-04-09 | El solicitante recibe únicamente sus solicitudes y la paginación no excede 100. | No ejecutado / pendiente | Pendiente |
| AC-04-02 | RF08 | TASK-04 / queries / audit | T-AC-04-02 / API + PostgreSQL; rendimiento para AC-04-09 | Filtros de estado, prioridad y zona y tablero respetan organización. | No ejecutado / pendiente | Pendiente |
| AC-04-03 | RF09, RNF06 | TASK-04 / queries / audit | T-AC-04-03 / API + PostgreSQL; rendimiento para AC-04-09 | Cada transición exitosa genera evento con usuario, organización, acción, UTC y correlationId en la misma unidad consistente. | No ejecutado / pendiente | Pendiente |
| AC-04-04 | RF09 | TASK-04 / queries / audit | T-AC-04-04 / API + PostgreSQL; rendimiento para AC-04-09 | No hay endpoint que permita editar ni borrar auditoría; acciones rechazadas no simulan transición exitosa. | No ejecutado / pendiente | Pendiente |
| AC-04-05 | RF08 | TASK-04 / queries / audit | T-AC-04-05 / API + PostgreSQL; rendimiento para AC-04-09 | pageSize 0/101 y page inválida retornan 422; página fuera de resultados devuelve 200 y lista vacía sin datos ajenos. | No ejecutado / pendiente | Pendiente |
| AC-04-06 | RF08 | TASK-04 / queries / audit | T-AC-04-06 / API + PostgreSQL; rendimiento para AC-04-09 | Combinar estado/prioridad/zona devuelve solo coincidencias autorizadas; total y tablero coinciden con consultas de base en el mismo alcance. | No ejecutado / pendiente | Pendiente |
| AC-04-07 | RF09, RNF06 | TASK-04 / queries / audit | T-AC-04-07 / API + PostgreSQL; rendimiento para AC-04-09 | Correlacionar una transición con respuesta, log sanitizado y evento persistido: mismo correlationId, actor y organización correctos, fecha UTC. | No ejecutado / pendiente | Pendiente |
| AC-04-08 | RF09 | TASK-04 / queries / audit | T-AC-04-08 / API + PostgreSQL; rendimiento para AC-04-09 | Intentar modificar o borrar eventos mediante HTTP no altera ninguna fila de auditoría; comparar contenido antes/después. | No ejecutado / pendiente | Pendiente |
| AC-04-09 | RNF03, RNF04 | TASK-04 / queries / audit | T-AC-04-09 / API + PostgreSQL; rendimiento para AC-04-09 | Medir con 2.000 solicitudes y 20 VU: 5s de calentamiento excluidos y 60s medidos, p95 por endpoint ≤700ms y errores <1%; conservar salida real. | No ejecutado / pendiente | Pendiente |
| AC-04-10 | RF08 | TASK-04 / queries / audit | T-AC-04-10 / API + PostgreSQL; rendimiento para AC-04-09 | Inventario consultable solo en alcance permitido CL-09: muestra físico, reservado y disponible consistentes sin exponer otra organización. | No ejecutado / pendiente | Pendiente |
| AC-05-01 | RF10 | TASK-05 / ai-adapter / review | T-AC-05-01 / contrato + adaptador simulado + API + PostgreSQL | El adaptador recibe máximo 20 solicitudes ENVIADA de la organización y solo seis campos autorizados. | No ejecutado / pendiente | Pendiente |
| AC-05-02 | RF11 | TASK-05 / ai-adapter / review | T-AC-05-02 / contrato + adaptador simulado + API + PostgreSQL | La salida con ID ajeno, duplicado o propiedad extra es inválida y provoca fallback controlado. | No ejecutado / pendiente | Pendiente |
| AC-05-03 | RF12 | TASK-05 / ai-adapter / review | T-AC-05-03 / contrato + adaptador simulado + API + PostgreSQL | Timeout a 5 s o fallo del proveedor genera resumen determinista sin bloquear flujo principal. | No ejecutado / pendiente | Pendiente |
| AC-05-04 | RF11, RNF06 | TASK-05 / ai-adapter / review | T-AC-05-04 / contrato + adaptador simulado + API + PostgreSQL | Solo coordinador aprueba borrador; se audita aprobación sin modificar inventario ni prioridad. | No ejecutado / pendiente | Pendiente |
| AC-05-05 | RF10, RNF01 | TASK-05 / ai-adapter / review | T-AC-05-05 / contrato + adaptador simulado + API + PostgreSQL | Con 21 ENVIADA y solicitudes de otra organización, capturar payload del adaptador: exactamente 20 autorizadas y solo whitelist; cero nombres/tokens/hashes. | No ejecutado / pendiente | Pendiente |
| AC-05-06 | RF11 | TASK-05 / ai-adapter / review | T-AC-05-06 / contrato + adaptador simulado + API + PostgreSQL | Para summary 601, seis recomendaciones, reason 241, evidenceFields 0/4 o clave no permitida: fallback con origen y motivo sanitizados; no guardar salida inválida como válida. | No ejecutado / pendiente | Pendiente |
| AC-05-07 | RF11 | TASK-05 / ai-adapter / review | T-AC-05-07 / contrato + adaptador simulado + API + PostgreSQL | Aceptar límites 600/5/240 y 1–3 evidenceFields solo si IDs únicos pertenecen a las solicitudes enviadas y evidencias son comprobables. | No ejecutado / pendiente | Pendiente |
| AC-05-08 | RF11, RNF01 | TASK-05 / ai-adapter / review | T-AC-05-08 / contrato + adaptador simulado + API + PostgreSQL | Inyectar instrucciones en observaciones y HTML en salida: no se ejecutan instrucciones, herramientas ni markup; no cambian inventario, estado, prioridad o permisos. | No ejecutado / pendiente | Pendiente |
| AC-05-09 | RF12 | TASK-05 / ai-adapter / review | T-AC-05-09 / contrato + adaptador simulado + API + PostgreSQL | Proveedor responde después de 5s, falla o no está configurado: abortar espera a 5s con tolerancia instrumental declarada y generar fallback repetible para igual snapshot. | No ejecutado / pendiente | Pendiente |
| AC-05-10 | RF10 | TASK-05 / ai-adapter / review | T-AC-05-10 / contrato + adaptador simulado + API + PostgreSQL | Sin solicitudes ENVIADA: resumen determinista de conjunto vacío, recomendaciones vacías y sin llamada innecesaria al proveedor. | No ejecutado / pendiente | Pendiente |
| AC-05-11 | RF11, RNF01 | TASK-05 / ai-adapter / review | T-AC-05-11 / contrato + adaptador simulado + API + PostgreSQL | Aprobar borrador ajeno retorna 404; rol distinto a coordinador retorna 403; no altera borrador, stock ni prioridades. | No ejecutado / pendiente | Pendiente |
| AC-05-12 | RF11, RNF06 | TASK-05 / ai-adapter / review | T-AC-05-12 / contrato + adaptador simulado + API + PostgreSQL | Aprobación válida persiste actor, UTC y evento junto al borrador aprobado; snapshot de solicitudes e inventario permanece idéntico. | No ejecutado / pendiente | Pendiente |
| AC-05-13 | RF12, RNF06 | TASK-05 / ai-adapter / review | T-AC-05-13 / contrato + adaptador simulado + API + PostgreSQL | Adaptador de reproducción identifica origen reproducción, fixture y versión; no reportar acceso a LLM real sin evidencia. | No ejecutado / pendiente | Pendiente |

## Dependencias y responsables propuestos
| Tarea | Dependencias obligatorias | Responsable principal propuesto |
|---|---|---|
| TASK-01 Identidad | SPEC-01 aprobada + G2 revisado | José Conde; revisión de otro integrante |
| TASK-02 Solicitudes/UI | TASK-01 + SPEC-02 aprobada | José Conde; Carolina revisa trazabilidad |
| TASK-03 Inventario | TASK-01/02 + SPEC-03 aprobada | Edier; revisión de otro integrante |
| TASK-04 Consultas/auditoría | SPEC-04 aprobada; auditoría debe integrarse en TASK-02/03 | Edier; revisión de otro integrante |
| TASK-05 IA | TASK-01/02/04 + SPEC-05 aprobada | José Vega; revisión de otro integrante |
| Validación y CI | Contratos G2 y módulos revisados | José Vega; todos aportan pruebas reales |

Carolina coordina cotejo de requisitos/SPEC. Ninguna fila acredita autoría, trabajo o revisión ya realizada. Registrar contribuciones reales por commit/PR/prueba cuando existan.
