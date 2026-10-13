# SPEC-04: Consultas y auditoría

**Versión:** 0.1-draft — **no aprobada para implementar**

**RF:** RF08, RF09; **RNF:** RNF03, RNF06

## Historia de usuario
Como usuario autorizado, quiero consultar estados y eventos de mi organización, para verificar el seguimiento.

## Criterios iniciales de aceptación (requieren ampliación)
- **AC-04-01:** El solicitante recibe únicamente sus solicitudes y la paginación no excede 100.
- **AC-04-02:** Filtros de estado, prioridad y zona y tablero respetan organización.
- **AC-04-03:** Cada transición exitosa genera evento con usuario, organización, acción, UTC y correlationId en la misma unidad consistente.
- **AC-04-04:** No hay endpoint que permita editar ni borrar auditoría; acciones rechazadas no simulan transición exitosa.

## Fuente única estructurada
El bloque `spec-json` se exporta automáticamente; no editar el JSON derivado.

```spec-json
{
  "id": "SPEC-04",
  "version": "0.1-draft",
  "title": "Consultas y auditoría",
  "requirements": {
    "rf": [
      "RF08",
      "RF09"
    ],
    "rnf": [
      "RNF03",
      "RNF06"
    ]
  },
  "userStory": "Como usuario autorizado, quiero consultar estados y eventos de mi organización, para verificar el seguimiento.",
  "inputs": [
    "Pendiente: detallar campos, tipos y validaciones de todos los endpoints"
  ],
  "process": [
    "Pendiente: secuencia comprobable por escenario"
  ],
  "outputs": [
    "Pendiente: HTTP, cuerpo y efectos persistidos"
  ],
  "rules": [
    "Consultar RF/RNF oficiales y clarification-log"
  ],
  "edgeCases": [
    "Pendiente: ampliar límites, fallos y permisos"
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
    }
  ]
}
```

## Pendientes de revisión
- Completar todos los AC y entradas/procesos/salidas/errores específicos que exige el enunciado.
- Resolver o registrar supuestos de aclaraciones.
- Enlazar tareas, contratos, pruebas, PR y evidencias reales.
