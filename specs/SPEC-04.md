# SPEC-04: Consultas y auditoría

- **Versión:** 0.3.1-draft — **no aprobada para implementar**.
- **Fuente vinculante:** `docs/examen-oficial/originales/Examen_Estudiantes_SDD_IA.pdf` §1 objetivos observables (p. 2), §2 (p. 3), §4 RF08–RF09 (p. 5), §5 RNF03–RNF06 (p. 6), §11 operación (p. 12), §12 ejecución reproducible (p. 13).
- **Estado y aprobación:** versión 0.3 elaborada con asistencia de IA a solicitud de Castro Edier (registro `docs/evidence/ai-log/AI-001-cotejo-oficial-g1.md`). Revisor humano distinto del autor: **PENDIENTE**. Fecha UTC y commit revisado: **PENDIENTE**.

El bloque siguiente es la fuente única. `specs/generated/SPEC-04.json` se genera con `python scripts/export_specs.py`; no editarlo a mano. Los AC son resultados **esperados**: ninguna prueba de producto se ha ejecutado.

<!-- spec-data:start -->
```json
{
  "id": "SPEC-04",
  "version": "0.3.1-draft",
  "name": "Consultas y auditoría",
  "status": "Borrador cotejado con el enunciado oficial; pendiente de revisión humana independiente",
  "story": {
    "id": "HU-04",
    "as": "solicitante, coordinador, encargado de bodega o auditor de una organización",
    "want": "consultar solicitudes, tablero, inventario e historial de auditoría dentro de mi alcance",
    "soThat": "pueda dar seguimiento y demostrar quién realizó cada operación sin ver datos de otra organización"
  },
  "requirements": ["RF02", "RF08", "RF09", "RNF03", "RNF04", "RNF05", "RNF06"],
  "description": "Listados paginados con filtros, tablero de cantidades por estado e inventario, historial de auditoría inmutable vía API y condiciones operativas de consulta: rendimiento, arranque reproducible, logs con correlationId y health y readiness. Enunciado §4 y §5.",
  "actors": [
    "Solicitante: solo sus solicitudes",
    "Coordinador: solicitudes, inventario e historial de su organización (CL-16/17 reportadas); Bodega: solicitudes e inventario sin auditoría",
    "Auditor: solicitudes, inventario y auditoría de su organización",
    "Operador o evaluador: arranque, health y readiness"
  ],
  "scope": [
    "Listados paginados con máximo 100 elementos y filtros por estado, prioridad y zona",
    "Tablero con cantidades por estado y cantidades físicas, reservadas y disponibles",
    "Escritura y consulta del historial de auditoría sin edición ni borrado por API",
    "Logs estructurados con correlationId, health y readiness diferenciados",
    "Rendimiento de listados y tablero y arranque desde clon limpio"
  ],
  "exclusions": [
    "Exportaciones, reportes adicionales y búsqueda de texto libre (no exigidos)",
    "Rendimiento del asistente de IA (excluido de RNF03)"
  ],
  "inputs": [
    {
      "field": "page",
      "type": "integer",
      "validation": ["Mayor o igual que 1; valor por defecto 1", "Inválido produce 422"]
    },
    {
      "field": "pageSize",
      "type": "integer",
      "validation": ["Entre 1 y 100; valor por defecto 20 (supuesto)", "0, 101 o no entero produce 422"]
    },
    {
      "field": "estado",
      "type": "enum BORRADOR, ENVIADA, RESERVADA, DESPACHADA, ENTREGADA o CANCELADA; opcional",
      "validation": ["Otro valor produce 422"]
    },
    {
      "field": "prioridad",
      "type": "enum NORMAL o ALTA; opcional",
      "validation": ["Otro valor produce 422"]
    },
    {
      "field": "zona",
      "type": "string del catálogo de CL-01; opcional",
      "validation": ["Zona desconocida produce 422 (supuesto)"]
    },
    {
      "field": "requestId del historial",
      "type": "string",
      "validation": ["Debe existir en la organización autenticada; si no, 404"]
    },
    {
      "field": "X-Correlation-Id",
      "type": "encabezado string opcional (supuesto: UUID)",
      "validation": ["Si falta o es inválido el servidor genera uno", "Nunca se registra sin sanitizar"]
    }
  ],
  "process": [
    "Autenticar y determinar el alcance: Solicitante, solo sus solicitudes; Coordinador, Bodega y Auditor, su organización según la matriz de SPEC-01",
    "Aplicar el alcance antes de filtros, conteos y paginación; ordenar de forma estable por fecha de creación e identificador",
    "Tablero: contar solicitudes por estado y calcular físico, reservado y disponible por SKU con el mismo alcance",
    "Auditoría: los módulos de SPEC-02, SPEC-03 y SPEC-05 escriben el evento en la misma transacción del cambio con identidad, organización, recurso, acción, fecha UTC y correlationId; la consulta devuelve eventos en orden temporal",
    "Registrar logs técnicos estructurados con correlationId para cada petición, incluidos los rechazos, sin secretos",
    "Health responde si el proceso está vivo; readiness comprueba la base de datos y las migraciones aplicadas; el proveedor de IA no forma parte de readiness"
  ],
  "outputs": [
    {"condition": "Listado válido", "result": "200 con items, page, pageSize y total del alcance; una página fuera de rango devuelve items vacío"},
    {"condition": "Tablero", "result": "200 con conteos por estado y cantidades de inventario según el rol"},
    {"condition": "Historial de una solicitud", "result": "200 con los eventos en orden temporal"},
    {"condition": "Parámetros inválidos", "result": "422"},
    {"condition": "Sin identidad, rol no permitido o recurso ajeno", "result": "401, 403 o 404 según SPEC-01; nunca conteos de otra organización"},
    {"condition": "Health y readiness", "result": "Health 200 si el proceso vive; readiness 200 solo con base disponible y migraciones aplicadas, 503 en otro caso"}
  ],
  "businessRules": [
    "RF08: listados paginados con máximo 100 elementos por página, filtros por estado, prioridad y zona y tablero con cantidades por estado; el solicitante ve solo sus solicitudes",
    "El tablero permite observar solicitudes por estado y cantidades disponibles y reservadas (objetivos del cliente, §1)",
    "RF09: se registra cada transición exitosa y cada aprobación de resumen con identidad, organización, recurso, acción, fecha UTC y correlationId; no hay edición ni borrado por API; un rechazo solo genera log técnico",
    "RF02: listados, auditoría y consultas agregadas respetan el límite de organización",
    "RNF03: p95 menor o igual a 700 ms y errores menores al 1 % con 20 usuarios virtuales durante 60 s tras 5 s de calentamiento, sobre 2.000 solicitudes sembradas, sin IA; reportar hardware y herramienta",
    "RNF04: iniciar desde un clon limpio en 15 min o menos con versiones fijadas, migraciones, seed y un comando documentado; la descarga inicial se reporta aparte",
    "RNF06: logs estructurados con correlationId sin secretos; health y readiness diferenciados",
    "El seed pequeño y el seed de carga de 2.000 solicitudes son independientes (enunciado §12)"
  ],
  "edgeCases": [
    "Página fuera de rango y filtros combinados sin coincidencias",
    "pageSize 0, 1, 100 y 101",
    "Empates de fecha de creación en la ordenación",
    "correlationId malicioso o excesivamente largo",
    "Solicitante sin solicitudes",
    "Auditor de ORG-A con el identificador de un evento o solicitud de ORG-B",
    "Base detenida durante la comprobación de readiness"
  ],
  "assumptions": [
    "pageSize por defecto 20 y orden por fecha de creación e identificador (decisión del equipo)",
    "Formato UUID para correlationId (decisión del equipo)",
    "RNF03, RNF04 y health y readiness se ubican en esta SPEC porque cubren consultas y operación; ninguna otra SPEC trata la operación"
  ],
  "openQuestions": [
    "CL-16: respuesta afirmativa docente reportada por el coordinador; evidencia original pendiente",
    "CL-17: Coordinador consulta historial de su organización y Solicitante de sus solicitudes según respuesta reportada; evidencia original pendiente",
    "CL-18: tablero propio sin inventario para Solicitante según respuesta reportada; evidencia original pendiente",
    "CL-15: retención de auditoría y de logs técnicos (equipo)"
  ],
  "dependencies": [
    "SPEC-01: alcance por rol y organización",
    "SPEC-02, SPEC-03 y SPEC-05: producen los eventos de auditoría",
    "Seed de carga con 2.000 solicitudes y herramienta de carga k6 o equivalente"
  ],
  "acceptanceCriteria": [
    {
      "id": "AC-04-01",
      "requirements": ["RF08", "RF02"],
      "kind": "permiso",
      "given": "un solicitante de ORG-A con solicitudes propias y existen solicitudes de otro solicitante de ORG-A y de ORG-B",
      "when": "lista solicitudes",
      "then": "solo recibe las propias; total cuenta solo las propias; pageSize nunca supera 100"
    },
    {
      "id": "AC-04-02",
      "requirements": ["RF08", "RF02"],
      "kind": "permiso",
      "given": "solicitudes de ORG-A y ORG-B en varios estados",
      "when": "el coordinador, la bodega y el auditor de ORG-A filtran por estado, prioridad y zona y consultan el tablero",
      "then": "resultados y conteos incluyen solo solicitudes de ORG-A"
    },
    {
      "id": "AC-04-03",
      "requirements": ["RF09"],
      "kind": "positivo",
      "given": "una transición exitosa de cada tipo (envío, reserva, despacho, entrega y cancelación) y una aprobación de resumen",
      "when": "cada operación se confirma",
      "then": "existe exactamente un evento por operación con identidad, organización, recurso, acción, fecha UTC y correlationId, confirmado en la misma transacción que el cambio de estado o inventario"
    },
    {
      "id": "AC-04-04",
      "requirements": ["RF09"],
      "kind": "negativo",
      "given": "el contrato OpenAPI publicado y una operación rechazada con 409, 403 o 422",
      "when": "se revisan las rutas y se consulta la auditoría después del rechazo",
      "then": "no existe ruta que edite o borre auditoría; el rechazo deja como máximo un log técnico y ningún evento de transición exitosa"
    },
    {
      "id": "AC-04-05",
      "requirements": ["RF08"],
      "kind": "limite",
      "given": "un usuario autorizado",
      "when": "pide pageSize 0, 101 o no entero, page 0 o un estado desconocido, y después una página fuera de rango",
      "then": "los parámetros inválidos reciben 422; la página fuera de rango recibe 200 con items vacío y sin datos ajenos"
    },
    {
      "id": "AC-04-06",
      "requirements": ["RF08"],
      "kind": "positivo",
      "given": "datos conocidos de ORG-A sembrados para la prueba",
      "when": "un usuario de la organización combina filtros de estado, prioridad y zona",
      "then": "items contiene solo las coincidencias autorizadas; total y tablero coinciden con una consulta directa a la base con el mismo alcance, calculada de forma independiente"
    },
    {
      "id": "AC-04-07",
      "requirements": ["RF09", "RNF06"],
      "kind": "positivo",
      "given": "una transición enviada con un X-Correlation-Id válido",
      "when": "se completa",
      "then": "el mismo correlationId aparece en la respuesta, en el log estructurado y en el evento; actor y organización son correctos y la fecha está en UTC"
    },
    {
      "id": "AC-04-08",
      "requirements": ["RF09"],
      "kind": "negativo",
      "given": "eventos de auditoría existentes",
      "when": "se envían PUT, PATCH y DELETE a las rutas de auditoría",
      "then": "reciben 404 o 405 y el contenido de la auditoría es idéntico antes y después"
    },
    {
      "id": "AC-04-09",
      "requirements": ["RNF03"],
      "kind": "positivo",
      "given": "el seed de carga con 2.000 solicitudes y la IA excluida",
      "when": "k6 o una herramienta equivalente ejecuta 20 usuarios virtuales sobre listados y tablero, descarta 5 s de calentamiento y mide 60 s",
      "then": "p95 es menor o igual a 700 ms por endpoint y la tasa de errores menor al 1 %; se conservan la salida real, el hardware, la herramienta y su versión"
    },
    {
      "id": "AC-04-10",
      "requirements": ["RF08", "RF02"],
      "kind": "permiso",
      "given": "inventario sembrado de ORG-A y ORG-B",
      "when": "bodega, auditor y coordinador de ORG-A consultan inventario, y el solicitante de ORG-A intenta consultarlo y consulta su tablero",
      "then": "bodega, auditor y coordinador ven físico, reservado y disponible consistentes solo de ORG-A; disponible igual a físico menos reservado; el solicitante recibe 403 en inventario y su tablero muestra solo conteos de solicitudes propias, sin inventario ni conteos ajenos (CL-16/18 reportadas)"
    },
    {
      "id": "AC-04-11",
      "requirements": ["RF09", "RF02"],
      "kind": "permiso",
      "given": "eventos de auditoría de ORG-A y de ORG-B",
      "when": "auditor y coordinador de ORG-A consultan historial; solicitante consulta historial propio y ajeno; bodega consulta historial; todos intentan acceder al historial de una solicitud de ORG-B",
      "then": "auditor y coordinador ven solo eventos de ORG-A; solicitante solo eventos de sus solicitudes y obtiene 404 para las ajenas; bodega recibe 403 para recurso de ORG-A; identificadores de ORG-B reciben 404 sin datos (CL-17 reportada)"
    },
    {
      "id": "AC-04-12",
      "requirements": ["RNF04"],
      "kind": "positivo",
      "given": "un clon limpio del commit evaluado con versiones fijadas y configuración de ejemplo sin secretos",
      "when": "se ejecuta el comando único documentado en el runbook",
      "then": "las migraciones y el seed pequeño quedan aplicados y la aplicación responde en 15 min o menos medidos sin contar la descarga inicial, que se reporta aparte; se conservan tiempos, comando y entorno"
    },
    {
      "id": "AC-04-13",
      "requirements": ["RNF06"],
      "kind": "fallo",
      "given": "la aplicación levantada",
      "when": "se consultan health y readiness con la base disponible y luego con la base detenida",
      "then": "health responde 200 en ambos casos; readiness responde 200 con base disponible y 503 sin base; el estado del proveedor de IA no afecta readiness"
    },
    {
      "id": "AC-04-14",
      "requirements": ["RNF05"],
      "kind": "positivo",
      "given": "listados, filtros, tablero e historial a 390 y 1366 px de ancho",
      "when": "se navegan y filtran solo con teclado, provocando también un filtro inválido",
      "then": "no hay desbordamiento horizontal; los controles tienen etiqueta; el error queda asociado al filtro; la verificación manual se registra sin afirmar conformidad WCAG total"
    }
  ]
}
```
<!-- spec-data:end -->

## Decisiones y aclaraciones

- RNF04 corresponde a arranque reproducible y no a rendimiento: AC-04-09 cubre solo RNF03 y AC-04-12 cubre RNF04 (diferencia D-06).
- Campos del evento según RF09, incluido «recurso» (diferencia D-11). Aislamiento de auditoría explícito en AC-04-11 (diferencia D-12).
- CL-16/17/18: respuesta afirmativa reportada por Conde José; evidencia original pendiente. Permisos y AC-04-10/11 modificados requieren nueva revisión sobre nuevo SHA.

## Riesgos y plan de prueba

- R-10 rendimiento y accesibilidad; R-11 auditoría y negocio divergentes.
- Pruebas previstas: API con base real para alcance y conteos con oráculo independiente, prueba de carga con seed separado, clon limpio cronometrado, health y readiness con base detenida, revisión manual de teclado y anchos.

## Cambios de versión

- 0.1: borrador inicial (commit `1aeacbf`).
- 0.2-draft: ampliación a 10 AC sin fuente oficial.
- 0.3.1-draft: cotejo con el enunciado; corrección de RNF04, campos RF09, AC-04-11 a AC-04-14 (auditoría aislada, arranque, health y readiness, RNF05).

Cambio 0.3.1-draft: CL-16/17/18 confirmadas según reporte del coordinador; sin aprobación humana de esta nueva versión. Otros hallazgos G1 siguen pendientes.
