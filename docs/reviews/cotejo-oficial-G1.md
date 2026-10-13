# Cotejo oficial G1 — preparado, pendiente de archivos

Estado: **NO REALIZADO**. El coordinador informa que dispone del enunciado; sus archivos completos y anexos todavía no se han recibido en este repositorio/chat. La lista resumida del mensaje no sustituye el texto oficial. Los posibles vacíos siguientes provienen del borrador, no son hallazgos humanos ni contradicciones confirmadas con el examen.

## Archivos que debe proporcionar el coordinador

1. Enunciado completo descargado del aula: PDF o DOCX original, con todas las páginas, RF01–RF12, RNF01–RNF06, rúbrica, instrucciones de las etapas y entrega. Si está en HTML, adjuntar exportación PDF completa o el texto íntegro con su identificación.
2. Todos los archivos referenciados como anexos: reglas de negocio, roles/permisos, estados/transiciones, datos de ejemplo/seed, campos y contrato IA, requisitos de pruebas/rendimiento y plantilla de entrega, **si el examen los incluye**. Mantener sus nombres originales.
3. Correcciones, versiones posteriores y aclaraciones oficiales del docente, si existen; incluir fecha y procedencia. Si no existen anexos o aclaraciones adicionales, indicarlo expresamente.

No se conocen todavía los nombres exactos de los anexos. Revisar cada referencia del enunciado para construir su inventario antes de afirmar que el conjunto está completo. No enviar credenciales del aula ni datos personales de beneficiarios. El coordinador puede adjuntar aquí los originales; su distribución o publicación en GitHub se decide según las condiciones del aula.

## Inventario de fuentes por completar

Registro central de disponibilidad y recepción: [docs/examen-oficial/inventario.md](../examen-oficial/inventario.md). Guardar originales en `docs/examen-oficial/` cuando se reciban; esta tabla sigue como referencia preparada y no acredita recepción.

| ID | Nombre original / versión | Procedencia y fecha oficial | SHA-256 del archivo recibido | Páginas/secciones | Disponible para revisores |
|---|---|---|---|---|---|
| OF-01 | Enunciado completo: pendiente | Pendiente | Pendiente | Pendiente | No recibido |
| OF-02… | Anexos: inventario pendiente | Pendiente | Pendiente | Pendiente | No recibidos |
| OF-ACL-01… | Aclaraciones, si existen | Pendiente | Pendiente | Pendiente | Por confirmar |

El hash identifica el archivo, no acredita por sí solo que sea oficial. Registrar la procedencia del aula y la versión. El cotejo debe citar archivo/página/sección; conservar solo citas breves necesarias y respetar condiciones de distribución.

## Comparación preparada por requisito

En cada fila, al recibir la fuente, añadir texto oficial o síntesis fiel, referencia exacta, diferencias, AC afectados, acción propuesta y revisor. Estados permitidos: pendiente de fuente, coincide, incompleto, ambiguo, contradicción. «Coincide» solo tras lectura y comprobación real.

| RF | SPEC / AC actuales | Qué cotejar exactamente | Riesgo o ambigüedad del borrador | Resultado oficial |
|---|---|---|---|---|
| RF01 | SPEC-01 / 01,04–06,08,10 | Login/logout, hash, identidad del servidor, cuatro roles, ocho cuentas y dos organizaciones | Duración/CSRF/política de sesión pendientes CL-14 | Pendiente de fuente |
| RF02 | SPEC-01 / 02,03,07,09 | Permisos por operación, dueño/organización, 401/403/404 y protección de respuestas/logs | Matriz y precedencia HTTP propuestas CL-09 | Pendiente de fuente |
| RF03 | SPEC-02 / 01,02,04–07 | Código, organización, dueño, zona, prioridad, fecha, observaciones, 1–10 SKU activos distintos y cantidades enteras 1–500 | Formato código, zona/prioridad/observaciones/fecha CL-01/02/03/07/10 | Pendiente de fuente |
| RF04 | SPEC-02 / 03,08–10; SPEC-03 / 05–07,13 | Edición exclusiva de borrador, fecha al enviar y grafo completo de estados/transiciones | Actor de cancelación/entrega y transiciones exactas CL-09 | Pendiente de fuente |
| RF05 | SPEC-03 / 01–03,08,09 | Reserva total o ninguna, disponibilidad, no negativos y concurrencia | Atomicidad incluye auditoría; precisar comportamiento de solicitudes competidoras | Pendiente de fuente |
| RF06 | SPEC-03 / 05,06,13 | Cancelar ENVIADA/RESERVADA, liberar reserva, despacho y entrega | Permisos y campos de entrega por confirmar; sin AC específico de datos adicionales de entrega | Pendiente de fuente |
| RF07 | SPEC-03 / 04,10–12 | Operaciones idempotentes, 24h, replay, conflicto, ámbito y conservación | Alcance solo reserva, TTL exacto, errores almacenados y retención CL-04/11 | Pendiente de fuente |
| RF08 | SPEC-04 / 01,02,05,06,10 | Paginación, filtros, tablero e inventario; alcance por rol/org | Defaults, métricas y permisos de tablero/inventario propuestos | Pendiente de fuente |
| RF09 | SPEC-04 / 03,04,07,08 | Eventos persistentes, campos, UTC, correlationId e inmutabilidad API | Eventos de login/rechazo/IA y retención CL-15; no confundir logs con auditoría | Pendiente de fuente |
| RF10 | SPEC-05 / 01,05,10 | Coordinador, hasta 20 ENVIADA, organización y whitelist exacta | Campos y selección de 20 propuestos CL-12 | Pendiente de fuente |
| RF11 | SPEC-05 / 02,04,06–08,11,12 | Schema estricto, límites 600/5/240/1–3, IDs válidos, evidencia verificable, aprobación humana | Nombres/tipos oficiales, semántica evidenceFields y replay de aprobación pendientes | Pendiente de fuente |
| RF12 | SPEC-05 / 03,09,13 | Timeout 5s, errores, fallback determinista, adaptador/reproducción y origen | Contenido de fallback y persistencia CL-05/08; tolerancia instrumental no definida | Pendiente de fuente |

Los sufijos AC se interpretan dentro de la SPEC indicada. Consultar coverage-matrix.md para los identificadores completos y resultados esperados.

## Cotejo RNF obligatorio

No inferir significado a partir del número. La asignación inicial es una hipótesis; incluso RNF03 y RNF04 comparten un AC de rendimiento, lo cual debe corregirse si el texto oficial lo exige.

| RNF | Asignación actual | Comprobación necesaria | Estado |
|---|---|---|---|
| RNF01 | SPEC-01 y SPEC-05 | Obtener texto íntegro, umbrales y evidencias; revisar cada AC vinculado | Pendiente CL-13 |
| RNF02 | SPEC-03 | Confirmar si cubre integridad/concurrencia u otro atributo; verificar efectos persistidos | Pendiente CL-13 |
| RNF03 | SPEC-04 / AC-04-09 | Confirmar definición y métrica, no asumir rendimiento | Pendiente CL-13 |
| RNF04 | SPEC-04 / AC-04-09 | Confirmar definición y evitar cobertura duplicada nominal | Pendiente CL-13 |
| RNF05 | SPEC-02 / AC-02-11 | Confirmar accesibilidad, resoluciones y método de evaluación exigido | Pendiente CL-13 |
| RNF06 | SPEC-01/04/05 | Confirmar definición; revisar vínculos a logs, auditoría y origen IA | Pendiente CL-13 |

## Requisitos transversales y entrega

| Obligación resumida por el coordinador | Artefacto preparado | Cotejo pendiente / estado real |
|---|---|---|
| Cinco historias/cinco SPEC | HU-01–05 y SPEC-01–05 | Confirmar formato oficial; borrador |
| Revisión antes de funcionalidad | gate-review-G1.md y plan-colaboracion.md | Revisión humana no recibida |
| Base real, CI y evidencias | Matriz de pruebas previstas | Implementación/ejecución pendientes |
| Cuatro ADR, diagramas y OpenAPI | Previstos para G2 | Confirmar tipos/formato/versión; no creados en esta etapa |
| PDF, release-examen y SHA | Constitución y misión | Entrega final pendiente; no generar PDF/tag ahora |

## Registro de diferencias (sin hallazgos inventados)

| ID | Fuente/página/sección | Requisito | Texto/condición oficial | SPEC/AC y condición actual | Clasificación | Corrección propuesta | Evidencia/revisor/decisión |
|---|---|---|---|---|---|---|---|
| Por completar tras recepción | Pendiente | Pendiente | Pendiente | Pendiente | Pendiente de fuente | Pendiente | Pendiente |

Proceso: inventariar todas las fuentes → extraer cada condición RF/RNF/transversal → comparar regla y AC → registrar ausencia/contradicción → proponer corrección → regenerar JSON/matriz cuando cambie SPEC → someter nuevo SHA a revisión humana. La aprobación anterior no se extiende automáticamente a requisitos modificados.
