# SPEC-02: Creación y envío de solicitudes

**Versión:** 0.2-draft — **no aprobada para implementar**

Fuente: misión del usuario y borrador inicial. Enunciado oficial y anexos NO recibidos. RF/RNF conservan identificadores iniciales; significado de RNF pendiente de cotejo. HTTP, roles, catálogos y campos propuestos requieren revisión.

**RF:** RF03, RF04; **RNF:** RNF05

## Historia de usuario HU-02

Como solicitante, quiero crear, editar y enviar una solicitud válida, para solicitar kits de mi organización.

## Entradas

- Borrador: zona, prioridad, fecha requerida YYYY-MM-DD, observaciones y líneas {skuId, quantity}.
- Líneas: 1–10 SKU activos distintos; quantity entero 1–500. Código, dueño y organización son generados por servidor.
- Catálogo de zonas, prioridades y límite de observaciones son propuestas CL-01/02/03/07/10.

## Procesos

- Validar esquema y catálogo de la organización; persistir cabecera y líneas conjuntamente como BORRADOR.
- Editar únicamente borrador del dueño; al enviar volver a validar líneas y fecha contra fecha local propuesta America/Bogota.
- Persistir transición BORRADOR → ENVIADA y evento de auditoría en una unidad consistente.

## Salidas y efectos persistidos

- Creación: 201 con ID, código único, BORRADOR y líneas persistidas; edición/envío: 200.
- 422 para datos inválidos, 403 para dueño no autorizado según CL-09, 404 para recurso ajeno, 409 para estado incompatible; sin efectos parciales.

## Reglas y supuestos

- Enviar no reserva inventario; operaciones posteriores se definen en SPEC-03.
- Propuesta de prioridades: ALTA, MEDIA, BAJA; no considerarlas catálogo oficial hasta CL-10.
- No aceptar cambios de código, dueño u organización desde cliente.

## Límites y escenarios adversariales

- 0/1/10/11 líneas; cantidad 0/1/500/501, decimal o string; SKU repetido, inactivo o ajeno; fecha inválida, ayer y hoy.

## Criterios de aceptación

Todos son resultados ESPERADOS; ninguna prueba de producto ejecutada. Conservar snapshots de base antes/después y evidencias según matriz.

- **AC-02-01:** Se guarda borrador con 1–10 SKU activos distintos y cantidades enteras 1–500.
- **AC-02-02:** Enviar línea con cantidad 0 retorna 422 sin solicitud enviada ni cambio de inventario.
- **AC-02-03:** Solo el dueño edita BORRADOR; edición tras envío retorna 409 sin cambios.
- **AC-02-04:** Enviar con fecha anterior a la fecha del envío retorna 422 sin transición.
- **AC-02-05:** Crear borrador válido retorna 201: cabecera y todas las líneas persisten con código único y dueño/organización del servidor; inventario no cambia.
- **AC-02-06:** Para 0 y 11 líneas, SKU duplicado/inactivo/ajeno, cantidades 0/501/decimal/string: 422, sin cabecera o líneas parciales ni evento de éxito.
- **AC-02-07:** Aceptar límites 1 y 10 líneas y cantidades 1 y 500; rechazar propiedades privilegiadas sin reasignar dueño/organización/código.
- **AC-02-08:** Enviar hoy o fecha futura válida retorna 200 y persiste ENVIADA con un evento; existencia y reservas no cambian.
- **AC-02-09:** Editar solicitud de otro dueño de la misma organización retorna 403 según propuesta CL-09; conservar cabecera, líneas y estado.
- **AC-02-10:** Reenviar ENVIADA o editar estado posterior retorna 409, sin segundo evento de envío ni cambios de inventario.
- **AC-02-11:** Formulario operable con teclado, etiquetas asociadas, errores vinculados a campos y foco visible a 390 y 1366 px; prueba manual/E2E pendiente.

## Fuente única estructurada

El bloque se exporta automáticamente; no editar JSON derivado.

```spec-json
{
  "id": "SPEC-02",
  "version": "0.2-draft",
  "title": "Creación y envío de solicitudes",
  "requirements": {
    "rf": [
      "RF03",
      "RF04"
    ],
    "rnf": [
      "RNF05"
    ]
  },
  "userStory": "Como solicitante, quiero crear, editar y enviar una solicitud válida, para solicitar kits de mi organización.",
  "inputs": [
    "Borrador: zona, prioridad, fecha requerida YYYY-MM-DD, observaciones y líneas {skuId, quantity}.",
    "Líneas: 1–10 SKU activos distintos; quantity entero 1–500. Código, dueño y organización son generados por servidor.",
    "Catálogo de zonas, prioridades y límite de observaciones son propuestas CL-01/02/03/07/10."
  ],
  "process": [
    "Validar esquema y catálogo de la organización; persistir cabecera y líneas conjuntamente como BORRADOR.",
    "Editar únicamente borrador del dueño; al enviar volver a validar líneas y fecha contra fecha local propuesta America/Bogota.",
    "Persistir transición BORRADOR → ENVIADA y evento de auditoría en una unidad consistente."
  ],
  "outputs": [
    "Creación: 201 con ID, código único, BORRADOR y líneas persistidas; edición/envío: 200.",
    "422 para datos inválidos, 403 para dueño no autorizado según CL-09, 404 para recurso ajeno, 409 para estado incompatible; sin efectos parciales."
  ],
  "rules": [
    "Enviar no reserva inventario; operaciones posteriores se definen en SPEC-03.",
    "Propuesta de prioridades: ALTA, MEDIA, BAJA; no considerarlas catálogo oficial hasta CL-10.",
    "No aceptar cambios de código, dueño u organización desde cliente."
  ],
  "edgeCases": [
    "0/1/10/11 líneas; cantidad 0/1/500/501, decimal o string; SKU repetido, inactivo o ajeno; fecha inválida, ayer y hoy."
  ],
  "acceptanceCriteria": [
    {
      "id": "AC-02-01",
      "expected": "Se guarda borrador con 1–10 SKU activos distintos y cantidades enteras 1–500.",
      "status": "draft"
    },
    {
      "id": "AC-02-02",
      "expected": "Enviar línea con cantidad 0 retorna 422 sin solicitud enviada ni cambio de inventario.",
      "status": "draft"
    },
    {
      "id": "AC-02-03",
      "expected": "Solo el dueño edita BORRADOR; edición tras envío retorna 409 sin cambios.",
      "status": "draft"
    },
    {
      "id": "AC-02-04",
      "expected": "Enviar con fecha anterior a la fecha del envío retorna 422 sin transición.",
      "status": "draft"
    },
    {
      "id": "AC-02-05",
      "expected": "Crear borrador válido retorna 201: cabecera y todas las líneas persisten con código único y dueño/organización del servidor; inventario no cambia.",
      "status": "draft"
    },
    {
      "id": "AC-02-06",
      "expected": "Para 0 y 11 líneas, SKU duplicado/inactivo/ajeno, cantidades 0/501/decimal/string: 422, sin cabecera o líneas parciales ni evento de éxito.",
      "status": "draft"
    },
    {
      "id": "AC-02-07",
      "expected": "Aceptar límites 1 y 10 líneas y cantidades 1 y 500; rechazar propiedades privilegiadas sin reasignar dueño/organización/código.",
      "status": "draft"
    },
    {
      "id": "AC-02-08",
      "expected": "Enviar hoy o fecha futura válida retorna 200 y persiste ENVIADA con un evento; existencia y reservas no cambian.",
      "status": "draft"
    },
    {
      "id": "AC-02-09",
      "expected": "Editar solicitud de otro dueño de la misma organización retorna 403 según propuesta CL-09; conservar cabecera, líneas y estado.",
      "status": "draft"
    },
    {
      "id": "AC-02-10",
      "expected": "Reenviar ENVIADA o editar estado posterior retorna 409, sin segundo evento de envío ni cambios de inventario.",
      "status": "draft"
    },
    {
      "id": "AC-02-11",
      "expected": "Formulario operable con teclado, etiquetas asociadas, errores vinculados a campos y foco visible a 390 y 1366 px; prueba manual/E2E pendiente.",
      "status": "draft"
    }
  ],
  "approvalStatus": "pending-human-review",
  "sourceStatus": "mission-provided-official-exam-missing",
  "userStoryId": "HU-02"
}
```

## Pendientes de revisión

- Cotejar con enunciado/anexos y resolver aclaraciones relacionadas.
- Revisar criterios, matriz de permisos y asignación RNF por una persona distinta al autor.
- Registrar revisión con SHA en docs/reviews/gate-review-G1.md.
- Código, pruebas de producto, PR revisado y evidencias: PENDIENTES; implementación bloqueada por G1.
