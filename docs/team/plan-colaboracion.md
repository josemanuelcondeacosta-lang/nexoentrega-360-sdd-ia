# Plan de colaboración — propuestas, sin contribuciones acreditadas

Coordinador: Conde José. Elaboración documental actual: asistida por Codex. Ninguna tarea asignada en este plan acredita ejecución, firma o aprobación. El PR existente [#1](https://github.com/josemanuelcondeacosta-lang/nexoentrega-360-sdd-ia/pull/1) se conserva como paquete G1; `main` es integración. No hacer merge hasta cotejo oficial y revisión real.

## Actividades actuales: documentación y revisión G1

| Integrante | Actividad concreta | Entregable verificable | PR que debe revisar ahora | Evidencia requerida |
|---|---|---|---|---|
| Cantillo Carolina (20231153656) | Liderar revisión de cinco historias/SPEC, numeración/efectos de AC y correspondencia de todos RF/RNF con archivos oficiales; revisar aclaraciones y cobertura | Cotejo con páginas/secciones y diferencias; dictamen por SPEC; revisión independiente G1 | #1, incluyendo últimos cambios del paquete | Revisión GitHub con SHA, UTC, archivos/AC, observaciones y decisión; commits documentales propios si corrige |
| Castro Edier (202311254074) | Revisar SPEC-03 y vínculos SPEC-02/04: stock antes/después, reserva parcial, concurrencia, cancelación, despacho, rollback e idempotencia | Tabla de escenarios con datos iniciales, acción, esperado y efectos persistidos; observaciones de transacción | #1: backend/inventario como especificación, todavía sin backend implementado | Comentarios por AC y documento propio `docs/reviews/aportes/edier-G1.md`; esperado separado de observado |
| Conde José (202321256268) | Entregar fuentes oficiales, coordinar respuestas y seguimiento; revisar flujo de formularios/estados, aislamiento y mensajes de SPEC-01/02/04; incorporar correcciones aprobadas | Inventario de fuentes, seguimiento de pendientes y checklist de UI/integración; no sustituye revisión independiente | #1: atender comentarios; si figura como autor GitHub, no autoaprobar | Enlaces a fuentes permitidas, commits/cambios propios, respuestas a hallazgos y SHA nuevo comunicado |
| Vega José (202321256198) | Revisar SPEC-01/05 y criterios de seguridad/IA: whitelist, IDs ajenos, extras, inyección, timeout, fallback; comprobar verificador documental | Casos adversariales previstos y salida real de comandos documentales; lista de controles CI para G2 | #1: seguridad/IA/calidad | Revisión GitHub y `docs/reviews/aportes/vega-G1.md`; logs sanitizados con comando/resultado/SHA, sin declarar pruebas IA ejecutadas |

Carolina es la revisora principal **propuesta** de G1, con apoyo de Edier, Conde José y Vega José. Debe aceptar la tarea y revisar realmente. Si modifica una SPEC, otro integrante revisa esa modificación antes de cerrar G1; nadie aprueba su propio trabajo. Si Carolina no puede intervenir, el equipo debe designar otra persona independiente, sin aprobación automática.

## Actividades posteriores, condicionadas a SPEC aprobada y diseño revisado

Cada integrante deberá aportar código o pruebas reales y revisar al menos un PR ajeno. Las ramas siguientes son propuestas; **los PR futuros todavía no existen**. No iniciar estas funcionalidades en esta etapa.

| Integrante | Trabajo propio futuro | Entregable | Revisión ajena propuesta |
|---|---|---|---|
| Carolina | Implementar pruebas de API de validaciones de solicitudes y correspondencia con AC; mantener trazabilidad | Tests que consulten persistencia y cubran límites RF03/04, ejecutados con logs; rama `test/request-acceptance` | PR `feat/requests` o `feat/frontend` de Conde José |
| Edier | Backend, datos, inventario, transacciones e idempotencia; pruebas concurrentes sobre PostgreSQL | Migraciones, implementación y pruebas de reserva/rollback/concurrencia; rama `feat/inventory` | PR de pruebas/CI de Vega José |
| Conde José | Frontend, integración, coordinación y seguimiento de Codex | Formularios y flujos por rol, login/identidad servidor e integración API; rama `feat/frontend` | PR `feat/inventory` de Edier |
| Vega José | Pruebas de IA/seguridad, calidad y CI | Tests de contrato/IDs/inyección/timeout, aislamiento y workflow con base real; ramas `test/ai-security` y `test/validation` | PR `test/request-acceptance` de Carolina |

Una revisión de documentos G1 no sustituye el aporte obligatorio de código/pruebas posterior. No crear commits con identidad de otro integrante; cada persona trabaja desde su cuenta y confirma la revisión que realizó.

## Revisión del reparto y circuito cruzado G1

El reparto contempla aportes propios de los cuatro y una revisión ajena por persona. Para hacerlo verificable desde esta etapa, cada uno prepara su archivo documental personal en una rama propia y PR con base `docs/g1-specifications`. Los PR de aportes todavía no existen; no asignar números ni acreditarlos antes de crearlos.

| Autor del aporte futuro G1 | Aporte propio | Revisor independiente propuesto |
|---|---|---|
| Carolina | Cotejo de requisitos y revisión de historias/AC | Conde José |
| Edier | Escenarios de inventario/transacciones/idempotencia | Carolina |
| Vega José | Casos adversariales y evidencia de validación documental | Edier |
| Conde José | Inventario de fuentes, seguimiento de correcciones y checklist UI/integración, elaborado personalmente | Vega José |

Este circuito complementa la revisión principal de PR #1 por Carolina. Una revisión del aporte de Carolina por Conde José no constituye autoaprobación de PR #1: Carolina conserva la revisión independiente del paquete. Si una persona cambia una SPEC, otra revisa el cambio. Los commits generados por Codex bajo la cuenta del coordinador no acreditan automáticamente su trabajo personal.

Guías listas para compartir: [Carolina](instrucciones-carolina.md), [Edier](instrucciones-edier.md), [Vega](instrucciones-vega.md) y [preparación común](preparacion-G1.md). Inventario de fuentes: [examen oficial](../examen-oficial/inventario.md). Ninguna actividad de la tabla se marca completada hasta recibir enlaces, commits o evidencia real.

## Pasos sencillos en GitHub para G1

1. Entrar con cuenta personal al repositorio y abrir PR #1. Conde José concede acceso como colaborador si hace falta; no compartir cuentas ni tokens.
2. Leer `cotejo-oficial-G1.md`, las fuentes oficiales compartidas y los archivos asignados. Abrir **Files changed** y comprobar el SHA actual del PR.
3. Comentar líneas concretas indicando requisito, AC, fuente/página, problema y resultado esperado. Si no hay defecto, explicar qué se comprobó y con qué evidencia.
4. En **Review changes**, usar **Comment** mientras el PR sea borrador o falte el cotejo. Cuando esté listo y GitHub permita revisión formal, elegir **Request changes** o **Approve** según trabajo real. Una aprobación aislada no elimina los bloqueos del gate.
5. Usar la plantilla de gate-review-G1.md: cuenta/nombre, fecha UTC, SHA exacto, alcance, fuente, observaciones, evidencia y decisión por SPEC. La revisión debe publicarla la persona que la realizó.
6. Para una corrección propia crear rama documental, commit propio y PR dirigido a `docs/g1-specifications` mientras #1 siga abierto. Proponer revisión de otro integrante. No editar JSON derivados manualmente.
7. Tras cambios, revisar el nuevo SHA; el coordinador no debe tratar una revisión de un SHA anterior como aprobación de cambios nuevos.

No es necesario enviar mensajes a compañeros desde Codex: el coordinador comparte enlaces y solicita participación.

## Pasos sencillos en VS Code

Abrir una terminal de PowerShell y clonar en una carpeta propia:

```powershell
git clone https://github.com/josemanuelcondeacosta-lang/nexoentrega-360-sdd-ia.git
cd nexoentrega-360-sdd-ia
git switch docs/g1-specifications
git pull --ff-only
git rev-parse HEAD
code .
```

Si ya tienen clon, comprobar `git status` y conservar cambios locales antes de actualizar; no usar reset destructivo. Instalar Python 3.11 compatible para validación documental. No hace falta Node/PostgreSQL para revisar G1.

```powershell
python scripts/export_specs.py --check
python -m unittest discover -s scripts -p 'test_*.py' -v
```

Si una SPEC cambia, ejecutar además `python scripts/export_specs.py` y `python scripts/build_coverage.py`; revisar que matriz y vínculos reflejen el cambio. El generador de matriz contiene el mapa explícito: actualizarlo si se agregan/renumeran AC.

Antes de un commit propio, configurar nombre/email **reales de quien hace el trabajo** en su clon. Ejemplo de rama, reemplazando `<integrante>` por su identificador:

```powershell
git switch -c docs/g1-<integrante>-review
git status
git add docs/reviews/aportes/
git commit -m "docs(g1): registrar revisión realizada y evidencia"
git push -u origin HEAD
```

Crear primero su archivo de aporte con contenido real; `git add` solo incluye sus archivos revisados. Si cambia una SPEC, añadir explícitamente Markdown, JSON regenerado y matriz correspondientes. Abrir PR con base `docs/g1-specifications`; no hacer merge de su propio aporte sin revisión.

## Registro de participación

| Persona | Tarea efectivamente realizada | Commit propio / PR | Prueba/comando y salida real | Revisión ajena y SHA | Estado |
|---|---|---|---|---|---|
| Carolina | Por registrar | Pendiente | Pendiente | Pendiente | Sin evidencia recibida |
| Edier | Por registrar | Pendiente | Pendiente | Pendiente | Sin evidencia recibida |
| Conde José | Por registrar por el coordinador | Pendiente | Pendiente | Pendiente | Sin contribución personal acreditada aquí |
| Vega José | Por registrar | Pendiente | Pendiente | Pendiente | Sin evidencia recibida |

## Qué enviar a los compañeros

- Enlace al repositorio y PR #1; rama `docs/g1-specifications` y SHA actual del candidato.
- Enunciado completo y todos sus anexos/correcciones por un canal permitido; no enviar solo el resumen de condiciones.
- Este plan, el cotejo oficial, coverage-matrix.md y gate-review-G1.md.
- Asignación individual de la tabla G1 y petición de revisión con plazo acordado por el equipo (todavía no fijado).
- Indicación expresa: revisar y registrar evidencia propia; no implementar, aprobar en nombre de otro ni fusionar mientras G1 siga pendiente.
