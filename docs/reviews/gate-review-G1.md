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

## Paquete candidato 0.3-draft — cotejo oficial

Elaboración asistida por IA (Claude) a solicitud de Castro Edier, 2026-10-14 UTC; registro `docs/evidence/ai-log/AI-001-cotejo-oficial-g1.md`. Rama `docs/g1-edier-cotejo-oficial`, creada desde `6fa084c` de `docs/g1-specifications`; el SHA candidato es el del commit de esa rama que introduce este apartado. No acredita revisión humana de ningún integrante.

Cambios: originales oficiales y anexos en `docs/examen-oficial/` con SHA-256; cotejo real en `cotejo-oficial-G1.md` (D-01 a D-24); SPEC 0.3-draft en el formato del anexo 13 con 68 AC; exportador, matriz y traza CSV regenerados desde la fuente única; registro de aclaraciones reclasificado (CL-16 a CL-18 para el docente).

## Lista de revisión humana

- [ ] Se recibió enunciado oficial/anexos y se cotejaron RF01–RF12 y RNF01–RNF06 (cotejo preparado en 0.3; falta verificación humana fila por fila).
- [ ] Cinco historias y 68 AC con entradas tipadas, proceso, salidas, escenarios positivos, negativos, límites, permisos y fallos, y efectos persistidos.
- [ ] Se revisaron rol, permisos, fecha, catálogos, TTL y whitelist IA; supuestos pendientes visibles.
- [ ] Reglas de concurrencia, reserva atómica, cancelación, despacho y auditoría son consistentes.
- [ ] Contrato IA y degradación no permiten escrituras privilegiadas ni datos ajenos.
- [ ] Se comprobó exportación JSON y matriz; los tests documentales no se confunden con pruebas de aplicación.
- [ ] Hallazgos resueltos o explícitamente bloqueantes, sin aprobaciones ficticias.
- [ ] Revisor humano distinto del autor registrado con fecha UTC, identidad, SHA y evidencia/enlace.

## Registro de hallazgos para el revisor

| ID | Hallazgo | Severidad | Disposición | Resolución humana |
|---|---|---|---|---|
| G1-H01 | Enunciado/anexos ausentes | Bloqueante para cierre | Recibidos y cotejados en 0.3 (`cotejo-oficial-G1.md`) | Pendiente: verificar el cotejo |
| G1-H02 | Semántica y asignación RNF provisional | Bloqueante para cobertura definitiva | Textos oficiales aplicados; RNF04 corregido (D-06) | Pendiente |
| G1-H03 | Matriz de roles/HTTP/cancelación propuesta | Bloqueante para funcionalidades afectadas | Matriz oficial aplicada (D-01, D-02); quedan CL-16 a CL-18 para el docente | Pendiente |
| G1-H04 | Whitelist y contrato IA sin anexo | Bloqueante para SPEC-05 | Campos y esquema oficiales aplicados (D-04, D-05, D-17) | Pendiente |
| G1-H05 | TTL, errores y retención idempotente propuestos | Bloqueante para SPEC-03 | RF07 aplicado; política de errores y retención como decisión del equipo (CL-04, CL-11) | Pendiente: acuerdo del equipo |
| G1-H06 | Formato SPEC sin entradas tipadas ni AC dado/cuando/entonces | Alta para la rúbrica de especificaciones | Migrado al anexo 13 (D-15) | Pendiente |
| G1-H07 | Sin CI de coincidencia JSON y constitution sin motivo/responsable | Media | Abiertos (D-20, D-21) | Pendiente |

Decisión por SPEC: SPEC-01 pendiente; SPEC-02 pendiente; SPEC-03 pendiente; SPEC-04 pendiente; SPEC-05 pendiente. No basta una reacción informal sin identificar artefactos y SHA. La revisión no acredita que pruebas futuras hayan sido ejecutadas.

## Organización propuesta de revisión

Revisora principal propuesta: **Cantillo Carolina**. Apoyo: **Castro Edier** (inventario/transacciones), **Conde José** (coordinación/UI/integración) y **Vega José** (IA/seguridad/calidad). Asignación pendiente de aceptación personal; ninguna revisión se ha registrado por esa propuesta. Detalle: `docs/team/plan-colaboracion.md`.

PR existente: https://github.com/josemanuelcondeacosta-lang/nexoentrega-360-sdd-ia/pull/1. El coordinador comunica el SHA actual tras cada corrección. No usar automáticamente el SHA inicial de G1 como si incluyera cambios posteriores.

## Criterios verificables para el equipo

| Comprobación | Responsable propuesto | Evidencia mínima para marcar cumplida |
|---|---|---|
| Inventario completo de enunciado, anexos y versiones | Conde José prepara; Carolina coteja | Nombre/procedencia/hash y referencias; inventario sin documentos faltantes |
| Cada condición RF01–12/RNF01–06 tiene correspondencia o brecha | Carolina | Fila de cotejo por condición con página/sección y SPEC/AC; sin cobertura solo por número |
| Cinco historias; entradas, proceso, salidas y AC positivos/negativos | Carolina | Revisión de cada SPEC y efectos observables/persistidos; ningún supuesto presentado como acuerdo |
| Reglas de stock, transiciones y rollback completas | Edier | Escenarios con stock inicial/final y estado; revisión de reserva parcial, doble reserva y fallo de auditoría |
| Idempotencia 24h, conflicto, ámbito y conservación definidos | Edier | Comparación de CL-04/11 con texto oficial y resultados esperados en SPEC-03 |
| Permisos, 8 cuentas ficticias, 2 organizaciones y respuestas seguras | Vega José y Conde José | Matriz oficial confrontada, pruebas negativas previstas y campos de identidad del servidor |
| Contrato IA estricto, whitelist, pertenencia IDs, límites y fallback | Vega José | Contraste con anexo y escenarios inválidos/timeout/inyección; aprobación sin efectos privilegiados |
| Formularios/estados, accesibilidad y resolución móvil/escritorio | Conde José | Flujo previsto y criterios específicos contrastados con RNF; no afirmar UI probada |
| Markdown/JSON coherentes y matriz por AC consistente | Carolina y Vega José | Comandos ejecutados personalmente y salida, SHA y revisión de vínculos de cada AC |
| Hallazgos bloqueantes resueltos y cambios revisados | Carolina coordina; otro revisa cambios propios | Enlaces a correcciones y revisión sobre SHA final; decisiones por SPEC |

## Plantilla de registro individual — completar solo tras revisar

Copiar en una revisión/comentario de GitHub publicado desde la cuenta personal y enlazarlo aquí. No sustituir evidencia personal por un registro escrito en su nombre por Codex.

```text
Autor(es) del artefacto revisado / asistencia utilizada:
Revisor: nombre y cuenta GitHub:
Fecha/hora UTC (YYYY-MM-DDTHH:mm:ssZ):
PR y SHA completo revisado:
Fuentes oficiales usadas: archivo, versión, página/sección:
SPEC / AC / archivos revisados:
Comprobaciones realizadas y comandos efectivamente ejecutados:
Observaciones y hallazgos (requisito, evidencia y corrección propuesta):
Resultado por SPEC: pendiente / requiere cambios / aprobada:
Bloqueos restantes y razones:
Enlaces a revisión, commit propio y evidencia:
```

| Integrante | Autor del artefacto | SHA / UTC revisados | Observaciones / evidencia GitHub | Decisión real |
|---|---|---|---|---|
| Carolina | Por identificar según artefacto | Pendiente | Pendiente | Sin revisión recibida |
| Edier | Por identificar según artefacto | Pendiente | Pendiente | Sin revisión recibida |
| Conde José | Por identificar según artefacto | Pendiente | Pendiente | Sin revisión independiente recibida; no autoaprobar PR propio |
| Vega José | Por identificar según artefacto | Pendiente | Pendiente | Sin revisión recibida |

## Condiciones para desbloquear G1 y continuar

1. Recibir y cotejar texto completo/anexos mediante `cotejo-oficial-G1.md`; el resumen del coordinador no los sustituye.
2. Corregir diferencias verificadas y regenerar JSON/matriz sin atribuir esas correcciones a otra persona.
3. Obtener revisión real independiente y decisión explícita por SPEC sobre el SHA corregido; registrar quién revisó y cuándo en UTC.
4. Si un cambio posterior afecta una SPEC aprobada, renovar su revisión; no implementar mientras su aprobación esté pendiente.
5. Completar PR #1 y mantener evidencia de revisión antes de integrar. Después comenzar G2 (arquitectura, cuatro ADR, diagramas, OpenAPI y planificación) y preparar su revisión independiente.

Estado actual: **G1 PENDIENTE**. No se genera PDF final ni tag de entrega; no hay nueva prueba de aplicación declarada.

## Corrección documental de constitución preparada en G2

Actualización CL-16/17/18: Conde José reporta respuesta afirmativa de la profesora en esta conversación (2026-10-14); no se registra mensaje directo ni UTC/canal original. SPEC-01/04 pasan a 0.3.1-draft y sus permisos/AC se actualizan junto al contrato. Las revisiones previas sobre `cf9d5f5` no aprueban esta versión nueva: Carolina/Vega deben revisar el nuevo SHA. G1 sigue pendiente y otros hallazgos no se cierran automáticamente.

D-21 / parte de G1-H07: `docs/constitution.md` ahora incluye motivo, evidencia, responsable propuesto y criterio de excepción, según anexo 01. Preparado por Codex a solicitud de Conde José, 2026-10-14; **revisión humana pendiente**, no cierre aprobado. D-20 (CI de SPEC) permanece abierto. CL-16/17/18 tienen mensaje preparado en `docs/clarifications/envio-docente-CL16-18.md`, pero Codex no las ha enviado: destinatario/canal pendientes.
