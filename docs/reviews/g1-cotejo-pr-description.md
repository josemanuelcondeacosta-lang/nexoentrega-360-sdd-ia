# Cotejo oficial G1 y SPEC 0.3-draft

Descripción preparada para el PR de la rama `docs/g1-edier-cotejo-oficial` hacia `docs/g1-specifications` (base del PR #1). Secciones de la plantilla de PR del starter oficial.

## RF, RNF, AC y tarea

RF01–RF12 y RNF01–RNF06. SPEC-01 a SPEC-05 pasan a 0.3-draft con 68 AC (AC-01-01 a 13, AC-02-01 a 11, AC-03-01 a 16, AC-04-01 a 14, AC-05-01 a 14). Tarea: inventario de fuentes oficiales y cotejo G1.

## Comportamiento antes y después

- Antes: sin documentos oficiales; SPEC 0.2 con matriz de permisos propuesta, prioridades ALTA/MEDIA/BAJA, selección de IA por el servidor, RNF04 como rendimiento, entradas sin tipo y AC de texto único.
- Después: enunciado y 27 anexos guardados sin cambios con SHA-256; cotejo con 24 diferencias (D-01 a D-24); SPEC corregidas al texto oficial y al formato del anexo 13; registro de aclaraciones reclasificado; matriz y traza CSV generadas desde la fuente única.

## Spec y contrato aprobados

Ninguno. G1 sigue **pendiente**: este PR requiere revisión de una persona distinta del autor. No hay contrato OpenAPI todavía (G2).

## Pruebas, comandos, datos y SHA

Documentales, no de producto (`docs/evidence/g1-validation.md`, ejecución 2):

```sh
python scripts/export_specs.py --check
python scripts/build_coverage.py --check
python -m unittest discover -s scripts -p "test_*.py" -v
cd docs/examen-oficial && sha256sum -c SHA256SUMS.txt
```

Resultado: 5 SPEC y 68 AC coherentes; matriz coherente; 14 pruebas OK; 29 archivos con hash correcto. SHA revisado: el del último commit de la rama al momento de revisar.

## Uso de IA y decisión humana

Elaborado con Claude (Anthropic) a solicitud de Castro Edier; registro `docs/evidence/ai-log/AI-001-cotejo-oficial-g1.md`. La decisión humana de Edier sobre cada grupo de cambios está pendiente en ese registro.

## Riesgos, migración y límites

- Cambia el formato del bloque de las SPEC (marcadores del starter y campos del anexo 13). Los IDs de AC existentes se conservan.
- Abiertos: CI de coincidencia JSON (D-20), formato de la constitution (D-21), envío de CL-16 a CL-18 al docente (D-22).
- Supuestos del equipo por acordar: CL-01, CL-02, CL-03, CL-04, CL-10, CL-11, CL-19.

## Revisión de otra persona

Pendiente. Revisor propuesto según el plan de colaboración: Carolina (revisión de las cinco SPEC y AC). Registrar SHA, fecha UTC, observaciones y decisión por SPEC.
