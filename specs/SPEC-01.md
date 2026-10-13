# SPEC-01: Identidad y permisos

**Versión:** 0.1-draft — **no aprobada para implementar**

**RF:** RF01, RF02; **RNF:** RNF01, RNF06

## Historia de usuario
Como usuario de una organización, quiero autenticarme y acceder únicamente a operaciones autorizadas, para proteger sus recursos.

## Criterios iniciales de aceptación (requieren ampliación)
- **AC-01-01:** Sin credenciales, consultar solicitudes retorna HTTP 401.
- **AC-01-02:** Un usuario autenticado de ORG-A consulta ID de ORG-B y recibe 404 sin revelar datos.
- **AC-01-03:** Auditor intenta mutar solicitud de su organización y obtiene 403 sin modificación.
- **AC-01-04:** Iniciar sesión con contraseña sembrada válida permite operar según el rol; cerrar sesión revoca acceso.

## Fuente única estructurada
El bloque `spec-json` se exporta automáticamente; no editar el JSON derivado.

```spec-json
{
  "id": "SPEC-01",
  "version": "0.1-draft",
  "title": "Identidad y permisos",
  "requirements": {
    "rf": [
      "RF01",
      "RF02"
    ],
    "rnf": [
      "RNF01",
      "RNF06"
    ]
  },
  "userStory": "Como usuario de una organización, quiero autenticarme y acceder únicamente a operaciones autorizadas, para proteger sus recursos.",
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
      "id": "AC-01-01",
      "expected": "Sin credenciales, consultar solicitudes retorna HTTP 401.",
      "status": "draft"
    },
    {
      "id": "AC-01-02",
      "expected": "Un usuario autenticado de ORG-A consulta ID de ORG-B y recibe 404 sin revelar datos.",
      "status": "draft"
    },
    {
      "id": "AC-01-03",
      "expected": "Auditor intenta mutar solicitud de su organización y obtiene 403 sin modificación.",
      "status": "draft"
    },
    {
      "id": "AC-01-04",
      "expected": "Iniciar sesión con contraseña sembrada válida permite operar según el rol; cerrar sesión revoca acceso.",
      "status": "draft"
    }
  ]
}
```

## Pendientes de revisión
- Completar todos los AC y entradas/procesos/salidas/errores específicos que exige el enunciado.
- Resolver o registrar supuestos de aclaraciones.
- Enlazar tareas, contratos, pruebas, PR y evidencias reales.
