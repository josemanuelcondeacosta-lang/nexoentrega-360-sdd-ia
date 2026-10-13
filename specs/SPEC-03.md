# SPEC-03: Inventario y transiciones

**Versión:** 0.2-draft — **no aprobada para implementar**

Fuente: misión del usuario y borrador inicial. Enunciado oficial y anexos NO recibidos. RF/RNF conservan identificadores iniciales; significado de RNF pendiente de cotejo. HTTP, roles, catálogos y campos propuestos requieren revisión.

**RF:** RF04, RF05, RF06, RF07; **RNF:** RNF02

## Historia de usuario HU-03

Como coordinador y encargado de bodega, queremos reservar, despachar y entregar solicitudes sin sobreasignar stock.

## Entradas

- ID de solicitud autorizado, operación y sesión; reserva requiere Idempotency-Key.
- Clave propuesta: 1–128 caracteres; ámbito organización/usuario/operación y hash canónico de ID+cuerpo; confirmar CL-04/11.

## Procesos

- En transacción bloquear solicitud y SKU en orden estable, comprobar estado y disponibilidad de todas las líneas antes de escribir.
- Reservar: ENVIADA → RESERVADA; aumentar reservado. Despachar: RESERVADA → DESPACHADA; disminuir físico y reservado.
- Cancelar ENVIADA o RESERVADA → CANCELADA; liberar reservado únicamente si estaba RESERVADA. Entregar DESPACHADA → ENTREGADA sin tocar stock.
- Confirmar estado, inventario, auditoría y resultado idempotente conjuntamente; rollback si una escritura falla.

## Salidas y efectos persistidos

- 200 con estado confirmado; 409 si falta stock, transición inválida o clave conflictiva; ningún efecto parcial.
- Replay durante 24 horas devuelve código y cuerpo originales sin nueva reserva ni nuevo evento.

## Reglas y supuestos

- Disponible = físico − reservado; físico ≥ reservado ≥ 0 después de cada commit.
- No transiciones desde CANCELADA o ENTREGADA; no cancelación desde DESPACHADA; confirmar con enunciado.
- Propuesta TTL: válida mientras now < createdAt+24h; expirada al alcanzar ese instante. Archivo separado conserva metadatos según CL-11.

## Límites y escenarios adversariales

- Stock exacto, una línea insuficiente, dos transacciones competidoras, doble despacho, caída antes de commit, reloj en frontera TTL.

## Criterios de aceptación

Todos son resultados ESPERADOS; ninguna prueba de producto ejecutada. Conservar snapshots de base antes/después y evidencias según matriz.

- **AC-03-01:** Reservar solicitud ENVIADA con todas las líneas disponibles reserva todas dentro de una transacción.
- **AC-03-02:** Una línea insuficiente produce 409 y estado, reservas y stock quedan inalterados.
- **AC-03-03:** Dos reservas concurrentes sobre stock insuficiente nunca se confirman ambas.
- **AC-03-04:** Repetir clave y contenido de reserva retorna resultado original sin efecto adicional; cambiar contenido con misma clave retorna 409.
- **AC-03-05:** Cancelar RESERVADA libera reserva sin cambiar existencia; despachar descuenta físico y reservado; entregar no cambia ninguno.
- **AC-03-06:** Cancelar ENVIADA persiste CANCELADA y evento sin cambiar físico o reservado; repetir cancelación retorna 409 sin efectos adicionales.
- **AC-03-07:** Para cada transición no permitida retorna 409: comparar snapshots completos de solicitud, stock, reservas y auditoría de éxito antes/después.
- **AC-03-08:** Con físico=10 y dos solicitudes de 7 del mismo SKU, reservas concurrentes dan una confirmación y un 409; físico=10, reservado=7 y un evento de reserva.
- **AC-03-09:** Provocar fallo de auditoría antes del commit revierte solicitud, stock y resultado idempotente; no existe reserva sin su evento.
- **AC-03-10:** Repetir clave con mismo ámbito/contenido a 23h59m devuelve respuesta original y conserva un único evento; cambiar ID/cuerpo con esa clave retorna 409.
- **AC-03-11:** A las 24h la clave expira según CL-11; nueva ejecución revalida estado y no duplica reserva de solicitud ya RESERVADA. Conservación histórica no habilita replay activo.
- **AC-03-12:** La misma clave textual en distinta organización o usuario no filtra respuesta original ni comparte efectos; ausencia de clave de reserva retorna 422 sin escritura.
- **AC-03-13:** Despachar dos veces retorna éxito y luego 409: físico y reservado disminuyen una sola vez; entrega cambia únicamente estado y auditoría.

## Fuente única estructurada

El bloque se exporta automáticamente; no editar JSON derivado.

```spec-json
{
  "id": "SPEC-03",
  "version": "0.2-draft",
  "title": "Inventario y transiciones",
  "requirements": {
    "rf": [
      "RF04",
      "RF05",
      "RF06",
      "RF07"
    ],
    "rnf": [
      "RNF02"
    ]
  },
  "userStory": "Como coordinador y encargado de bodega, queremos reservar, despachar y entregar solicitudes sin sobreasignar stock.",
  "inputs": [
    "ID de solicitud autorizado, operación y sesión; reserva requiere Idempotency-Key.",
    "Clave propuesta: 1–128 caracteres; ámbito organización/usuario/operación y hash canónico de ID+cuerpo; confirmar CL-04/11."
  ],
  "process": [
    "En transacción bloquear solicitud y SKU en orden estable, comprobar estado y disponibilidad de todas las líneas antes de escribir.",
    "Reservar: ENVIADA → RESERVADA; aumentar reservado. Despachar: RESERVADA → DESPACHADA; disminuir físico y reservado.",
    "Cancelar ENVIADA o RESERVADA → CANCELADA; liberar reservado únicamente si estaba RESERVADA. Entregar DESPACHADA → ENTREGADA sin tocar stock.",
    "Confirmar estado, inventario, auditoría y resultado idempotente conjuntamente; rollback si una escritura falla."
  ],
  "outputs": [
    "200 con estado confirmado; 409 si falta stock, transición inválida o clave conflictiva; ningún efecto parcial.",
    "Replay durante 24 horas devuelve código y cuerpo originales sin nueva reserva ni nuevo evento."
  ],
  "rules": [
    "Disponible = físico − reservado; físico ≥ reservado ≥ 0 después de cada commit.",
    "No transiciones desde CANCELADA o ENTREGADA; no cancelación desde DESPACHADA; confirmar con enunciado.",
    "Propuesta TTL: válida mientras now < createdAt+24h; expirada al alcanzar ese instante. Archivo separado conserva metadatos según CL-11."
  ],
  "edgeCases": [
    "Stock exacto, una línea insuficiente, dos transacciones competidoras, doble despacho, caída antes de commit, reloj en frontera TTL."
  ],
  "acceptanceCriteria": [
    {
      "id": "AC-03-01",
      "expected": "Reservar solicitud ENVIADA con todas las líneas disponibles reserva todas dentro de una transacción.",
      "status": "draft"
    },
    {
      "id": "AC-03-02",
      "expected": "Una línea insuficiente produce 409 y estado, reservas y stock quedan inalterados.",
      "status": "draft"
    },
    {
      "id": "AC-03-03",
      "expected": "Dos reservas concurrentes sobre stock insuficiente nunca se confirman ambas.",
      "status": "draft"
    },
    {
      "id": "AC-03-04",
      "expected": "Repetir clave y contenido de reserva retorna resultado original sin efecto adicional; cambiar contenido con misma clave retorna 409.",
      "status": "draft"
    },
    {
      "id": "AC-03-05",
      "expected": "Cancelar RESERVADA libera reserva sin cambiar existencia; despachar descuenta físico y reservado; entregar no cambia ninguno.",
      "status": "draft"
    },
    {
      "id": "AC-03-06",
      "expected": "Cancelar ENVIADA persiste CANCELADA y evento sin cambiar físico o reservado; repetir cancelación retorna 409 sin efectos adicionales.",
      "status": "draft"
    },
    {
      "id": "AC-03-07",
      "expected": "Para cada transición no permitida retorna 409: comparar snapshots completos de solicitud, stock, reservas y auditoría de éxito antes/después.",
      "status": "draft"
    },
    {
      "id": "AC-03-08",
      "expected": "Con físico=10 y dos solicitudes de 7 del mismo SKU, reservas concurrentes dan una confirmación y un 409; físico=10, reservado=7 y un evento de reserva.",
      "status": "draft"
    },
    {
      "id": "AC-03-09",
      "expected": "Provocar fallo de auditoría antes del commit revierte solicitud, stock y resultado idempotente; no existe reserva sin su evento.",
      "status": "draft"
    },
    {
      "id": "AC-03-10",
      "expected": "Repetir clave con mismo ámbito/contenido a 23h59m devuelve respuesta original y conserva un único evento; cambiar ID/cuerpo con esa clave retorna 409.",
      "status": "draft"
    },
    {
      "id": "AC-03-11",
      "expected": "A las 24h la clave expira según CL-11; nueva ejecución revalida estado y no duplica reserva de solicitud ya RESERVADA. Conservación histórica no habilita replay activo.",
      "status": "draft"
    },
    {
      "id": "AC-03-12",
      "expected": "La misma clave textual en distinta organización o usuario no filtra respuesta original ni comparte efectos; ausencia de clave de reserva retorna 422 sin escritura.",
      "status": "draft"
    },
    {
      "id": "AC-03-13",
      "expected": "Despachar dos veces retorna éxito y luego 409: físico y reservado disminuyen una sola vez; entrega cambia únicamente estado y auditoría.",
      "status": "draft"
    }
  ],
  "approvalStatus": "pending-human-review",
  "sourceStatus": "mission-provided-official-exam-missing",
  "userStoryId": "HU-03"
}
```

## Pendientes de revisión

- Cotejar con enunciado/anexos y resolver aclaraciones relacionadas.
- Revisar criterios, matriz de permisos y asignación RNF por una persona distinta al autor.
- Registrar revisión con SHA en docs/reviews/gate-review-G1.md.
- Código, pruebas de producto, PR revisado y evidencias: PENDIENTES; implementación bloqueada por G1.
