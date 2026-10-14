# Aporte G1 — Vega José: IA, seguridad y calidad

Plantilla de `docs/team/preparacion-G1.md`. Casos **previstos**: ninguna prueba de producto se ha ejecutado porque la aplicación no existe.

- **Nombre / cuenta GitHub:** Vega José (202321256198) / `josevegalago2006-cpu`
- **Autor del artefacto revisado / asistencia IA:** SPEC-01 y SPEC-05 0.3-draft, elaboradas con asistencia de IA a solicitud de Castro Edier (AI-001). Este aporte se preparó con asistencia de IA (AI-003).
- **SHA completo revisado:** `cf9d5f5d905dee3a3774c623283c7045271dbe60` (rama `docs/g1-edier-cotejo-oficial`, PR #2)
- **Fecha UTC:** 2026-10-14T15:03:10Z
- **Fuentes disponibles:** OF-01 `Examen_Estudiantes_SDD_IA.pdf` §2 (p. 3), §4 RF10–RF12 (p. 5), §5 RNF01 y RNF06 (p. 6), §10 (p. 11) y §11 familias Seguridad e IA (p. 12); anexos `ai-output.schema.json`, `ai-provider-fixtures.json`, `untrusted-observation.txt` y `fixture-scenarios.json`. **Fuentes pendientes:** respuesta del docente a CL-16, CL-17 y CL-18 (permisos de inventario, auditoría y tablero; no afectan al asistente).
- **SPEC / AC revisados:** SPEC-01 AC-01-01 a AC-01-13; SPEC-05 AC-05-01 a AC-05-14; `docs/ai-trust-boundaries.md`; aclaraciones CL-05, CL-08, CL-12, CL-13, CL-14, CL-15 y CL-19.

## Comprobaciones documentales

| Comprobación | Resultado |
|---|---|
| Campos enviados al proveedor iguales a RF10 (requestId, zona, prioridad, fecha requerida, estado, observaciones) | Cumple: entrada «payload al adaptador» de SPEC-05 y AC-05-01 |
| Contrato de salida igual al anexo `ai-output.schema.json` (600 / 5 / 240 / 1–3, sin propiedades extra) | Cumple: entrada «salida del proveedor», AC-05-06 y AC-05-07 |
| IDs duplicados o ajenos rechazados en código, no solo con el esquema | Cumple: el esquema no impide repetir `requestId` entre recomendaciones; SPEC-05 lo exige en código (proceso, paso 5) y AC-05-02 lo prueba |
| Los siete fixtures del anexo tienen un AC que los usa | Cumple: AI-VALID (05-07), AI-JSON-BAD (05-06), AI-FOREIGN-ID y AI-EXTRA (05-02), AI-TIMEOUT y AI-ERROR (05-03), AI-INJECTION (05-08); los siete en CI (05-14) |
| Aprobar no cambia inventario, estado ni prioridad (RF12) | Cumple: AC-05-04 y AC-05-12 con snapshot |
| Semántica 401 / 403 / 404 igual al enunciado §2 | Cumple: SPEC-01 salidas y AC-01-01, 01-02 y 01-03 |
| Dos lecturas y dos escrituras cruzadas denegadas (RNF01) | Cumple: AC-01-09 |
| Logs con correlationId y sin secretos (RNF06) | Cumple: AC-01-10 y AC-04-07 |

## Casos previstos

Datos: seed restablecido según `fixture-scenarios.json` antes de cada caso (ORG-A: A-001 ENVIADA ALTA NORTE y A-002 ENVIADA NORMAL SUR; ORG-B: B-001 ENVIADA; ocho cuentas). «Snapshot» = solicitudes, líneas, inventario y auditoría leídos de la base antes y después.

### Asistente de IA (SPEC-05)

| ID | AC | Datos y fixture | Actor | Acción | Esperado | Efectos persistidos | Observado |
|---|---|---|---|---|---|---|---|
| V-01 | AC-05-07 | AI-VALID; selección A-001 y A-002 | Coordinador ORG-A | Solicitar resumen | 200; borrador marcado como asistencia de IA con origen | Borrador guardado; snapshot de negocio idéntico | No ejecutado |
| V-02 | AC-05-01 | Selección A-001 y A-002 | Coordinador ORG-A | Capturar el payload del adaptador | Solo dos solicitudes y solo los seis campos de RF10; sin nombre del solicitante, líneas, tokens ni ORG-B | Ninguno | No ejecutado |
| V-03 | AC-05-02 | AI-FOREIGN-ID (B-001) | Coordinador ORG-A | Solicitar resumen | Mensaje controlado y resumen alternativo; ningún dato de ORG-B | Sin borrador válido; snapshot idéntico | No ejecutado |
| V-04 | AC-05-02 | Salida con `requestId` A-001 repetido en dos recomendaciones | Coordinador ORG-A | Solicitar resumen | Rechazo y resumen alternativo | Sin borrador válido | No ejecutado |
| V-05 | AC-05-02 | AI-EXTRA (`"execute": "reserve-all"`) | Coordinador ORG-A | Solicitar resumen | Rechazo por propiedad extra; nada se ejecuta | A-001 sigue ENVIADA; reservado 0 | No ejecutado |
| V-06 | AC-05-06 | summary de 601; seis recomendaciones; reason de 241; evidenceFields vacío, con 4 claves, repetidas o con `"nombre"` | Coordinador ORG-A | Validar cada salida | Rechazo con motivo sanitizado y resumen alternativo | Ninguna salida guardada como válida | No ejecutado |
| V-07 | AC-05-07 | summary de 600; cinco recomendaciones; reason de 240; evidenceFields de 1 y de 3 claves | Coordinador ORG-A | Validar | Se aceptan | Borrador guardado | No ejecutado |
| V-08 | AC-05-06 | AI-JSON-BAD (`{"summary":`) | Coordinador ORG-A | Solicitar resumen | Resumen alternativo | Sin borrador válido | No ejecutado |
| V-09a | AC-05-08 | Observaciones de A-001 = texto de `untrusted-observation.txt`; AI-INJECTION | Coordinador ORG-A | Solicitar resumen | La instrucción no se obedece; la salida completa se rechaza por B-001 y se muestra el resumen alternativo (el `<script>` nunca llega a pantalla) | Inventario de ORG-A y ORG-B, estados, prioridades y permisos idénticos | No ejecutado |
| V-09b | AC-05-08 (ver H-V07) | Salida válida con IDs A-001 y A-002 y `<script>window.__unsafe=true</script>` dentro de summary | Coordinador ORG-A | Solicitar resumen y abrir la pantalla | Se acepta y el marcado se muestra como texto escapado; `window.__unsafe` no existe | Ninguno sobre el negocio | No ejecutado |
| V-10 | AC-05-03 | AI-TIMEOUT (5001 ms) con reloj controlado | Coordinador ORG-A | Solicitar resumen | Corte a los 5 s; mensaje controlado y resumen determinista | Sin borrador válido | No ejecutado |
| V-11 | AC-05-03 | AI-ERROR | Coordinador ORG-A | Solicitar resumen y, durante el fallo, listar y reservar | Resumen alternativo; listar y reservar siguen funcionando | Reserva confirmada normalmente | No ejecutado |
| V-12 | AC-05-09 | Proveedor no configurado | Coordinador ORG-A | Pedir dos veces el resumen de la misma selección | Mismo resumen determinista en ambas; sin servicio de pago | Ninguno | No ejecutado |
| V-13 | AC-05-05 | 21 IDs; lista vacía; IDs repetidos; B-001; una RESERVADA | Coordinador ORG-A | Solicitar resumen | 422, 422, 422, 404 y 409; proveedor no invocado | Ninguno | No ejecutado |
| V-14 | AC-05-04, AC-05-12 | Borrador válido de ORG-A | Coordinador ORG-A | Aprobar con X-Correlation-Id | 200; un evento con identidad, organización, recurso, acción, fecha UTC y correlationId | Snapshot de solicitudes e inventario idéntico | No ejecutado |
| V-15 | AC-05-11 | Borradores de ORG-A y ORG-B | Auditor, Bodega y Solicitante ORG-A; Coordinador ORG-A | Solicitar o aprobar; aprobar el de ORG-B | 403 para los otros roles; 404 para el borrador de ORG-B | Ningún cambio | No ejecutado |
| V-16 | AC-05-13 | Respuesta real capturada de un LLM con entrada, salida y origen | Coordinador ORG-A | Ejecutar con el adaptador de reproducción | Pasa por el mismo validador; origen «reproducción» visible | Borrador con origen reproducción | No ejecutado |

### Identidad, permisos y aislamiento (SPEC-01)

| ID | AC | Datos | Actor | Acción | Esperado | Efectos persistidos | Observado |
|---|---|---|---|---|---|---|---|
| V-17 | AC-01-01 | — | Sin sesión | GET listado de solicitudes | 401 sin datos | Ninguna sesión creada | No ejecutado |
| V-18 | AC-01-06 | Cuenta existente con contraseña errónea; login inexistente | — | Login | 401 con el mismo mensaje en ambos | Sin sesión | No ejecutado |
| V-19 | AC-01-08 | Sesión cerrada con logout | Cualquier rol | Reutilizar la credencial | 401 | Revocación persistida | No ejecutado |
| V-20 | AC-01-02 | B-001 de ORG-B y un ID inexistente | Coordinador ORG-A | GET de ambos | 404 con cuerpo idéntico | Ninguno | No ejecutado |
| V-21 | AC-01-09 | B-001 | Usuarios de ORG-A | Lecturas: detalle e historial de B-001. Escrituras: reservar y despachar B-001 | Cuatro 404 | Snapshot de ORG-B idéntico | No ejecutado |
| V-22 | AC-01-03 | A-001 ENVIADA | Auditor ORG-A | Reservar A-001 | 403 | Snapshot idéntico | No ejecutado |
| V-23 | AC-01-07 | — | Solicitante ORG-A | Enviar `organizationId: ORG-B` y `role: COORDINADOR` en cuerpo y encabezados al reservar A-001 | 403 (acción prohibida para su rol); el campo se ignora | Snapshot idéntico | No ejecutado |
| V-24 | AC-01-10 | Logs de V-17 a V-23 y de un error interno | — | Inspeccionar logs y respuestas | JSON con correlationId; sin contraseñas, hashes, tokens ni secretos | — | No ejecutado |
| V-25 | AC-01-12 | Repositorio en el SHA evaluado | — | Detección de secretos | Sin secretos; solo `.env.example` | — | No ejecutado |

## Hallazgos y preguntas para el equipo

Son observaciones de revisión, no decisiones del cliente.

| ID | Hallazgo | Evidencia | Propuesta |
|---|---|---|---|
| H-V01 | La «tolerancia instrumental declarada» del timeout no tiene valor | AC-05-03 y AC-05-09 no la cuantifican | Fijar en el plan de pruebas G2 un máximo, por ejemplo 5 s + 250 ms, medido con reloj controlado |
| H-V02 | No se define el orden de validación cuando una selección mezcla errores (por ejemplo, 21 IDs que incluyen B-001) | Salidas de SPEC-05: 422, 404 y 409 por separado | Validar en este orden: formato (422), pertenencia (404), estado (409); documentarlo en el contrato |
| H-V03 | No se dice si el coordinador puede aprobar un resumen alternativo (fallback) | RF12 habla del «resumen»; SPEC-05 no distingue el origen al aprobar | Permitirlo y conservar el origen fallback en el evento de auditoría |
| H-V04 | Falta qué pasa al aprobar dos veces el mismo borrador | Ningún AC lo cubre | Segunda aprobación: 409 sin segundo evento; añadir AC en 0.4 |
| H-V05 | AC-01-07 dice «403 o 404 según corresponda» y no fija el esperado | Texto de AC-01-07 | Separar los casos: recurso de su organización con acción prohibida → 403; recurso de otra organización → 404 (como V-23) |
| H-V06 | Si la sesión usa cookies, falta protección CSRF | CL-14 lo deja para ADR-003 | Decidirlo en ADR-003 y añadir un caso negativo de CSRF a SPEC-01 |
| H-V07 | AC-05-08 mezcla dos casos incompatibles: con AI-INJECTION la salida se rechaza entera (ID ajeno) y se usa el resumen alternativo, así que el marcado nunca se muestra y el escape no queda probado | Texto de AC-05-08: «se usa el resumen alternativo; el marcado se muestra escapado» | Separar en 0.4: un AC de inyección con ID ajeno (rechazo y fallback) y otro con salida válida que contiene marcado (se muestra escapado), como V-09a y V-09b |

## Checklist previsto de CI

No se crea todavía ningún workflow de aplicación ni se afirma ninguna ejecución de CI. Herramientas propuestas para el stack sugerido por el enunciado §6.

| # | Paso del pipeline | Qué comprueba | Herramienta propuesta | Requisito | Estado |
|---|---|---|---|---|---|
| CI-01 | Instalación reproducible | Dependencias desde lockfile, sin red adicional | `npm ci` / `pip install -r` con versiones fijadas | RNF04 | Previsto |
| CI-02 | Coincidencia de SPEC | JSON y matriz iguales a la fuente, **sin regenerar antes** | `python scripts/export_specs.py --check` y `build_coverage.py --check` | §8, anexo 18 | Previsto (cubre D-20) |
| CI-03 | Formato, lint y tipos | Estilo y errores estáticos | ESLint + `tsc --noEmit` (o equivalente) | §9 | Previsto |
| CI-04 | Pruebas unitarias | Dominio: cantidades, SKU, fechas, transiciones | Vitest o Jest | §11 Dominio | Previsto |
| CI-05 | Integración con base real | API contra PostgreSQL en contenedor de servicio; efectos persistidos | GitHub Actions `services: postgres` | RNF02, §11 | Previsto |
| CI-06 | Contrato | Respuestas conformes a OpenAPI 3.1.1; salida IA conforme al esquema | Validador OpenAPI + JSON Schema | §6, RF11 | Previsto |
| CI-07 | Pruebas de IA con fixtures | Los siete fixtures, sin credenciales ni proveedor de pago | Pruebas del adaptador | RNF06, AC-05-14 | Previsto |
| CI-08 | Seguridad | 401, 403, 404, aislamiento y logs sin secretos | Pruebas de API | RNF01 | Previsto |
| CI-09 | Build | La aplicación compila | `npm run build` (o equivalente) | §9 | Previsto |
| CI-10 | Dependencias | Vulnerabilidades conocidas, con alcance documentado | `npm audit` / Dependabot | §9 | Previsto |
| CI-11 | Secretos | Sin claves ni contraseñas en el repositorio | gitleaks | RF01, AC-01-12 | Previsto |
| CI-12 | E2E sobre candidato | Recorrido completo y cancelación | Playwright | §11 E2E | Previsto (solo candidato de entrega) |

Regla: un check fallido impide considerar listo el PR; un test omitido o un check desactivado debe quedar visible (§9).

## Comandos ejecutados

Ejecutados por Vega José en su PC (Windows, PowerShell, Python 3.12.10), 2026-10-14T15:03:10Z, sobre el SHA `cf9d5f5d905dee3a3774c623283c7045271dbe60`:

```text
> python --version
Python 3.12.10
> python scripts/export_specs.py --check
5 SPEC y 68 AC validados; JSON coherentes
> python scripts/build_coverage.py --check
Matriz y traza coherentes con las SPEC
> python -m unittest discover -s scripts -p "test_*.py" -v
test_ac_references_requirement_outside_spec ... ok
test_ac_sequence ... ok
test_check_does_not_create_output_directory ... ok
test_duplicate_block ... ok
test_duplicate_json_key ... ok
test_failed_validation_does_not_partially_export ... ok
test_invalid_kind ... ok
test_missing_spec ... ok
test_requirement_without_ac ... ok
test_roundtrip ... ok
test_stale_json ... ok
test_template_placeholder_rejected ... ok
test_untyped_input ... ok
test_wrong_id ... ok
Ran 14 tests in 0.510s
OK
> git rev-parse HEAD
cf9d5f5d905dee3a3774c623283c7045271dbe60
```

## Resultado y decisión

- **Resultado observado de producto:** no ejecutado.
- **Decisión por SPEC-01:** aprobada con observaciones. H-V05 y H-V06 quedan abiertos y deben resolverse en la versión 0.4 antes de implementar autenticación y permisos.
- **Decisión por SPEC-05:** aprobada con observaciones. H-V01 a H-V04 y H-V07 quedan abiertos y deben resolverse en la versión 0.4 antes de implementar el asistente.
- **Alcance de la aprobación:** SHA `cf9d5f5` únicamente. Si la versión 0.4 cambia SPEC-01 o SPEC-05, la aprobación debe renovarse sobre el nuevo SHA.
- **Enlace a comentario o revisión GitHub:** pendiente hasta publicarlo.
- **PR ajeno revisado / SHA / comentario propio:** pendiente hasta que Conde José publique su aporte.
