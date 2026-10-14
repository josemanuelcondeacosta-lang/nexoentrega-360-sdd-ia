# Fuentes y alcance de G1

1. **Enunciado oficial y anexos — RECIBIDOS** el 2026-10-14 y guardados sin cambios en `docs/examen-oficial/` (inventario con SHA-256 en [inventario.md](examen-oficial/inventario.md)). Máxima autoridad: «los valores explícitos de este enunciado prevalecen sobre supuestos o sugerencias del modelo» (§5).
2. Misión del usuario «DESARROLLAR Y ENTREGAR NEXOENTREGA 360» (2026-10-13): contexto de organización; no sustituye al enunciado.
3. Borrador inicial del repositorio: commit `1aeacbf681aa711c78c4a4488f5d7bb2acaf43cd`; ampliación 0.2-draft en la rama `docs/g1-specifications`.

La versión 0.3-draft de las SPEC aplica el cotejo de [cotejo-oficial-G1.md](reviews/cotejo-oficial-G1.md). Ningún requisito se declara cumplido: la cobertura es documental hasta que existan código, pruebas y revisión humana.

| Tema | Fuente oficial | Estado |
|---|---|---|
| Roles, permisos y respuestas 401/403/404 | OF-01 §2, p. 3 | Resuelto por enunciado; quedan CL-16, CL-17 y CL-18 para el docente |
| Prioridad NORMAL o ALTA, 1 a 10 líneas, cantidades 1 a 500 | OF-01 §3 RF03, p. 4 | Resuelto por enunciado |
| Zonas, formato de fecha, límite de observaciones, código | OF-01 §5 «Puntos que debe concretar el equipo» | Decisión del equipo: CL-01, CL-02, CL-03 y CL-10 |
| Idempotencia: alcance y retención mínima | OF-01 §3 RF07, p. 4 | Resuelto; política de errores y reintentos es decisión del equipo (CL-04, CL-11) |
| Campos del asistente y contrato de salida | OF-01 §4 RF10–RF11, p. 5; anexo `ai-output.schema.json` | Resuelto por enunciado |
| Textos RNF01–RNF06 | OF-01 §5, p. 6 | Resuelto por enunciado |
| Datos de prueba, cuentas y oráculos | Anexos `fixture-scenarios.json` y `acceptance-oracles.md` | Recibidos |

## Matriz oficial de permisos (enunciado §2)

Todas las operaciones requieren identidad autenticada; organización y rol salen de la identidad, nunca del navegador. Un recurso inexistente o de otra organización responde 404; una acción prohibida sobre un recurso de su organización responde 403.

| Operación | Solicitante | Coordinador | Bodega | Auditor |
|---|---|---|---|---|
| Crear y editar borrador | Solo los suyos | No | No | No |
| Enviar solicitud | Solo la suya | No | No | No |
| Consultar solicitudes | Solo las propias | Su organización | Su organización | Su organización |
| Cancelar ENVIADA o RESERVADA | Solo las propias | No | No | No |
| Reservar ENVIADA | No | Su organización | No | No |
| Despachar RESERVADA | No | No | Su organización | No |
| Registrar ENTREGADA sobre DESPACHADA | No | Su organización | No | No |
| Consultar inventario | No | **Pendiente CL-16** (supuesto: lectura) | Su organización | Su organización |
| Consultar auditoría | **Pendiente CL-17** (supuesto: no) | **Pendiente CL-17** (supuesto: no) | **Pendiente CL-17** (supuesto: no) | Su organización |
| Tablero | **Pendiente CL-18** (supuesto: solo sus solicitudes) | Su organización | Su organización | Su organización |
| Usar y aprobar resúmenes de IA | No | Su organización | No | No |

La matriz 0.2 asignaba la cancelación al Coordinador y la entrega a Bodega; ambas asignaciones contradecían el enunciado y se corrigieron (diferencias D-01 y D-02 del cotejo).
