# SPEC-05: Asistente IA

**Versión:** 0.1-draft — **no aprobada para implementar**

**RF:** RF10, RF11, RF12; **RNF:** RNF01, RNF06

## Historia de usuario
Como coordinador, quiero obtener un resumen revisable de solicitudes enviadas, para orientar mi revisión sin delegar decisiones.

## Criterios iniciales de aceptación (requieren ampliación)
- **AC-05-01:** El adaptador recibe máximo 20 solicitudes ENVIADA de la organización y solo seis campos autorizados.
- **AC-05-02:** La salida con ID ajeno, duplicado o propiedad extra es inválida y provoca fallback controlado.
- **AC-05-03:** Timeout a 5 s o fallo del proveedor genera resumen determinista sin bloquear flujo principal.
- **AC-05-04:** Solo coordinador aprueba borrador; se audita aprobación sin modificar inventario ni prioridad.

## Fuente única estructurada
El bloque `spec-json` se exporta automáticamente; no editar el JSON derivado.

```spec-json
{
  "id": "SPEC-05",
  "version": "0.1-draft",
  "title": "Asistente IA",
  "requirements": {
    "rf": [
      "RF10",
      "RF11",
      "RF12"
    ],
    "rnf": [
      "RNF01",
      "RNF06"
    ]
  },
  "userStory": "Como coordinador, quiero obtener un resumen revisable de solicitudes enviadas, para orientar mi revisión sin delegar decisiones.",
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
      "id": "AC-05-01",
      "expected": "El adaptador recibe máximo 20 solicitudes ENVIADA de la organización y solo seis campos autorizados.",
      "status": "draft"
    },
    {
      "id": "AC-05-02",
      "expected": "La salida con ID ajeno, duplicado o propiedad extra es inválida y provoca fallback controlado.",
      "status": "draft"
    },
    {
      "id": "AC-05-03",
      "expected": "Timeout a 5 s o fallo del proveedor genera resumen determinista sin bloquear flujo principal.",
      "status": "draft"
    },
    {
      "id": "AC-05-04",
      "expected": "Solo coordinador aprueba borrador; se audita aprobación sin modificar inventario ni prioridad.",
      "status": "draft"
    }
  ]
}
```

## Pendientes de revisión
- Completar todos los AC y entradas/procesos/salidas/errores específicos que exige el enunciado.
- Resolver o registrar supuestos de aclaraciones.
- Enlazar tareas, contratos, pruebas, PR y evidencias reales.
