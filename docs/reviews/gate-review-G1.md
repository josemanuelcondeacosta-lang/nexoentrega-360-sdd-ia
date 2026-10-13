# Gate review G1 — plantilla sin aprobación

- Fecha y hora UTC: PENDIENTE
- Artefactos, versiones y SHA: PENDIENTE
- Autor(es): PENDIENTE
- Revisor(es) distinto(s): PENDIENTE
- Criterios verificados y comandos: PENDIENTE
- Hallazgos adversariales (con evidencia): PENDIENTE
- Decisiones y cambios: PENDIENTE
- Estado final: **NO APROBADO / PENDIENTE**

No completar aprobaciones ficticias. Guardar enlace a PR/commit real.

## Paquete candidato 0.2-draft

Autor de la ampliación: asistente Codex bajo instrucciones del usuario. No atribuir elaboración ni revisión a integrantes sin evidencia. Rama `docs/g1-specifications`; el SHA candidato debe obtenerse del commit de esta rama, no del commit inicial.

Artefactos: cinco SPEC y JSON derivados, constitution.md, requirements-source.md, clarification-log.md, risk-register.md, ai-trust-boundaries.md, coverage-matrix.md, initial-audit.md y evidencia documental en evidence/g1-validation.md.

## Lista de revisión humana

- [ ] Se recibió enunciado oficial/anexos y se cotejaron RF01–RF12 y RNF01–RNF06.
- [ ] Cinco historias y 57 AC con entradas/proceso/salidas, escenarios negativos y persistencia.
- [ ] Se revisaron rol, permisos, fecha, catálogos, TTL y whitelist IA; supuestos pendientes visibles.
- [ ] Reglas de concurrencia, reserva atómica, cancelación, despacho y auditoría son consistentes.
- [ ] Contrato IA y degradación no permiten escrituras privilegiadas ni datos ajenos.
- [ ] Se comprobó exportación JSON y matriz; los tests documentales no se confunden con pruebas de aplicación.
- [ ] Hallazgos resueltos o explícitamente bloqueantes, sin aprobaciones ficticias.
- [ ] Revisor humano distinto del autor registrado con fecha UTC, identidad, SHA y evidencia/enlace.

## Registro de hallazgos para el revisor

| ID | Hallazgo | Severidad | Disposición | Resolución humana |
|---|---|---|---|---|
| G1-H01 | Enunciado/anexos ausentes | Bloqueante para cierre | Solicitar y cotejar | Pendiente |
| G1-H02 | Semántica y asignación RNF provisional | Bloqueante para cobertura definitiva | Confirmar CL-13 | Pendiente |
| G1-H03 | Matriz de roles/HTTP/cancelación propuesta | Bloqueante para funcionalidades afectadas | Confirmar CL-09 | Pendiente |
| G1-H04 | Whitelist y contrato IA sin anexo | Bloqueante para SPEC-05 | Confirmar CL-12 | Pendiente |
| G1-H05 | TTL, errores y retención idempotente propuestos | Bloqueante para SPEC-03 | Confirmar CL-04/11 | Pendiente |

Decisión por SPEC: SPEC-01 pendiente; SPEC-02 pendiente; SPEC-03 pendiente; SPEC-04 pendiente; SPEC-05 pendiente. No basta una reacción informal sin identificar artefactos y SHA. La revisión no acredita que pruebas futuras hayan sido ejecutadas.
