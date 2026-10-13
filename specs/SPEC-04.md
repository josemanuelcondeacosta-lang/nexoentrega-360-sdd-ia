# SPEC-04: Consultas y auditoría

**Versión:** 0.2-draft — **no aprobada para implementar**

Fuente: misión del usuario y borrador inicial. Enunciado oficial y anexos NO recibidos. RF/RNF conservan identificadores iniciales; significado de RNF pendiente de cotejo. HTTP, roles, catálogos y campos propuestos requieren revisión.

**RF:** RF08, RF09; **RNF:** RNF03, RNF06, RNF04

## Historia de usuario HU-04

Como usuario autorizado, quiero consultar estados y eventos de mi organización, para verificar el seguimiento.

## Entradas

- Listados: page entero ≥1, pageSize 1–100, filtros estado/prioridad/zona; valores por defecto propuestos 1/20.
- Tablero e historial: sesión, alcance del rol y ID de solicitud; correlationId generado o validado por servidor.

## Procesos

- Aplicar organización y alcance de dueño antes de filtros, conteo y paginación; ordenar estable por fecha e ID.
- Calcular tablero con el mismo alcance autorizado; leer historial persistente en orden temporal.
- Escribir evento junto al cambio de negocio con actor, organización, solicitud, acción, antes/después, UTC y correlationId sanitizado.

## Salidas y efectos persistidos

- 200 con items, page, pageSize y total; vacío devuelve items=[] y total=0.
- 422 para parámetros inválidos; 401/403/404 conforme SPEC-01; nunca revelar conteos de otra organización.

## Reglas y supuestos

- Auditoría no editable ni eliminable mediante API; logs de rechazo son distintos a eventos de transición exitosa.
- Carga prevista: 2.000 solicitudes, 20 VU durante 60s después de 5s de calentamiento; IA excluida.
- p95 listados/tablero ≤700ms; errores <1%; registrar hardware, entorno, datos y comandos.

## Límites y escenarios adversariales

- Página vacía, filtros combinados, pageSize=0/101, empates de fecha, fallo de auditoría y correlationId malicioso.

## Criterios de aceptación

Todos son resultados ESPERADOS; ninguna prueba de producto ejecutada. Conservar snapshots de base antes/después y evidencias según matriz.

- **AC-04-01:** El solicitante recibe únicamente sus solicitudes y la paginación no excede 100.
- **AC-04-02:** Filtros de estado, prioridad y zona y tablero respetan organización.
- **AC-04-03:** Cada transición exitosa genera evento con usuario, organización, acción, UTC y correlationId en la misma unidad consistente.
- **AC-04-04:** No hay endpoint que permita editar ni borrar auditoría; acciones rechazadas no simulan transición exitosa.
- **AC-04-05:** pageSize 0/101 y page inválida retornan 422; página fuera de resultados devuelve 200 y lista vacía sin datos ajenos.
- **AC-04-06:** Combinar estado/prioridad/zona devuelve solo coincidencias autorizadas; total y tablero coinciden con consultas de base en el mismo alcance.
- **AC-04-07:** Correlacionar una transición con respuesta, log sanitizado y evento persistido: mismo correlationId, actor y organización correctos, fecha UTC.
- **AC-04-08:** Intentar modificar o borrar eventos mediante HTTP no altera ninguna fila de auditoría; comparar contenido antes/después.
- **AC-04-09:** Medir con 2.000 solicitudes y 20 VU: 5s de calentamiento excluidos y 60s medidos, p95 por endpoint ≤700ms y errores <1%; conservar salida real.
- **AC-04-10:** Inventario consultable solo en alcance permitido CL-09: muestra físico, reservado y disponible consistentes sin exponer otra organización.

## Fuente única estructurada

El bloque se exporta automáticamente; no editar JSON derivado.

```spec-json
{
  "id": "SPEC-04",
  "version": "0.2-draft",
  "title": "Consultas y auditoría",
  "requirements": {
    "rf": [
      "RF08",
      "RF09"
    ],
    "rnf": [
      "RNF03",
      "RNF06",
      "RNF04"
    ]
  },
  "userStory": "Como usuario autorizado, quiero consultar estados y eventos de mi organización, para verificar el seguimiento.",
  "inputs": [
    "Listados: page entero ≥1, pageSize 1–100, filtros estado/prioridad/zona; valores por defecto propuestos 1/20.",
    "Tablero e historial: sesión, alcance del rol y ID de solicitud; correlationId generado o validado por servidor."
  ],
  "process": [
    "Aplicar organización y alcance de dueño antes de filtros, conteo y paginación; ordenar estable por fecha e ID.",
    "Calcular tablero con el mismo alcance autorizado; leer historial persistente en orden temporal.",
    "Escribir evento junto al cambio de negocio con actor, organización, solicitud, acción, antes/después, UTC y correlationId sanitizado."
  ],
  "outputs": [
    "200 con items, page, pageSize y total; vacío devuelve items=[] y total=0.",
    "422 para parámetros inválidos; 401/403/404 conforme SPEC-01; nunca revelar conteos de otra organización."
  ],
  "rules": [
    "Auditoría no editable ni eliminable mediante API; logs de rechazo son distintos a eventos de transición exitosa.",
    "Carga prevista: 2.000 solicitudes, 20 VU durante 60s después de 5s de calentamiento; IA excluida.",
    "p95 listados/tablero ≤700ms; errores <1%; registrar hardware, entorno, datos y comandos."
  ],
  "edgeCases": [
    "Página vacía, filtros combinados, pageSize=0/101, empates de fecha, fallo de auditoría y correlationId malicioso."
  ],
  "acceptanceCriteria": [
    {
      "id": "AC-04-01",
      "expected": "El solicitante recibe únicamente sus solicitudes y la paginación no excede 100.",
      "status": "draft"
    },
    {
      "id": "AC-04-02",
      "expected": "Filtros de estado, prioridad y zona y tablero respetan organización.",
      "status": "draft"
    },
    {
      "id": "AC-04-03",
      "expected": "Cada transición exitosa genera evento con usuario, organización, acción, UTC y correlationId en la misma unidad consistente.",
      "status": "draft"
    },
    {
      "id": "AC-04-04",
      "expected": "No hay endpoint que permita editar ni borrar auditoría; acciones rechazadas no simulan transición exitosa.",
      "status": "draft"
    },
    {
      "id": "AC-04-05",
      "expected": "pageSize 0/101 y page inválida retornan 422; página fuera de resultados devuelve 200 y lista vacía sin datos ajenos.",
      "status": "draft"
    },
    {
      "id": "AC-04-06",
      "expected": "Combinar estado/prioridad/zona devuelve solo coincidencias autorizadas; total y tablero coinciden con consultas de base en el mismo alcance.",
      "status": "draft"
    },
    {
      "id": "AC-04-07",
      "expected": "Correlacionar una transición con respuesta, log sanitizado y evento persistido: mismo correlationId, actor y organización correctos, fecha UTC.",
      "status": "draft"
    },
    {
      "id": "AC-04-08",
      "expected": "Intentar modificar o borrar eventos mediante HTTP no altera ninguna fila de auditoría; comparar contenido antes/después.",
      "status": "draft"
    },
    {
      "id": "AC-04-09",
      "expected": "Medir con 2.000 solicitudes y 20 VU: 5s de calentamiento excluidos y 60s medidos, p95 por endpoint ≤700ms y errores <1%; conservar salida real.",
      "status": "draft"
    },
    {
      "id": "AC-04-10",
      "expected": "Inventario consultable solo en alcance permitido CL-09: muestra físico, reservado y disponible consistentes sin exponer otra organización.",
      "status": "draft"
    }
  ],
  "approvalStatus": "pending-human-review",
  "sourceStatus": "mission-provided-official-exam-missing",
  "userStoryId": "HU-04"
}
```

## Pendientes de revisión

- Cotejar con enunciado/anexos y resolver aclaraciones relacionadas.
- Revisar criterios, matriz de permisos y asignación RNF por una persona distinta al autor.
- Registrar revisión con SHA en docs/reviews/gate-review-G1.md.
- Código, pruebas de producto, PR revisado y evidencias: PENDIENTES; implementación bloqueada por G1.
