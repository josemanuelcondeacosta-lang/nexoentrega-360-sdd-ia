# Revisión completa y organización documental — 2026-10-14

Preparación: Codex a solicitud de Conde José. Rama `docs/consolidated-review`, basada en `b1dbbf93d9800b69d32daec352aacf754f876ebe` de `docs/g2-architecture`. No se fusionó ningún PR ni se publicaron revisiones en nombre de integrantes. Este informe no declara G1/G2 aprobados.

## Ubicación de los artefactos

| Artefacto | Ubicación canónica | Fuente conservada |
|---|---|---|
| Enunciado, ZIP y anexos oficiales | `docs/examen-oficial/` | Trabajo publicado por Edier; manifiesto SHA256SUMS |
| Cinco historias/SPEC y derivados | `specs/SPEC-01.md` a `SPEC-05.md`, `specs/generated/` | Paquete `b1dbbf9`; 01/04 0.3.1-draft, otras 0.3-draft |
| Constitución, aclaraciones y respuesta docente | `docs/constitution.md`, `docs/clarifications/`, `docs/evidence/respuesta-docente-CL16-18.md` | Paquete del coordinador; sin cambiar la autoridad de la evidencia |
| ADR-001 | `docs/adr/ADR-001-estructura-limites.md` | Paquete coordinador |
| ADR-002 | `docs/adr/ADR-002-transacciones-idempotencia.md` | Edier, `68eab81bc6bb382daa42d117b09203afc32fbfdc` |
| ADR-003 | `docs/adr/ADR-003-autenticacion-aislamiento.md` | Carolina, `7637056ffa5cf6c623e5bae424eead2a2d0fbeac` |
| ADR-004 | `docs/adr/ADR-004-integracion-ia-degradacion.md` | Vega, ya en base |
| Contexto/componentes | `docs/architecture/contexto.md`, `componentes.md` | Paquete coordinador |
| Datos/estados/reserva concurrente | `docs/architecture/datos.md`, `estados.md`, `secuencia-reserva-concurrente.md` | Edier, `68eab81` |
| Secuencia IA con fallo | `docs/architecture/secuencia-ia-con-fallo.md` | Vega, ya en base |
| Contratos HTTP/IA | `contracts/openapi.yaml`, `openapi-ia.yaml`, `ai-output.schema.json` | Base validada; pendientes R-V01/02 visibles |
| Plan y tareas | `docs/plan.md`, `docs/tasks.md` | Copia exacta de Carolina, `7637056` |
| Estrategia de pruebas | `docs/testing/estrategia-pruebas.md` | Vega, `d8c166c826126da2b43754c6d69bea239ed921f9`; incluye corrección R-E01 |
| Seis registros de IA | `docs/evidence/ai-log/AI-001…AI-006…` | Conservados; AI-005 Edier y AI-006 Vega añadidos desde sus ramas |
| Revisiones individuales vigentes | `docs/reviews/aportes/` | Edier/Vega conservados; Carolina corregida según PR #9 |
| Original previo de Carolina | `docs/reviews/archive/carolina-G1-original-0f98f68.md` | Copia exacta de `0f98f68966ebcca0e620b7dfa83594bfc37dfbf1`; **histórico, contiene SHA no verificable** |
| Matriz y traza | `docs/coverage-matrix.md`, `docs/evidence/traceability.csv` | Generadas desde SPEC; no editadas manualmente |

El original histórico no es la revisión vigente. No copiar su afirmación de aprobación sobre `3b8c9d1` al acta ni al PDF como evidencia válida. El registro canónico de Carolina distingue SHA anterior y candidato pendiente; corregirlo requiere su revisión personal.

## Revisiones humanas encontradas en GitHub

| Persona / PR | SHA / contenido verificable | Alcance y límite |
|---|---|---|
| Vega, PR #2 | `cf9d5f5`; revisión de SPEC-01/05 | Aprobadas con observaciones; H-V01…H-V07 requieren resolución antes de implementar |
| Carolina, comentario PR #2 | `cf9d5f5`; SPEC-02/03/04 | Comentario dice «en revisión»; archivos posteriores dicen aprobadas, pero citan SHA no verificable. Falta confirmación coherente del candidato actualizado |
| Edier, PR #3 | `1b3bb37`; aporte de Vega | Aprobó revisión documental de Vega; no aprueba aplicación ni todos los hallazgos |
| Edier, PR #4 | `a13aa17`; paquete IA G2 | ADR-004/secuencia/contrato aprobados en su revisión; estrategia necesitaba corregir concurrencia R-E01 |
| Vega, PR #4 | Comentario de corrección `1a9fe52` | Estrategia exige procesos realmente concurrentes; AI-006 registra su decisión |
| Vega, PR #5 | `b1dbbf9`; paquete G2 y SPEC-01 actualizada | Renueva aprobación de SPEC-01 0.3.1-draft; contrato requiere cambios R-V01/02/03 |
| PR #6/#7/#9 | Sin revisión nueva de Carolina del candidato encontrado | PR abiertos; no se presumen revisados o fusionados |

Fuentes: [PR #2](https://github.com/josemanuelcondeacosta-lang/nexoentrega-360-sdd-ia/pull/2), [PR #3](https://github.com/josemanuelcondeacosta-lang/nexoentrega-360-sdd-ia/pull/3), [PR #4](https://github.com/josemanuelcondeacosta-lang/nexoentrega-360-sdd-ia/pull/4), [PR #5](https://github.com/josemanuelcondeacosta-lang/nexoentrega-360-sdd-ia/pull/5), [comentario Carolina](https://github.com/josemanuelcondeacosta-lang/nexoentrega-360-sdd-ia/pull/2#issuecomment-6173494323). Se leyeron todos los PR #1…#9 y sus revisiones/comentarios. No se atribuye una ejecución personal a nadie más allá de lo registrado por esa persona.

## Hallazgos y acciones restantes

| ID | Hallazgo | Acción necesaria / estado |
|---|---|---|
| RC-01 | Carolina no identifica una revisión verificable del SHA actualizado; `3b8c9d1` no existe en las referencias consultadas | Registro corregido traído del PR #9; aprobación actualizada pendiente de Carolina |
| R-V01 | Escrituras IA no declaran header CSRF, mientras las base sí | Fragmento IA vigente todavía no contiene CsrfToken. Vega debe corregir; regenerar contrato y revisar ADR-003; abierto |
| R-V02 | Historial propio/ajeno del Solicitante usa 404 incluso en misma organización | Alinear SPEC-04 AC-04-11 y contrato con 403 en acción prohibida dentro de organización, 404 solo inexistente/interorganización. Abierto; renovar revisión del cambio |
| R-V03 / R-E01 | Estrategia antigua usaba servidor sin concurrencia real | Traída copia exacta actualizada de Vega; corrección documental presente. No prueba de concurrencia ejecutada |
| RC-02 | G1/H-V/H-E todavía abiertos y gates desactualizados | Registrar decisiones por SPEC/SHA y resolver hallazgos antes de implementación; no inventar cierre |
| RC-03 | Plan usa nombres `kits/request_items/audit_logs`; datos propone `inventory_items/request_lines/audit_events` | Unificar nombres en diseño/migraciones antes de implementar; no cambiar silenciosamente archivos de autor |
| RC-04 | Tareas agregan migración/seed/CI, pero dependencias incompletas | Asegurar autenticación depende de migración/seed; inventario/IA dependen de solicitudes y auditoría disponible desde transiciones. Incluir gates, módulos/tests/rutas previstos y revisión |
| RC-05 | TSK-05 lista RF11/RNF05, sin explicitar RF10/RF12/RNF06; plan no justifica autoría conjunta | Completar cobertura y distinguir autores reales de responsables propuestos; no atribuir trabajo personal por el encabezado |
| RC-06 | Gates G2 todavía enumeraban ADR-002/003 y diagramas como inexistentes | Ubicados en esta rama; preparación completa de vistas no equivale a aceptación ni plan completo |
| RC-07 | Una validación OpenAPI puede pasar con semántica/CSRF incorrectos | No confundir validez documental con cumplimiento de permisos y pruebas de producto |
| RC-08 | Cambios aún en ramas; `main` conserva estado inicial | Integrar solamente después de revisión humana. Esta rama no modifica `main` ni el estado de PR |

## Comprobaciones ejecutadas en esta consolidación

Windows, PowerShell, Python 3.11.9; comandos en árbol candidato antes de commit:

```text
python scripts/export_specs.py --check
5 SPEC y 68 AC validados; JSON coherentes
python scripts/build_coverage.py --check
Matriz y traza coherentes con las SPEC
python -m unittest discover -s scripts -p 'test_*.py'
Ran 14 tests in 0.849s — OK
.venv-docs/Scripts/python.exe scripts/build_openapi.py --check
Contrato integrado coherente con base y fragmento IA
.venv-docs/Scripts/python.exe scripts/validate_openapi.py
OpenAPI 3.1.1 válido: 19 rutas, 21 operaciones; IA preservada;
2 ejemplos válidos y 3 casos negativos rechazados
```

Escaneo UTF-8/caracteres de control: 67 Markdown/YAML, sin errores. Integridad oficial: 29 entradas de manifiesto verificadas (4 coinciden con bytes locales; 25 con blobs Git). Windows transforma LF a CRLF al extraer archivos de texto, por eso su hash local difiere; blobs originales coinciden. No se alteraron originales para forzar hashes.

No se ejecutaron aplicación, base, API, carga, E2E o CI remoto. Mermaid se conserva como fuente, sin render automatizado. La validación de contratos no cierra R-V01/02.

## Actualización técnica posterior — pendientes atendidos por Codex

Las filas anteriores son hallazgos del corte original `4b75798`, no estado final de esta rama. Se incorpora fragmento IA v0.2.0 de Vega (`833e9b9`), se regenera OpenAPI, y se corrige R-V02 en SPEC-04 0.3.2-draft/AC-04-11 y contrato: 403 misma organización/acción prohibida; 404 inexistente/interorganización. CSRF y política de errores tienen verificación documental de regresión. R-V01/R-V02: **corregidos documentalmente, revisión independiente pendiente**, no implementación probada.

RC-03/04/05: plan y tareas alineados con nombres de datos.md, dependencias de migración/seed/auditoría, cobertura IA, módulos/tests futuros, responsabilidades del equipo y condición de término. RC-01 continúa pendiente de revisión personal de Carolina. RC-02/H-V/H-E restantes no se cierran automáticamente. R-V03/R-E01 conservan copia corregida de estrategia. RC-08 permanece: sin merge a main ni aprobación global.

Evidencia nueva: `docs/evidence/correcciones-RV01-RV02-plan.md`; guía accesible para revisión humana: `carolina-revision-sencilla.md`.
