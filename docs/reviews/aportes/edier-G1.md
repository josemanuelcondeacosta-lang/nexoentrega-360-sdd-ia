# Aporte G1 — Castro Edier: inventario, transacciones e idempotencia

Plantilla de `docs/team/preparacion-G1.md`. Escenarios **previstos**: ninguna prueba de producto se ha ejecutado porque la aplicación no existe.

- **Nombre / cuenta GitHub:** Castro Edier (202311254074) / `eblanquiceth13-cmyk`
- **Autor del artefacto revisado / asistencia IA:** SPEC-03 0.3-draft, elaborada con asistencia de IA a solicitud de Edier (AI-001). Este aporte también se preparó con asistencia de IA (AI-002).
- **SHA completo revisado:** `560c9cbe7972fcfe2258406ca7ed5f094b5a50e3`
- **Fecha UTC:** 2026-10-14T05:15Z
- **Fuentes disponibles:** OF-01 `Examen_Estudiantes_SDD_IA.pdf` §2 (p. 3), §3 RF04–RF07 y condiciones de consistencia (p. 4), §11 oráculos independientes (p. 12); anexos `acceptance-oracles.md` y `fixture-scenarios.json`. **Fuentes pendientes:** ninguna para SPEC-03; las preguntas CL-16 a CL-18 no afectan transiciones ni stock.
- **SPEC / AC revisados:** SPEC-03 AC-03-01 a AC-03-16; vínculos AC-02-08 (enviar no reserva), AC-04-03 (evento de auditoría) y AC-01-09 (aislamiento).

## Comprobaciones documentales

| Comprobación | Resultado |
|---|---|
| Disponible = físico − reservado y las tres cantidades no negativas tras cada transición | Cumple en el texto: reservar solo suma si disponible ≥ cantidad en todas las líneas; despachar resta la misma cantidad de físico y reservado (que ya la contenía); cancelar RESERVADA resta solo de reservado |
| Ningún rechazo de negocio (409, 403, 404, 422) deja cambios parciales | Cumple: los rechazos ocurren antes de escribir o dentro de la transacción con reversión total (proceso, pasos 1–5 y 9) |
| La auditoría confirma o revierte junto con el cambio | Cumple: evento y resultado idempotente en la misma transacción (paso 9); AC-03-09 lo prueba con fallo inducido |
| Actores por transición iguales a la matriz oficial (§2) | Cumple tras D-01 y D-02: Coordinador reserva y entrega, Bodega despacha, Solicitante dueño cancela |
| Cantidades de los AC iguales a `acceptance-oracles.md` | Cumple: 10/7/3 tras reservar, 10/0/10 tras cancelar, 3/0/3 tras despachar, una reserva exitosa en la competencia |
| La clave se procesa después de autenticar y autorizar | Cumple: proceso paso 2 y AC-03-15 |

## Escenarios previstos

Datos iniciales: seed restablecido según `fixture-scenarios.json` antes de cada escenario (ORG-A: KIT-ALIMENTO 10, KIT-HIGIENE 8, KIT-AGUA 20; ORG-B: KIT-ALIMENTO 4, KIT-HIGIENE 5, KIT-AGUA 6; reservado 0 en todos; A-001 y A-002 ENVIADA con 7 KIT-ALIMENTO; B-001 ENVIADA con 2 KIT-HIGIENE). Stock expresado como físico / reservado / disponible. «Eventos» cuenta eventos de transición exitosa nuevos. K1 y K2 son Idempotency-Key distintas; t0 es el instante del reloj de prueba en que se creó K1.

| ID | AC | Precondición adicional | Actor | Acción | Respuesta esperada | Estado final | Stock final | Eventos | Fuente | Observado |
|---|---|---|---|---|---|---|---|---|---|---|
| E-01 | AC-03-01 | — | Coordinador ORG-A | Reservar A-001 con K1 | 200 | A-001 RESERVADA | KIT-ALIMENTO 10/7/3; ORG-B sin cambios | +1 | RF05; oráculo «Reserva A-001» | No ejecutado |
| E-02 | AC-03-01 (caso límite) | Solicitud ENVIADA de ORG-A con 8 KIT-HIGIENE | Coordinador ORG-A | Reservar con clave nueva | 200 (disponible exacto) | RESERVADA | KIT-HIGIENE 8/8/0 | +1 | RF05 | No ejecutado |
| E-03 | AC-03-02 | Solicitud ENVIADA con 5 KIT-AGUA y 9 KIT-HIGIENE | Coordinador ORG-A | Reservar | 409 | Sigue ENVIADA | KIT-AGUA 20/0/20; KIT-HIGIENE 8/0/8 | 0 | RF05; oráculo «línea insuficiente» | No ejecutado |
| E-04 | AC-03-08, AC-03-03 | — | Coordinador ORG-A (dos peticiones simultáneas) | Reservar A-001 con K1 y A-002 con K2 a la vez | Una 200 y otra 409, sin fijar cuál | Una RESERVADA y otra ENVIADA | KIT-ALIMENTO 10/7/3 | +1 | Condiciones de consistencia; oráculo «Dos reservas simultáneas» | No ejecutado |
| E-05 | AC-03-05 | A-001 RESERVADA (E-01) | Solicitante dueño ORG-A | Cancelar A-001 | 200 | CANCELADA | KIT-ALIMENTO 10/0/10 | +1 | RF06; oráculo «Cancelación» | No ejecutado |
| E-06 | AC-03-06 | — | Solicitante dueño ORG-A | Cancelar A-002 y repetir la cancelación | 200 y luego 409 | CANCELADA | KIT-ALIMENTO 10/0/10 (sin cambios) | +1 | RF04, RF06 | No ejecutado |
| E-07 | AC-03-13 | A-001 RESERVADA | Bodega ORG-A | Despachar A-001 dos veces | 200 y luego 409 | DESPACHADA | KIT-ALIMENTO 3/0/3 (una sola resta) | +1 | RF06; oráculo «Despacho» | No ejecutado |
| E-08 | AC-03-05, AC-03-13 | A-001 DESPACHADA (E-07) | Coordinador ORG-A | Registrar entrega | 200 | ENTREGADA | KIT-ALIMENTO 3/0/3 (sin cambios) | +1 | RF06 | No ejecutado |
| E-09 | AC-03-09 | Fallo inducido al escribir el evento antes del commit | Coordinador ORG-A | Reservar A-001 con K1 | Error controlado (código por fijar, ver H-E01) | Sigue ENVIADA | KIT-ALIMENTO 10/0/10 | 0; sin registro idempotente | Condiciones de consistencia | No ejecutado |
| E-10 | AC-03-04 | A-001 reservada con K1 (E-01) | Coordinador ORG-A | Repetir K1 con el mismo contenido | Mismo código y cuerpo que E-01 | RESERVADA | KIT-ALIMENTO 10/7/3 | 0 | RF07 | No ejecutado |
| E-11 | AC-03-04, AC-03-10 | A-001 reservada con K1 | Coordinador ORG-A | Repetir K1 con requestId A-002 | 409 | A-002 sigue ENVIADA | KIT-ALIMENTO 10/7/3 | 0 | RF07 | No ejecutado |
| E-12 | AC-03-10, AC-03-11 | A-001 reservada con K1 en t0 | Coordinador ORG-A | Repetir K1 igual en t0 + 23 h 59 min y en t0 + 24 h | Respuesta original; después 409 por estado | RESERVADA | KIT-ALIMENTO 10/7/3 | 0 | RF07 (≥ 24 h); supuesto CL-11 | No ejecutado |
| E-13 | AC-03-14 | A-001 reservada con K1 | Coordinador ORG-A | Reservar A-001 con K2 | 409 | RESERVADA | KIT-ALIMENTO 10/7/3 | 0 | Condiciones de consistencia | No ejecutado |
| E-14 | AC-03-12 | A-001 reservada con K1 | Coordinador ORG-B | Reservar B-001 usando el texto K1 | 200 propio de ORG-B; nunca la respuesta de ORG-A | B-001 RESERVADA | ORG-B KIT-HIGIENE 5/2/3; ORG-A 10/7/3 | +1 (ORG-B) | RF07, RF02 | No ejecutado |
| E-15 | AC-03-12 | — | Coordinador ORG-A | Reservar A-001 sin Idempotency-Key | 422 | Sigue ENVIADA | KIT-ALIMENTO 10/0/10 | 0 | RF07; supuesto CL-11 | No ejecutado |
| E-16 | AC-03-15 | A-001 en el estado de origen de cada intento | Bodega, Coordinador y Auditor ORG-A | Bodega reserva; Coordinador cancela; Auditor despacha | 403 en cada caso; clave no almacenada | Sin cambios | Sin cambios | 0 | §2 matriz oficial | No ejecutado |
| E-17 | AC-01-09 | — | Coordinador ORG-A | Reservar B-001 de ORG-B | 404 | B-001 sigue ENVIADA | ORG-B KIT-HIGIENE 5/0/5 | 0 | RF02 | No ejecutado |

Los oráculos se escriben antes de ejecutar y se comprueban consultando la base de prueba, no solo el código HTTP (§11).

## Hallazgos y preguntas para el equipo

Son observaciones de revisión, no decisiones del cliente.

| ID | Hallazgo | Evidencia | Propuesta |
|---|---|---|---|
| H-E01 | AC-03-09 no fija el código HTTP del fallo inducido antes del commit | AC-03-09 dice «falla de forma controlada» | 500 con correlationId, sin guardar resultado idempotente (coherente con CL-04); fijarlo en el contrato G2 |
| H-E02 | No hay AC para la carrera entre despacho (Bodega) y cancelación (Solicitante) de la misma RESERVADA | El proceso bloquea la fila de la solicitud, pero ningún AC lo prueba | Añadir en 0.4 un AC: con ambas peticiones simultáneas, una tiene éxito y la otra recibe 409; stock final coherente con la que ganó (3/0/3 si gana el despacho, 10/0/10 si gana la cancelación) |
| H-E03 | Con CL-04, un 409 de negocio guardado con K1 se repite durante 24 h aunque luego se libere stock | Consecuencia de almacenar respuestas completadas | Documentarlo en el contrato G2 para que el cliente use una clave nueva al reintentar tras un 409 |
| H-E04 | Desactivación de un SKU entre envío y reserva | RF03 exige SKU activo al enviar; SPEC-03 no lo menciona | No aplica mientras no exista API para desactivar SKU (CL-06); revisar si G2 añade administración de catálogo |

## Comandos ejecutados

- Por el asistente (AI-002), sobre el SHA revisado: `python scripts/export_specs.py --check` → «5 SPEC y 68 AC validados; JSON coherentes»; `python scripts/build_coverage.py --check` → «Matriz y traza coherentes con las SPEC».
- Ejecución propia de Edier en Windows: **pendiente**; pegar aquí comando, fecha y salida real.

## Resultado y decisión

- **Resultado observado de producto:** no ejecutado.
- **Decisión por SPEC-03:** pendiente. Edier participó en la versión 0.3, así que la aprobación corresponde a otra persona (revisora propuesta: Carolina). Recomendación del aporte: atender H-E01 y H-E02 antes de aprobar.
- **Enlace a comentario o revisión GitHub:** pendiente hasta publicarlo en el PR.
- **PR ajeno revisado / SHA / comentario propio:** pendiente hasta que Vega publique su PR.
