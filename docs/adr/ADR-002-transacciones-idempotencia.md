# ADR-002 Estrategia transaccional, concurrencia e idempotencia

Formato del anexo oficial `06_adr-template.md`.

- **Estado:** propuesto
- **Fecha y versión:** 2026-10-14, v0.1 (G2)
- **Autor:** Castro Edier, con asistencia de IA (registro AI-005)
- **Depende de:** ADR-001 (Laravel + PostgreSQL, monolito modular; módulos Inventory, Requests, Idempotency y Audit) y contrato `contracts/openapi.yaml` (rama `docs/g2-architecture`, `e63ebf5`)

## Contexto y restricciones

- **RF05:** la reserva cubre todas las líneas o ninguna; disponible = físico − reservado; las tres cantidades nunca negativas; solo desde ENVIADA; si falta stock en una línea, 409 sin cambios.
- **RF06:** cancelar RESERVADA libera exactamente su reserva; despachar baja físico y reservado; ENTREGADA y cancelar ENVIADA no tocan inventario.
- **RF07:** reserva transaccional segura ante concurrencia, con `Idempotency-Key`; misma clave, usuario, organización, operación y contenido repiten el resultado; contenido distinto, 409; resultado conservado al menos 24 h.
- **Condiciones de consistencia (§3):** dos reservas que compiten por stock insuficiente no pueden tener éxito ambas; un fallo antes del commit no deja reserva parcial; una segunda reserva con otra clave devuelve 409; la clave se procesa después de autenticar y autorizar.
- **RF09:** estado, inventario y evento de auditoría se persisten de forma consistente.
- **RNF02:** verificado contra una base real (PostgreSQL), no con mocks.
- Oráculos oficiales (`acceptance-oracles.md`): con KIT-ALIMENTO de ORG-A en físico 10, reservar A-001 (7) deja 10/7/3; dos reservas simultáneas de 7 dejan una exitosa, otra 409 y 10/7/3.

## Alternativas consideradas

**Control de concurrencia del stock**

| Opción | Cómo funciona | Ventajas | Desventajas |
|---|---|---|---|
| **A. Bloqueo pesimista** | `SELECT … FOR UPDATE` sobre la solicitud y las filas de stock en orden de SKU, en READ COMMITTED (Laravel `lockForUpdate()`) | Correcto con varias líneas; fácil de razonar, explicar y probar; sin reintentos en el caso normal | Las reservas del mismo SKU esperan en fila; requiere orden fijo de bloqueo para evitar deadlocks |
| B. UPDATE condicional | `UPDATE … SET reservado = reservado + n WHERE fisico - reservado >= n` y revisar filas afectadas | Sin bloqueos explícitos | Con varias líneas hay que deshacer las ya actualizadas; más difícil de garantizar «todas o ninguna» |
| C. Bloqueo optimista | Columna `version`; si cambió, reintentar | Sin esperas | Muchos reintentos cuando hay competencia, que es justo el caso a probar |
| D. SERIALIZABLE + reintentos | PostgreSQL aborta transacciones en conflicto (40001) | Garantía fuerte | Reintentos obligatorios en todo el código; errores más difíciles de diagnosticar |

**Almacén de idempotencia**

| Opción | Ventajas | Desventajas |
|---|---|---|
| **Tabla en PostgreSQL dentro de la misma transacción** | El resultado se guarda si y solo si se confirman los efectos; sin servicios extra | Requiere limpieza periódica |
| Caché externa (Redis) | Rápida | No es transaccional con la base: podría guardar un resultado sin efectos o al revés; servicio adicional |
| Middleware fuera de la transacción | Genérico | Mismo problema de consistencia que Redis |

## Criterios de elección

1. Cumplir los oráculos oficiales en cualquier orden de llegada de las peticiones.
2. Atomicidad de estado, inventario, auditoría y resultado idempotente.
3. Que el mecanismo se pueda probar de forma determinista contra PostgreSQL real.
4. Mínima complejidad.

## Decisión y justificación

Se adoptan el **bloqueo pesimista (A)** y la **tabla de idempotencia en la misma transacción**.

### Reserva (`POST /api/requests/{requestId}/reserve`)

| Paso | Acción | Resultado si falla |
|---|---|---|
| 1 | Autenticar; resolver la solicitud **dentro de la organización** del usuario | 401 / 404 (no se guarda) |
| 2 | Autorizar rol Coordinador | 403 (no se guarda) |
| 3 | Validar cuerpo `{}` y cabecera `Idempotency-Key` (1–128 caracteres) | 422 (no se guarda) |
| 4 | `BEGIN` (READ COMMITTED) y `SET LOCAL lock_timeout = '3s'` | — |
| 5 | `pg_advisory_xact_lock(hash(org, usuario, 'reserve', clave))`: serializa peticiones con la **misma** clave | — |
| 6 | Buscar el registro de idempotencia del ámbito. Si existe y no venció: misma huella → devolver código y cuerpo guardados sin efectos; huella distinta → 409 | Replay o 409 (no se guarda de nuevo) |
| 7 | `SELECT … FOR UPDATE` de la solicitud | — |
| 8 | Si el estado no es ENVIADA → 409 | 409 guardado en el registro de idempotencia |
| 9 | `SELECT … FOR UPDATE` de las filas de inventario de sus SKU, **ordenadas por SKU** | — |
| 10 | Comprobar, para cada línea, SKU activo y `fisico − reservado ≥ cantidad` | 409 guardado; ninguna fila modificada |
| 11 | `reservado += cantidad` por línea; estado RESERVADA; evento de auditoría; registro de idempotencia con 200 y cuerpo | — |
| 12 | `COMMIT` | Cualquier excepción antes: `ROLLBACK` total y 500 con correlationId, sin registro (H-E01) |

Detalles:

- **Huella:** SHA-256 del JSON canónico `{"operation":"reserve","requestId":…,"body":{}}` con claves ordenadas.
- **Ámbito de la clave:** `UNIQUE (organization_id, user_id, operation, idempotency_key)`. La misma clave en otra organización o de otro usuario es independiente (AC-03-12).
- **Retención:** `expires_at = created_at + 24 h`. Se repite el resultado mientras `now < expires_at`. Al vencer, el registro se reemplaza y la operación se reevalúa (AC-03-11, supuesto CL-11). Un comando programado borra los registros con más de 7 días.
- **Respuestas guardadas (CL-04):** 200 y 409 de negocio. No se guardan 401, 403, 404, 422 ni 500, que se pueden reintentar con la misma clave. Consecuencia (H-E03): un 409 por falta de stock se repite durante 24 h; para reintentar después de liberar stock, el cliente usa una clave nueva.
- **Segunda reserva con otra clave:** el paso 8 la rechaza con 409 porque el estado ya no es ENVIADA (AC-03-14).
- **Por qué funciona la competencia en READ COMMITTED:** cuando la segunda transacción obtiene el bloqueo de la fila de stock, PostgreSQL le entrega la versión ya confirmada por la primera (reservado = 7), así que su comprobación ve disponible 3 y responde 409.

### Otras transiciones

Despachar, entregar, cancelar y enviar usan la misma transacción, sin `Idempotency-Key` (RF07 solo la exige para reservar): bloquear la solicitud, comprobar estado y rol, bloquear el stock en orden de SKU si se modifica, actualizar, registrar el evento y confirmar.

| Transición | Actor (§2) | Efecto en stock |
|---|---|---|
| Enviar BORRADOR → ENVIADA | Solicitante dueño | Ninguno |
| Reservar ENVIADA → RESERVADA | Coordinador | `reservado += cantidad` |
| Despachar RESERVADA → DESPACHADA | Bodega | `fisico −= cantidad` y `reservado −= cantidad` |
| Entregar DESPACHADA → ENTREGADA | Coordinador | Ninguno |
| Cancelar ENVIADA → CANCELADA | Solicitante dueño | Ninguno |
| Cancelar RESERVADA → CANCELADA | Solicitante dueño | `reservado −= cantidad` |
| Cualquier otra | — | 409 sin cambios |

Una carrera entre despacho y cancelación de la misma reserva (H-E02) se resuelve con el bloqueo de la solicitud: la primera gana y la segunda ve el nuevo estado y recibe 409.

### Orden de bloqueo y deadlocks

Siempre: bloqueo consultivo de la clave (solo en reservas) → fila de la solicitud → filas de stock por SKU ascendente. Con un orden único no se forman ciclos. Como defensa, `DB::transaction($callback, 3)` de Laravel reintenta hasta 3 veces si PostgreSQL detecta un deadlock (40P01). Si se agota el `lock_timeout`, la respuesta es 500 controlado, sin guardar, y se puede reintentar con la misma clave.

### Restricciones en la base (defensa en profundidad)

- `CHECK (fisico >= 0 AND reservado >= 0 AND reservado <= fisico)` en inventario: si un error de código intentara dejar stock negativo, la base lo rechaza y la transacción se revierte.
- `CHECK (cantidad BETWEEN 1 AND 500)` en las líneas y `CHECK` del estado con los seis valores.
- Llaves foráneas compuestas con `organization_id` entre solicitud, líneas e inventario: una línea no puede apuntar a stock de otra organización.
- Auditoría append-only: un trigger rechaza `UPDATE` y `DELETE` en `audit_events`.

Detalle en [datos](../architecture/datos.md), [estados](../architecture/estados.md) y [secuencia de reserva concurrente](../architecture/secuencia-reserva-concurrente.md).

## Consecuencias y riesgos

- (+) Los oráculos se cumplen sin importar el orden de llegada; los efectos y el resultado idempotente se confirman juntos.
- (+) Las restricciones de la base detienen errores de código antes de que corrompan el inventario.
- (−) Las reservas del mismo SKU se procesan una a la vez; aceptable para el volumen del caso. RNF03 mide listados y tablero, no reservas.
- (−) Las restricciones y el trigger dependen de PostgreSQL; las pruebas deben usar PostgreSQL real (ver `docs/testing/estrategia-pruebas.md`).
- Riesgo: un bloqueo mantenido demasiado tiempo → `lock_timeout` de 3 s y transacciones cortas, sin llamadas externas dentro (la IA nunca se invoca dentro de una transacción de inventario, ADR-001).

## Coherencia con el contrato

- `Idempotency-Key` y la política 200/409 guardados coinciden con `contracts/openapi.yaml`.
- Observación para el contrato (H-E05): las descripciones de `dispatch`, `deliver` y `cancel` terminan con «Misma reserva con otra clave devuelve 409», que no aplica porque esas operaciones no usan clave. Conviene quitar la frase.

## RF y AC afectados

RF02, RF04, RF05, RF06, RF07, RF09, RNF02; AC-03-01 a AC-03-16, AC-04-03, AC-04-08; hallazgos H-E01 a H-E03.

## Verificación de la decisión

Pruebas previstas contra PostgreSQL real (oráculos escritos antes):

| Prueba | AC | Cómo |
|---|---|---|
| Reserva exitosa 10/7/3 y un evento | AC-03-01 | Seed restablecido; consulta directa a la base |
| Línea insuficiente sin cambios | AC-03-02 | Snapshot antes y después |
| Dos reservas simultáneas | AC-03-03, 03-08 | Dos procesos reales con barrera, ≥ 20 repeticiones; siempre una 200 y una 409, 10/7/3 y un evento |
| Replay y conflicto de clave | AC-03-04, 03-10 | Misma respuesta y un solo evento; otro contenido → 409 |
| Expiración a 24 h | AC-03-11 | Reloj controlado (`travelTo`) |
| Ámbito de la clave | AC-03-12 | Misma clave en ORG-B no devuelve la respuesta de ORG-A |
| Segunda reserva con otra clave | AC-03-14 | 409 y reservado sin cambios |
| Fallo antes del commit | AC-03-09 | Excepción inyectada al escribir el evento: estado, stock y registro de idempotencia sin cambios |
| Carrera despacho / cancelación | H-E02 | Dos procesos; estado final coherente con el que ganó |
| Restricción de stock | RNF02 | Un `UPDATE` directo que deje `reservado > fisico` falla |
| Auditoría inmutable | AC-04-08 | `UPDATE` o `DELETE` sobre `audit_events` falla |

## Autor y revisión humana

- Autor: Castro Edier (asistido por IA, AI-005).
- Revisión humana: **pendiente** de una persona distinta del autor en la revisión G2.
