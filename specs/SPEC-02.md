# SPEC-02: Creación y envío de solicitudes

**Versión:** 0.1-draft — **no aprobada para implementar**

**RF:** RF03, RF04; **RNF:** RNF05

## Historia de usuario
Como solicitante, quiero crear, editar y enviar una solicitud válida, para solicitar kits de mi organización.

## Criterios iniciales de aceptación (requieren ampliación)
- **AC-02-01:** Se guarda borrador con 1–10 SKU activos distintos y cantidades enteras 1–500.
- **AC-02-02:** Enviar línea con cantidad 0 retorna 422 sin solicitud enviada ni cambio de inventario.
- **AC-02-03:** Solo el dueño edita BORRADOR; edición tras envío retorna 409 sin cambios.
- **AC-02-04:** Enviar con fecha anterior a la fecha del envío retorna 422 sin transición.

## Fuente única estructurada
El bloque `spec-json` se exporta automáticamente; no editar el JSON derivado.

```spec-json
{
  "id": "SPEC-02",
  "version": "0.1-draft",
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
    }
  ]
}
```

## Pendientes de revisión
- Completar todos los AC y entradas/procesos/salidas/errores específicos que exige el enunciado.
- Resolver o registrar supuestos de aclaraciones.
- Enlazar tareas, contratos, pruebas, PR y evidencias reales.
