# Evidencia de validación documental G1

Fecha de ejecución: 2026-10-13 (America/Bogota). Entorno: Windows, PowerShell, Python 3.11.9. Referencia inicial: `1aeacbf681aa711c78c4a4488f5d7bb2acaf43cd`; rama de ejecución `docs/g1-specifications`. SHA de candidato: consultar el commit que introduce este informe; no se inventa un hash antes de crear el commit.

## Ejecución real

`python scripts/export_specs.py`

```text
SPEC JSON exportados
5 SPEC y 57 AC validados; JSON exportados
```

`python scripts/export_specs.py --check`

```text
5 SPEC y 57 AC validados; JSON coherentes
```

`python -m unittest discover -s scripts -p test_*.py -v`

```text
test_check_does_not_create_output_directory ... ok
test_duplicate_block ... ok
test_duplicate_json_key ... ok
test_failed_validation_does_not_partially_export ... ok
test_markdown_json_divergence ... ok
test_missing_spec ... ok
test_roundtrip ... ok
test_stale_json ... ok
test_wrong_id ... ok
Ran 9 tests in 0.797s
OK
```

Todos los casos usan copias temporales del conjunto SPEC. Se comprobaron rechazo de estructura inválida, coherencia Markdown/JSON y ausencia de escritura parcial o de creación de directorio en modo check.

## Límites de evidencia

Esto valida artefactos documentales, no RF de una aplicación ejecutable. No se ejecutaron pruebas HTTP, PostgreSQL, UI, E2E, carga, IA real, accesibilidad o CI remoto. No hay revisión humana aprobada. Los resultados esperados de los 57 AC permanecen separados de resultados observados en la matriz.

## Ejecución 2 — cotejo oficial, SPEC 0.3-draft

Fecha de ejecución: 2026-10-14T03:50Z. Entorno: Linux (espacio de trabajo del asistente de IA usado por Castro Edier, registro AI-001), Python 3.13.16, biblioteca estándar. Rama `docs/g1-edier-cotejo-oficial` creada desde `6fa084c`. El SHA es el del commit que introduce esta sección; no se escribe un hash antes de crear el commit. Los integrantes deben repetir los comandos en su equipo (Windows, Python 3.11) y registrar su propia salida.

`python scripts/export_specs.py --check`

```text
5 SPEC y 68 AC validados; JSON coherentes
```

`python scripts/build_coverage.py --check`

```text
Matriz y traza coherentes con las SPEC
```

`python -m unittest discover -s scripts -p "test_*.py" -v`

```text
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
Ran 14 tests in 0.068s
OK
```

`cd docs/examen-oficial && sha256sum -c SHA256SUMS.txt`: 29 archivos OK (2 originales y 27 anexos).

Comprobación adversarial adicional: en una copia temporal se añadió una línea a `docs/coverage-matrix.md` y `python scripts/build_coverage.py --check` respondió `Matriz desactualizada: docs/coverage-matrix.md` con código de salida 1.

Límites: igual que la ejecución 1. Los 68 AC son resultados esperados; ninguno se ha observado en una aplicación.
