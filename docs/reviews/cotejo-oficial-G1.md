# Cotejo oficial G1 — RF01–RF12 y RNF01–RNF06 contra las cinco SPEC

**Estado: COTEJO REALIZADO Y CORRECCIONES DOCUMENTALES APLICADAS (SPEC 0.3-draft). PENDIENTE DE REVISIÓN HUMANA INDEPENDIENTE.** G1 no está aprobado.

- **Elaboración:** asistida por IA (Claude, Anthropic) a solicitud de Castro Edier, 2026-10-14 UTC. Registro: [AI-001](../evidence/ai-log/AI-001-cotejo-oficial-g1.md). Este documento no es una revisión humana: Edier debe verificarlo y una persona distinta debe revisar la versión final.
- **Fuentes oficiales:** OF-01 `Examen_Estudiantes_SDD_IA.pdf` (15 páginas) y anexos OF-A00 a OF-A26; hashes en [inventario.md](../examen-oficial/inventario.md).
- **Base comparada:** rama `docs/g1-specifications`, commit `6fa084c` (SPEC 0.2-draft, 57 AC).
- **Resultado:** rama `docs/g1-edier-cotejo-oficial` (SPEC 0.3-draft, 68 AC). El SHA a revisar es el del commit que introduce este documento en esa rama.

## Resumen

| Resultado en 0.2-draft | Requisitos |
|---|---|
| Coincide | RF05, RF06, RF11, RNF02, RNF03 |
| Incompleto | RF01, RF07, RF09, RF12, RNF01, RNF05, RNF06 |
| Contradicción con el enunciado | RF02 (matriz de permisos), RF03 (prioridades), RF04 (actores), RF10 (selección y campos), RNF04 (significado) |
| Ambiguo | RF08 (permisos de inventario y tablero) |

Además, el formato de las SPEC no cumplía la sección 8 y la plantilla del starter (D-15). Tras las correcciones, los 18 requisitos tienen AC vinculados en el bloque canónico, verificado por `scripts/export_specs.py`. Quedan tres preguntas para el docente (CL-16, CL-17, CL-18) y varias decisiones del equipo registradas en el [registro de aclaraciones](../clarifications/clarification-log.md).

## Cotejo por requisito

| Req. | Fuente | Condición oficial (síntesis) | Situación en 0.2 | Resultado | AC en 0.3 | Pendiente |
|---|---|---|---|---|---|---|
| RF01 | OF-01 §2 p. 3 | Login y logout con cuentas sembradas; sesión o token justificado; sin selector de rol; hash; sin claves privadas en el repositorio; una cuenta por rol y organización, credenciales ficticias regenerables | Login, logout, hash y 8 cuentas cubiertos; faltaban selector de rol, secretos y regeneración | Incompleto → corregido (D-09) | AC-01-01, 04, 05, 06, 08, 11, 12 | Justificación sesión o token en ADR-003 (G2) |
| RF02 | OF-01 §2 p. 3 | Aislamiento total por organización, incluidos listados, auditoría y agregados; organización y rol desde la identidad; 401, 403 y 404 con semántica definida, también en errores y logs | Aislamiento cubierto; matriz propuesta contradecía el enunciado en cancelación y entrega | Contradicción → corregido (D-01, D-02, D-14) | AC-01-02, 03, 07, 09; AC-03-12, 15; AC-04-01, 02, 10, 11; AC-05-05, 11 | CL-16, CL-17, CL-18 (docente) |
| RF03 | OF-01 §3 p. 4 | Código generado, solicitante, organización, zona, prioridad NORMAL o ALTA, fecha requerida, observaciones opcionales, 1 a 10 líneas con SKU activo distinto y cantidad entera 1–500; fecha no anterior al momento de envío | Prioridades ALTA/MEDIA/BAJA; fecha sin hora frente a «momento» y al anexo de datos | Contradicción → corregido (D-03, D-13) | AC-02-01, 02, 04, 05, 06, 07 | CL-01, CL-02, CL-03, CL-10 (equipo) |
| RF04 | OF-01 §3 p. 4 | Flujo base; cancelación desde ENVIADA o RESERVADA por el solicitante dueño; solo el borrador es editable; transición no indicada → 409 sin cambios | Actores de cancelación y entrega errados en la matriz; SPEC-03 sin actor por transición | Contradicción → corregido (D-01, D-02) | AC-02-03, 08, 09, 10; AC-03-05, 06, 07, 13, 14, 15 | — |
| RF05 | OF-01 §3 p. 4 | Reserva total o nula, solo desde ENVIADA; disponible = física − reservada; todas no negativas; 409 sin cambios si falta disponibilidad | Cubierto; AC sin cantidades de los oráculos oficiales | Coincide; AC precisados (D-16) | AC-03-01, 02, 03, 08, 09 | — |
| RF06 | OF-01 §3 p. 4 | Cancelar RESERVADA libera exactamente su reserva; despachar baja física y reservada; ENTREGADA y cancelar ENVIADA no alteran inventario | Cubierto; AC sin cantidades | Coincide; AC precisados (D-16) | AC-03-05, 06, 13 | — |
| RF07 | OF-01 §3 p. 4 | Reserva transaccional con Idempotency-Key; misma clave y contenido repite resultado; contenido distinto → 409; retención ≥ 24 h; segunda reserva con otra clave → 409; clave procesada tras autenticar y autorizar; contrato fija errores almacenados y reintentos | Faltaban la segunda reserva con otra clave y el procesamiento tras autorizar | Incompleto → corregido (D-10) | AC-03-03, 04, 08, 10, 11, 12, 14, 15 | CL-04, CL-11 en contrato G2 (equipo) |
| RF08 | OF-01 §4 p. 5; §1 p. 2 | Listados paginados ≤ 100, filtros estado, prioridad y zona; tablero por estado; el tablero permite ver cantidades disponibles y reservadas; solicitante solo las suyas | Cubierto; permisos de inventario y tablero dependían de la matriz propuesta | Ambiguo → aclarado salvo docente | AC-04-01, 02, 05, 06, 10 | CL-16, CL-18 (docente) |
| RF09 | OF-01 §4 p. 5 | Evento por transición exitosa y por aprobación de resumen con identidad, organización, recurso, acción, fecha UTC y correlationId; sin edición ni borrado por API; rechazo solo log técnico; persistencia consistente | Faltaba «recurso»; la aprobación de resumen no exigía los campos RF09; sin AC de aislamiento de auditoría | Incompleto → corregido (D-11, D-12) | AC-02-08, AC-03-09, AC-04-03, 04, 07, 08, 11, AC-05-12 | CL-15 retención (equipo); CL-17 (docente) |
| RF10 | OF-01 §4 p. 5 | El coordinador elige por identificador hasta 20 ENVIADA de su organización; el adaptador recibe solo requestId, zona, prioridad, fecha requerida, estado y observaciones | El servidor elegía 20 por fecha; campos incluían líneas y omitían estado | Contradicción → corregido (D-04, D-05, D-24) | AC-05-01, 05 | CL-19 (equipo) |
| RF11 | OF-01 §4 p. 5; anexo `ai-output.schema.json` | summary ≤ 600; ≤ 5 recomendaciones; requestId del conjunto; reason ≤ 240; 1–3 evidenceFields permitidas; sin duplicados, ajenos ni propiedades extra; texto no confiable | Límites correctos; regla adicional «evidencia respaldando la razón» no verificable; faltaban summary vacío y evidenceFields repetidas | Coincide; ajustado al esquema (D-17) | AC-05-02, 06, 07, 08 | — |
| RF12 | OF-01 §4 p. 5 | Borrador identificable como IA; solo el coordinador aprueba y deja constancia; aprobar no reserva, despacha ni cambia prioridad; salida inválida, timeout 5 s o fallo → mensaje controlado y resumen determinista | Cubierto salvo «mensaje controlado» y la identificación visible | Incompleto → corregido (D-23) | AC-05-03, 04, 09, 10, 11, 12, 13 | CL-05, CL-08 (equipo) |
| RNF01 | OF-01 §5 p. 6 | Autorización en API, aislamiento y validación de entrada y salida; al menos dos lecturas y dos escrituras cruzadas denegadas | Cubierto en SPEC-01 y SPEC-05; validación de entrada de SPEC-02 sin vínculo | Incompleto → corregido (D-18) | AC-01-01, 02, 03, 06, 07, 09, 10; AC-02-02, 06, 07; AC-05-01, 02, 05, 08 | — |
| RNF02 | OF-01 §5 p. 6 | Sin existencias ni reservas negativas, sin efectos duplicados, transacciones verificadas contra base real | Cubierto en SPEC-03 | Coincide | AC-03-01, 02, 03, 04, 05, 07, 08, 09, 13 | — |
| RNF03 | OF-01 §5 p. 6 | Listados y tablero p95 ≤ 700 ms, errores < 1 %, 20 VU, 60 s tras 5 s, 2.000 solicitudes, sin IA, reportar hardware y herramienta | Cubierto por AC-04-09, compartido erróneamente con RNF04 | Coincide | AC-04-09 | — |
| RNF04 | OF-01 §5 p. 6 | Arranque desde clon limpio en ≤ 15 min con versiones fijadas, migraciones, seed y un comando; descarga inicial aparte | Se interpretó como rendimiento | Contradicción → corregido (D-06) | AC-04-12 | — |
| RNF05 | OF-01 §5 p. 6 | Interfaz a 390 y 1366 px sin desbordamiento horizontal; flujo principal con teclado; etiquetas y errores asociados; verificación manual sin afirmar WCAG total | Solo el formulario de solicitudes; sin «desbordamiento horizontal» | Incompleto → corregido (D-08) | AC-01-13, AC-02-11, AC-03-16, AC-04-14 | — |
| RNF06 | OF-01 §5 p. 6 | Logs estructurados con correlationId sin secretos; health y readiness diferenciados; fallo de IA controlado; CI y demo sin servicio de pago | Logs y fallback cubiertos; faltaban health/readiness y CI sin pago | Incompleto → corregido (D-07) | AC-01-10, AC-04-07, 13, AC-05-03, 09, 14 | — |

## Registro de diferencias

| ID | Fuente | Req. | Condición oficial | Situación en 0.2 | Clasificación | Corrección aplicada en 0.3 | Estado |
|---|---|---|---|---|---|---|---|
| D-01 | §2 p. 3; RF04 | RF02, RF04 | Cancela el solicitante dueño | Matriz: cancela el Coordinador; Solicitante «pendiente docente» | Contradicción | Matriz oficial en SPEC-01 y `requirements-source.md`; actores en SPEC-03; AC-03-15 exige 403 al Coordinador que cancela | Corregido; revisión pendiente |
| D-02 | §2 p. 3 | RF02, RF04 | Registra ENTREGADA el Coordinador | Matriz: despacha y entrega Bodega | Contradicción | Igual que D-01; AC-03-05, 13 y 15 | Corregido; revisión pendiente |
| D-03 | RF03 p. 4 | RF03 | Prioridad NORMAL o ALTA | Propuesta ALTA, MEDIA, BAJA | Contradicción | Enum oficial en SPEC-02 y SPEC-04; MEDIA en casos negativos (AC-02-06) | Corregido; revisión pendiente |
| D-04 | RF10 p. 5 | RF10 | El coordinador selecciona por identificador | El servidor seleccionaba 20 por fecha | Contradicción | Entrada requestIds de 1 a 20 en SPEC-05; AC-05-01 y AC-05-05 | Corregido; revisión pendiente |
| D-05 | RF10 p. 5 | RF10 | Campos: requestId, zona, prioridad, fecha requerida, estado y observaciones | Incluía líneas y omitía estado; nombres en inglés | Contradicción | Proyección oficial con los nombres del esquema | Corregido; revisión pendiente |
| D-06 | §5 p. 6 | RNF04 | Arranque reproducible en ≤ 15 min | Asignado a rendimiento (AC-04-09) | Contradicción | AC-04-09 solo RNF03; nuevo AC-04-12 | Corregido; revisión pendiente |
| D-07 | §5 p. 6 | RNF06 | Health y readiness diferenciados; CI sin servicio de pago | Sin AC | Omisión | AC-04-13 y AC-05-14 | Corregido; revisión pendiente |
| D-08 | §5 p. 6 | RNF05 | Flujo principal completo, 390 y 1366 px sin desbordamiento | Solo formulario de solicitudes | AC insuficiente | AC-01-13, AC-03-16, AC-04-14; AC-02-11 ajustado | Corregido; revisión pendiente |
| D-09 | RF01 p. 3 | RF01 | Sin selector de rol; sin claves privadas; credenciales regenerables | Sin AC | Omisión | AC-01-11, AC-01-12; AC-01-05 ampliado | Corregido; revisión pendiente |
| D-10 | §3 p. 4 | RF07 | Segunda reserva con otra clave → 409; clave procesada tras autenticar y autorizar | Sin AC explícito | Omisión | AC-03-14 y AC-03-15 | Corregido; revisión pendiente |
| D-11 | RF09 p. 5 | RF09 | Evento con identidad, organización, recurso, acción, fecha UTC y correlationId, también al aprobar resúmenes | AC-04-03 sin recurso; AC-05-12 sin campos RF09 | AC insuficiente | AC-04-03, AC-05-12 y AC-02-08 con todos los campos | Corregido; revisión pendiente |
| D-12 | RF02; §11 p. 12 | RF09, RF02 | Aislamiento también en auditoría | Sin AC específico | Omisión | AC-04-11 | Corregido; revisión pendiente |
| D-13 | RF03 p. 4; anexo `fixture-scenarios.json` | RF03 | Fecha no anterior al «momento de envío»; datos con fecha y hora UTC | Fecha sin hora comparada con fecha local | Ambigüedad | Supuesto CL-02 actualizado: instante RFC 3339 UTC; AC-02-04 con reloj de prueba | Corregido como supuesto del equipo; requiere acuerdo |
| D-14 | §2 p. 3 | RF02 | Semántica de 401, 403 y 404 definida | CL-09 abierta | Ambigüedad resuelta por fuente | CL-09 resuelta; nuevas CL-16, CL-17, CL-18 para lo no listado | Corregido; envío al docente pendiente |
| D-15 | §8 p. 9; anexos 03, 13 y starter | Todos | Entradas con tipos y validaciones; AC con datos, precondiciones y resultados; actores, alcance, exclusiones, supuestos, preguntas y dependencias; plantilla del starter | Entradas en prosa sin tipo; AC con un solo texto; bloque `spec-json` propio; AC duplicados a mano en Markdown | Formato insuficiente | Bloque canónico con marcadores del starter y campos del anexo 13 más los de §8; AC dado/cuando/entonces con requisitos y tipo de escenario; exportador y 14 pruebas actualizados; IDs AC conservados | Corregido; revisión pendiente |
| D-16 | Anexo `acceptance-oracles.md` | RF05, RF06 | Cantidades esperadas definidas | AC sin cantidades | AC insuficiente | AC-03-01, 05, 08 y 13 con los oráculos oficiales | Corregido; revisión pendiente |
| D-17 | RF11 p. 5; `ai-output.schema.json` | RF11 | Validación estructural y de pertenencia | Regla «evidenceFields respaldando la razón» no exigida ni verificable; faltaban summary vacío y claves repetidas | Ambigüedad | Regla retirada; casos del esquema añadidos en AC-05-06 | Corregido; revisión pendiente |
| D-18 | RNF01 p. 6 | RNF01 | Validación de entrada | Validación de SPEC-02 sin vínculo RNF01 | Trazabilidad incompleta | RNF01 vinculado a AC-02-02, 06, 07 | Corregido; revisión pendiente |
| D-19 | §3 p. 4 | RF04 | Actor de cada transición | SPEC-03 sin actor por operación | Omisión | Campo actors y reglas por transición en SPEC-03 | Corregido; revisión pendiente |
| D-20 | §8 p. 9; anexo 18 | Transversal | CI comprueba la coincidencia JSON sin regenerar antes | No existe workflow de CI | Omisión | No corregido en este cambio (fuera del alcance documental); propuesto para el responsable de CI | **Abierto** |
| D-21 | §8 p. 9; anexo 01 | Transversal | Constitution: cada regla con motivo, evidencia, responsable y criterio de excepción | Reglas sin motivo, responsable ni criterio de excepción | Formato insuficiente | No corregido; propuesto para la revisión documental | **Abierto** |
| D-22 | §5 p. 6 | Transversal | Día 1: preguntas enviadas al docente cuando requieren decisión del cliente | Ninguna enviada | Proceso pendiente | Registro reclasificado; CL-16 a CL-18 listas para enviar | **Abierto** (coordinador) |
| D-23 | RF12 p. 5 | RF12 | Mensaje controlado y borrador identificable | AC sin mensaje controlado ni identificación | AC insuficiente | AC-05-03, AC-05-09 y AC-05-10 | Corregido; revisión pendiente |
| D-24 | RF10 p. 5 | RF10 | Selección por identificador | AC-05-05 y AC-05-10 suponían selección del servidor y conjunto vacío | Contradicción derivada de D-04 | AC reescritos; códigos de error en CL-19 | Corregido; revisión pendiente |

## Requisitos transversales y entrega

| Obligación oficial | Artefacto | Estado |
|---|---|---|
| Cinco historias y cinco SPEC que cubren todos los RF y RNF (§8) | HU-01 a HU-05, SPEC-01 a SPEC-05 0.3-draft | Cobertura completa verificada por el exportador; revisión pendiente |
| Fuente única y JSON exportado comprobado en CI (§8) | `scripts/export_specs.py --check` | Comprobación local; CI pendiente (D-20) |
| Aprobación humana con fecha UTC, commit, autor y revisor distinto (§7) | `gate-review-G1.md` | Pendiente |
| Seis preguntas como mínimo y envío al docente (§5) | `clarification-log.md` | 19 registradas; 3 por enviar (D-22) |
| Constitution comprobable (§8) | `docs/constitution.md` | Formato por completar (D-21) |
| Registro de intervenciones de IA (§10) | `docs/evidence/ai-log/AI-001-cotejo-oficial-g1.md` | Primera intervención registrada; decisión humana pendiente |
| Cuatro ADR, diagramas, OpenAPI 3.1.1, plan y tareas (§6, G2) | — | Pendiente G2 |
| Traza RF/RNF en formato del anexo (§8) | `docs/evidence/traceability.csv` (generado) | Columnas de código, prueba, PR y evidencia vacías hasta implementar |

## Cómo revisar este cotejo

1. Abrir OF-01 y comprobar, fila por fila, la síntesis oficial y la clasificación. Anotar cualquier desacuerdo como comentario en el PR con página y AC.
2. Revisar las diferencias D-01 a D-24 y los AC nuevos: AC-01-11 a 13, AC-03-14 a 16, AC-04-11 a 14 y AC-05-14.
3. Confirmar o corregir los supuestos marcados como «Decisión del equipo» en el registro de aclaraciones.
4. Ejecutar:

```sh
python scripts/export_specs.py --check
python scripts/build_coverage.py --check
python -m unittest discover -s scripts -p "test_*.py" -v
cd docs/examen-oficial && sha256sum -c SHA256SUMS.txt
```

5. Registrar la revisión en GitHub con el SHA exacto revisado y la decisión por SPEC. Una aprobación sobre un SHA anterior no cubre cambios posteriores.
