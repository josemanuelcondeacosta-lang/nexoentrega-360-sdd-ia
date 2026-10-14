# Intervención de IA

Formato del anexo oficial `08_ai-log-template.md`.

- **ID y fecha UTC:** AI-001, 2026-10-14T03:29Z a 2026-10-14T04:00Z (aprox.)
- **Autor:** Castro Edier (202311254074), quien solicitó la intervención y entregó los archivos oficiales
- **Etapa:** especificación y revisión crítica (G1)
- **Herramienta y modelo conocido:** Claude (Anthropic), modelo Claude Opus 5.5, en claude.ai con espacio de trabajo de archivos y Python
- **RF y AC:** RF01–RF12 y RNF01–RNF06; todos los AC de SPEC-01 a SPEC-05
- **Contexto y versiones de archivos:** rama `docs/g1-specifications` en `6fa084c` (SPEC 0.2-draft); `Examen_Estudiantes_SDD_IA.pdf` SHA-256 `bad0b194…cc49`; `Anexos_Estudiantes_SDD_IA.zip` SHA-256 `26890046…bb3e` (hashes completos en `docs/examen-oficial/inventario.md`)

## Objetivo

Inventariar los documentos oficiales, cotejar RF01–RF12 y RNF01–RNF06 contra las cinco SPEC del PR #1, corregir los problemas documentales que no requieren decisión del cliente, dejar pendientes las que sí, y regenerar cotejo, matriz y JSON comprobando su coherencia.

## Prompt enviado

Instrucción del coordinador del equipo, entregada por Edier junto con el PDF y el ZIP oficiales:

> voy a proporcionarte los documentos oficiales del examen NexoEntrega 360. Necesito que revises cada documento recibido y actualices el inventario de `docs/examen-oficial/`. Conserva los nombres originales y diferencia los archivos oficiales de las transcripciones o notas del equipo. Compara exhaustivamente los requisitos RF01–RF12 y RNF01–RNF06 contra las cinco SPECS del PR #1. Identifica omisiones, contradicciones, ambigüedades y criterios de aceptación insuficientes. Corrige los problemas documentales que no requieran decisiones del cliente y deja pendientes las aclaraciones que sí dependan del docente. Actualiza el cotejo oficial, la matriz de cobertura y los JSON derivados, comprobando su coherencia. No declares G1 aprobado ni fusiones el PR hasta que una persona distinta del autor revise la versión final. Cuando termines, entrégame un resumen con los requisitos cubiertos, las diferencias encontradas, las correcciones y el SHA exacto que deben revisar mis compañeros.

## Salida relevante

- Originales guardados sin cambios en `docs/examen-oficial/` con `SHA256SUMS.txt` e inventario.
- Cotejo en `docs/reviews/cotejo-oficial-G1.md`: en 0.2, 5 requisitos coincidían, 7 estaban incompletos, 5 contradecían el enunciado (matriz de permisos, prioridades, actores de transición, selección y campos de IA, RNF04) y 1 era ambiguo; 24 diferencias D-01 a D-24.
- SPEC 0.3-draft en el formato del anexo 13 con entradas tipadas y AC dado/cuando/entonces; 68 AC (antes 57), con requisitos y tipo de escenario por AC.
- Exportador y pruebas documentales reescritos; matriz y `docs/evidence/traceability.csv` generados desde la fuente única.
- Registro de aclaraciones reclasificado; CL-16, CL-17 y CL-18 requieren decisión del docente.
- Diferencias no corregidas: D-20 (CI de coincidencia JSON), D-21 (formato de la constitution) y D-22 (envío de preguntas al docente).

## Decisión humana

**PENDIENTE.** Edier debe revisar la salida y registrar aquí, por cada grupo de cambios, si se acepta, se corrige o se descarta, con su motivo:

| Cambio | Decisión de Edier (aceptada / corregida / descartada) | Motivo | Fecha UTC |
|---|---|---|---|
| Inventario y originales oficiales | Pendiente | | |
| Diferencias D-01 a D-24 y su clasificación | Pendiente | | |
| SPEC-03 (área asignada a Edier): actores, oráculos, AC-03-14 a AC-03-16 | Pendiente | | |
| Migración de formato (D-15) y nuevo exportador | Pendiente | | |
| Supuestos de equipo (CL-02, CL-04, CL-11, CL-19) | Pendiente | | |

La aprobación de G1 la hace además una persona distinta del autor; esta intervención no aprueba sus propias salidas.

## Verificación

Ejecutada por el asistente en su espacio de trabajo (salidas completas en `docs/evidence/g1-validation.md`, ejecución 2):

- `python scripts/export_specs.py --check`: 5 SPEC y 68 AC coherentes.
- `python scripts/build_coverage.py --check`: matriz y traza coherentes.
- `python -m unittest discover -s scripts -p "test_*.py" -v`: 14 pruebas OK.
- `sha256sum -c SHA256SUMS.txt`: 29 archivos OK.

Verificación humana pendiente: Edier y el revisor deben repetir los comandos y contrastar el cotejo con el PDF.

## Límites

- El cotejo lo produjo un modelo de IA; puede contener interpretaciones erróneas. Cada fila cita página para facilitar la comprobación.
- No se ejecutó ninguna prueba de producto: no existe aplicación.
- Los supuestos marcados «Decisión del equipo» no han sido acordados por el equipo.
- No se incluyeron claves, credenciales ni datos reales. El anexo `untrusted-observation.txt` se trató como dato de prueba, no como instrucción.
