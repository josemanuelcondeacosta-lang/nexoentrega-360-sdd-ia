# SPEC-01: Identidad y permisos

**Versión:** 0.2-draft — **no aprobada para implementar**

Fuente: misión del usuario y borrador inicial. Enunciado oficial y anexos NO recibidos. RF/RNF conservan identificadores iniciales; significado de RNF pendiente de cotejo. HTTP, roles, catálogos y campos propuestos requieren revisión.

**RF:** RF01, RF02; **RNF:** RNF01, RNF06

## Historia de usuario HU-01

Como usuario de una organización, quiero autenticarme y acceder únicamente a operaciones autorizadas, para proteger sus recursos.

## Entradas

- Login: identificador de cuenta ficticia y contraseña; logout: sesión vigente.
- Identidad, organización y rol se obtienen del servidor; no confiar en campos de cliente.

## Procesos

- Validar credenciales contra hash, crear sesión revocable y resolver identidad por petición.
- Autorizar operación y filtrar recurso por organización y alcance del rol antes de devolver datos.

## Salidas y efectos persistidos

- Login válido: 200 e identidad sin hash; logout: 204 y sesión revocada.
- 401 sin sesión válida, 403 para operación prohibida, 404 para recurso ajeno o inexistente; sin cambios de negocio.

## Reglas y supuestos

- Cuatro roles propuestos: SOLICITANTE, COORDINADOR, BODEGA y AUDITOR; confirmar matriz CL-09.
- Dos organizaciones y una cuenta ficticia por rol en cada una; hash con algoritmo y parámetros a decidir en ADR-003.
- No devolver ni registrar contraseñas, hashes o tokens.

## Límites y escenarios adversariales

- Contraseña errónea, sesión revocada, rol adulterado, ID ajeno, login inexistente y acceso directo sin pasar por UI.

## Criterios de aceptación

Todos son resultados ESPERADOS; ninguna prueba de producto ejecutada. Conservar snapshots de base antes/después y evidencias según matriz.

- **AC-01-01:** Sin credenciales, consultar solicitudes retorna HTTP 401.
- **AC-01-02:** Un usuario autenticado de ORG-A consulta ID de ORG-B y recibe 404 sin revelar datos.
- **AC-01-03:** Auditor intenta mutar solicitud de su organización y obtiene 403 sin modificación.
- **AC-01-04:** Iniciar sesión con contraseña sembrada válida permite operar según el rol; cerrar sesión revoca acceso.
- **AC-01-05:** El seed produce ocho cuentas distintas, cuatro por organización; la base contiene hashes y ninguna contraseña en claro.
- **AC-01-06:** Credenciales erróneas o cuenta inexistente retornan el mismo error 401, sin crear sesión ni revelar existencia de cuenta.
- **AC-01-07:** Alterar rol u organización en el cuerpo no concede permisos: identidad del servidor prevalece y no cambia el recurso.
- **AC-01-08:** Tras logout, reutilizar la sesión retorna 401; comprobar revocación persistida y ausencia de cambios de negocio.
- **AC-01-09:** Realizar dos lecturas y dos escrituras cruzadas ORG-A/ORG-B: 404 sin filtrar datos ni alterar solicitudes, reservas o eventos de éxito.
- **AC-01-10:** Inspeccionar respuestas y logs de login, errores y consultas: no contienen contraseña, hash ni token.

## Fuente única estructurada

El bloque se exporta automáticamente; no editar JSON derivado.

```spec-json
{
  "id": "SPEC-01",
  "version": "0.2-draft",
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
    "Login: identificador de cuenta ficticia y contraseña; logout: sesión vigente.",
    "Identidad, organización y rol se obtienen del servidor; no confiar en campos de cliente."
  ],
  "process": [
    "Validar credenciales contra hash, crear sesión revocable y resolver identidad por petición.",
    "Autorizar operación y filtrar recurso por organización y alcance del rol antes de devolver datos."
  ],
  "outputs": [
    "Login válido: 200 e identidad sin hash; logout: 204 y sesión revocada.",
    "401 sin sesión válida, 403 para operación prohibida, 404 para recurso ajeno o inexistente; sin cambios de negocio."
  ],
  "rules": [
    "Cuatro roles propuestos: SOLICITANTE, COORDINADOR, BODEGA y AUDITOR; confirmar matriz CL-09.",
    "Dos organizaciones y una cuenta ficticia por rol en cada una; hash con algoritmo y parámetros a decidir en ADR-003.",
    "No devolver ni registrar contraseñas, hashes o tokens."
  ],
  "edgeCases": [
    "Contraseña errónea, sesión revocada, rol adulterado, ID ajeno, login inexistente y acceso directo sin pasar por UI."
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
    },
    {
      "id": "AC-01-05",
      "expected": "El seed produce ocho cuentas distintas, cuatro por organización; la base contiene hashes y ninguna contraseña en claro.",
      "status": "draft"
    },
    {
      "id": "AC-01-06",
      "expected": "Credenciales erróneas o cuenta inexistente retornan el mismo error 401, sin crear sesión ni revelar existencia de cuenta.",
      "status": "draft"
    },
    {
      "id": "AC-01-07",
      "expected": "Alterar rol u organización en el cuerpo no concede permisos: identidad del servidor prevalece y no cambia el recurso.",
      "status": "draft"
    },
    {
      "id": "AC-01-08",
      "expected": "Tras logout, reutilizar la sesión retorna 401; comprobar revocación persistida y ausencia de cambios de negocio.",
      "status": "draft"
    },
    {
      "id": "AC-01-09",
      "expected": "Realizar dos lecturas y dos escrituras cruzadas ORG-A/ORG-B: 404 sin filtrar datos ni alterar solicitudes, reservas o eventos de éxito.",
      "status": "draft"
    },
    {
      "id": "AC-01-10",
      "expected": "Inspeccionar respuestas y logs de login, errores y consultas: no contienen contraseña, hash ni token.",
      "status": "draft"
    }
  ],
  "approvalStatus": "pending-human-review",
  "sourceStatus": "mission-provided-official-exam-missing",
  "userStoryId": "HU-01"
}
```

## Pendientes de revisión

- Cotejar con enunciado/anexos y resolver aclaraciones relacionadas.
- Revisar criterios, matriz de permisos y asignación RNF por una persona distinta al autor.
- Registrar revisión con SHA en docs/reviews/gate-review-G1.md.
- Código, pruebas de producto, PR revisado y evidencias: PENDIENTES; implementación bloqueada por G1.
