# SPEC-01: Identidad y permisos

- **Versión:** 0.3-draft — **no aprobada para implementar**.
- **Fuente vinculante:** `docs/examen-oficial/originales/Examen_Estudiantes_SDD_IA.pdf` §2 (p. 3), §5 RNF01/RNF05/RNF06 (p. 6); anexo `fixture-scenarios.json` (cuentas).
- **Estado y aprobación:** versión 0.3 elaborada con asistencia de IA a solicitud de Castro Edier (registro `docs/evidence/ai-log/AI-001-cotejo-oficial-g1.md`). Revisor humano distinto del autor: **PENDIENTE**. Fecha UTC y commit revisado: **PENDIENTE**.

El bloque siguiente es la fuente única. `specs/generated/SPEC-01.json` se genera con `python scripts/export_specs.py`; no editarlo a mano. Los AC son resultados **esperados**: ninguna prueba de producto se ha ejecutado.

<!-- spec-data:start -->
```json
{
  "id": "SPEC-01",
  "version": "0.3-draft",
  "name": "Identidad y permisos",
  "status": "Borrador cotejado con el enunciado oficial; pendiente de revisión humana independiente",
  "story": {
    "id": "HU-01",
    "as": "usuario de una organización con rol solicitante, coordinador, bodega o auditor",
    "want": "iniciar y cerrar sesión con una cuenta sembrada y operar solo dentro de los permisos de mi rol y de mi organización",
    "soThat": "nadie lea ni modifique recursos de otra organización ni ejecute acciones que su rol no permite"
  },
  "requirements": ["RF01", "RF02", "RNF01", "RNF05", "RNF06"],
  "description": "Autenticación con cuentas sembradas, resolución de identidad en el servidor, matriz de permisos por rol aplicada en cada operación y aislamiento por organización con respuestas 401, 403 y 404 que no revelan datos. Enunciado §2.",
  "actors": [
    "Solicitante, Coordinador, Bodega y Auditor de ORG-A u ORG-B",
    "API del sistema, que resuelve identidad, organización y rol"
  ],
  "scope": [
    "Inicio y cierre de sesión con cuentas sembradas; sesión o token justificado en ADR-003",
    "Hash apropiado de contraseñas y ausencia de secretos en el repositorio",
    "Ocho cuentas ficticias: una por rol en cada organización, regenerables por seed",
    "Matriz de permisos aplicada en el servidor y aislamiento por organización en toda operación",
    "Respuestas, errores y logs sin datos de otra organización ni secretos"
  ],
  "exclusions": [
    "Creación masiva de usuarios, recuperación de contraseña y proveedores de identidad externos (no exigidos, enunciado §2)",
    "Selector de rol como reemplazo del inicio de sesión (prohibido por RF01)"
  ],
  "inputs": [
    {
      "field": "login",
      "type": "string; correo ficticio del seed, por ejemplo org-a-solicitante@example.test",
      "validation": ["Obligatorio", "Debe corresponder a una cuenta sembrada; el error no revela si la cuenta existe"]
    },
    {
      "field": "password",
      "type": "string",
      "validation": ["Obligatorio", "Se verifica contra el hash almacenado", "Nunca se registra ni se devuelve"]
    },
    {
      "field": "credencial de sesión",
      "type": "cookie de sesión o token bearer según ADR-003",
      "validation": ["Debe estar vigente y no revocada en cada petición", "Ausente, inválida o revocada produce 401"]
    },
    {
      "field": "organización y rol",
      "type": "derivados de la identidad autenticada",
      "validation": ["Cualquier organizationId o role enviado por el navegador se ignora y no autoriza"]
    },
    {
      "field": "identificador de recurso",
      "type": "string",
      "validation": ["Se busca solo dentro de la organización autenticada", "Inexistente o de otra organización produce 404"]
    }
  ],
  "process": [
    "Recibir login y contraseña, buscar la cuenta y verificar la contraseña contra su hash",
    "Si son válidas, crear una sesión o token revocable; si no, responder 401 genérico sin crear sesión",
    "En cada petición resolver identidad, organización y rol desde la credencial; sin credencial válida responder 401",
    "Resolver el recurso filtrando por la organización de la identidad; si no existe en ella responder 404",
    "Evaluar el permiso del rol sobre la operación según la matriz del enunciado; si está prohibida responder 403 sin efectos",
    "Ejecutar la operación autorizada y registrar logs estructurados con correlationId, sin contraseñas, hashes ni tokens",
    "En logout revocar la credencial; cualquier uso posterior recibe 401"
  ],
  "outputs": [
    {"condition": "Credenciales válidas", "result": "200 con cuenta, rol y organización, sin hash; se crea sesión o token"},
    {"condition": "Contraseña errónea o cuenta inexistente", "result": "401 con el mismo mensaje en ambos casos; no se crea sesión"},
    {"condition": "Logout", "result": "204; la credencial queda revocada de forma persistente"},
    {"condition": "Petición sin identidad válida", "result": "401 sin datos de negocio"},
    {"condition": "Recurso inexistente o de otra organización", "result": "404 sin revelar existencia ni datos"},
    {"condition": "Acción prohibida para el rol sobre un recurso de su organización", "result": "403 sin cambios persistidos"}
  ],
  "businessRules": [
    "Matriz oficial, enunciado §2. Solicitante: crear y editar sus borradores, enviar su solicitud, consultar las propias y cancelar las propias en ENVIADA o RESERVADA",
    "Coordinador: consultar solicitudes de su organización, reservar ENVIADA, registrar ENTREGADA sobre DESPACHADA, usar y aprobar resúmenes de IA",
    "Bodega: consultar inventario y solicitudes de su organización y despachar RESERVADA",
    "Auditor: consultar solicitudes, inventario y auditoría de su organización; no modifica datos ni invoca el asistente",
    "Una operación no listada para un rol se deniega: 403 si el recurso es de su organización. Quedan para confirmación docente la consulta de inventario por el Coordinador (CL-16), la consulta de auditoría por roles distintos del Auditor (CL-17) y el tablero del Solicitante (CL-18)",
    "Los permisos se comprueban en el servidor en cada operación; ocultar un botón no es autorización",
    "La organización y el rol provienen de la identidad autenticada; un valor enviado por el navegador no autoriza cambiar de organización",
    "No se admite un selector de rol como reemplazo del inicio de sesión",
    "Contraseñas guardadas con hash apropiado; sin claves privadas ni secretos en el repositorio",
    "Una cuenta por rol en cada organización con credenciales ficticias publicadas para evaluación y forma documentada de regenerarlas",
    "Las reglas 401, 403 y 404 se aplican también al contenido de respuestas, errores y logs"
  ],
  "edgeCases": [
    "Contraseña errónea y login inexistente con respuesta indistinguible",
    "Credencial revocada o expirada reutilizada",
    "Rol u organización adulterados en cuerpo, parámetros o encabezados",
    "Usuario de ORG-A que conoce el identificador B-001 de ORG-B",
    "Llamadas directas a la API sin pasar por la interfaz",
    "Auditor que intenta una escritura dentro de su organización",
    "Solicitante que consulta la solicitud de otro solicitante de su organización: 403 por la regla de acción prohibida sobre recurso propio de la organización"
  ],
  "assumptions": [
    "Sesión de servidor revocable como propuesta; la elección sesión o token se justifica en ADR-003 (G2)",
    "Algoritmo y parámetros de hash se fijan en ADR-003",
    "Los logins de las ocho cuentas son los del anexo fixture-scenarios.json"
  ],
  "openQuestions": [
    "CL-14: expiración de sesión, protección CSRF y política de contraseña (decisión del equipo en ADR-003)",
    "CL-16, CL-17 y CL-18: permisos no listados en la matriz oficial (requieren docente)"
  ],
  "dependencies": [
    "Seed de cuentas y migraciones (RNF04, SPEC-04)",
    "Las SPEC-02 a SPEC-05 aplican esta matriz de permisos a sus operaciones"
  ],
  "acceptanceCriteria": [
    {
      "id": "AC-01-01",
      "requirements": ["RF01", "RNF01"],
      "kind": "negativo",
      "given": "un cliente sin sesión ni token",
      "when": "consulta el listado de solicitudes por la API",
      "then": "recibe 401 sin datos de solicitudes y no se crea ninguna sesión"
    },
    {
      "id": "AC-01-02",
      "requirements": ["RF02", "RNF01"],
      "kind": "permiso",
      "given": "un coordinador de ORG-A autenticado y la solicitud B-001 de ORG-B sembrada",
      "when": "consulta B-001 por su identificador",
      "then": "recibe 404 con el mismo cuerpo que para un identificador inexistente; la respuesta y el log no contienen datos de B-001"
    },
    {
      "id": "AC-01-03",
      "requirements": ["RF02", "RNF01"],
      "kind": "permiso",
      "given": "un auditor de ORG-A autenticado y A-001 en ENVIADA",
      "when": "intenta reservar o modificar A-001",
      "then": "recibe 403; el estado de A-001, el inventario y los eventos de auditoría no cambian"
    },
    {
      "id": "AC-01-04",
      "requirements": ["RF01"],
      "kind": "positivo",
      "given": "el seed aplicado con las ocho cuentas ficticias",
      "when": "cada cuenta inicia sesión con su contraseña sembrada, ejecuta una operación permitida de su rol y cierra sesión",
      "then": "el login responde 200 con rol y organización correctos y sin hash; la operación permitida tiene éxito; el logout responde 204 y la misma credencial recibe 401 después"
    },
    {
      "id": "AC-01-05",
      "requirements": ["RF01"],
      "kind": "positivo",
      "given": "una base recién migrada y sembrada",
      "when": "se consultan las cuentas en la base y se lee el runbook",
      "then": "existen ocho cuentas distintas, una por rol en ORG-A y en ORG-B, con los logins del anexo; las contraseñas solo están como hash; el runbook publica las credenciales ficticias y el comando que las regenera"
    },
    {
      "id": "AC-01-06",
      "requirements": ["RF01", "RNF01"],
      "kind": "negativo",
      "given": "una cuenta existente y un login inexistente",
      "when": "se intenta iniciar sesión con contraseña errónea en la primera y con cualquier contraseña en el segundo",
      "then": "ambos intentos reciben 401 con el mismo mensaje; no se crea sesión y la respuesta no revela si la cuenta existe"
    },
    {
      "id": "AC-01-07",
      "requirements": ["RF02", "RNF01"],
      "kind": "permiso",
      "given": "un solicitante de ORG-A autenticado",
      "when": "envía organizationId ORG-B o role COORDINADOR en el cuerpo, parámetros o encabezados de una operación",
      "then": "la API usa la identidad del servidor; el valor enviado no concede permisos ni cambia la organización del recurso; la operación no permitida responde 403 o 404 según corresponda y no persiste cambios"
    },
    {
      "id": "AC-01-08",
      "requirements": ["RF01"],
      "kind": "negativo",
      "given": "una credencial cerrada con logout",
      "when": "se reutiliza en cualquier operación",
      "then": "recibe 401; la revocación está persistida y no se ejecuta ninguna operación de negocio"
    },
    {
      "id": "AC-01-09",
      "requirements": ["RF02", "RNF01"],
      "kind": "permiso",
      "given": "usuarios autenticados de ORG-A y recursos sembrados de ORG-B",
      "when": "ejecutan dos lecturas cruzadas, por ejemplo detalle de B-001 y su auditoría, y dos escrituras cruzadas, por ejemplo reservar y despachar B-001",
      "then": "las cuatro reciben 404; solicitudes, inventario y auditoría de ORG-B quedan idénticos según snapshot antes y después"
    },
    {
      "id": "AC-01-10",
      "requirements": ["RNF01", "RNF06"],
      "kind": "fallo",
      "given": "respuestas y logs producidos por login correcto, login fallido, 401, 403, 404 y un error interno controlado",
      "when": "se inspeccionan",
      "then": "los logs son estructurados con correlationId y ni logs ni respuestas contienen contraseñas, hashes, tokens ni secretos"
    },
    {
      "id": "AC-01-11",
      "requirements": ["RF01"],
      "kind": "negativo",
      "given": "la interfaz web y la API sin sesión iniciada",
      "when": "se intenta entrar al flujo eligiendo un rol o llamando a una operación sin credencial",
      "then": "la interfaz solo ofrece inicio de sesión con credenciales y no existe selector de rol que conceda acceso; la API responde 401"
    },
    {
      "id": "AC-01-12",
      "requirements": ["RF01"],
      "kind": "negativo",
      "given": "el repositorio en el commit evaluado",
      "when": "se ejecuta la detección de secretos documentada en CI",
      "then": "no se encuentran claves privadas, contraseñas reales ni secretos; solo existe configuración de ejemplo sin secretos"
    },
    {
      "id": "AC-01-13",
      "requirements": ["RNF05"],
      "kind": "positivo",
      "given": "la pantalla de inicio y cierre de sesión a 390 y 1366 px de ancho",
      "when": "se opera solo con teclado, incluido un intento con credenciales erróneas",
      "then": "no hay desbordamiento horizontal; los campos tienen etiqueta; el error queda asociado al formulario y visible; la verificación manual se registra con navegador y fecha sin afirmar conformidad WCAG total"
    }
  ]
}
```
<!-- spec-data:end -->

## Decisiones y aclaraciones

- Precedencia de respuestas resuelta por el enunciado §2: 401 sin identidad; 404 para recurso inexistente o de otra organización; 403 para acción prohibida sobre recurso de su organización. Por eso se resuelve primero el recurso dentro de la organización y después el permiso del rol.
- Matriz de permisos tomada del enunciado; sustituye la matriz propuesta en 0.2 (ver diferencias D-01 y D-02 del cotejo).
- Pendientes: CL-14 (equipo), CL-16, CL-17 y CL-18 (docente).

## Riesgos y plan de prueba

- R-02 acceso interorganización: AC-01-02, AC-01-07 y AC-01-09 con snapshots de base.
- Pruebas previstas: API con base real para 401/403/404, inspección de logs, detección de secretos en CI y revisión manual de teclado y anchos.

## Cambios de versión

- 0.1: borrador inicial (commit `1aeacbf`).
- 0.2-draft: ampliación a 10 AC sin fuente oficial.
- 0.3-draft: cotejo con el enunciado oficial; matriz oficial de permisos; formato del anexo 13 con entradas tipadas y AC dado/cuando/entonces; nuevos AC-01-11 a AC-01-13 (selector de rol, secretos, RNF05).
