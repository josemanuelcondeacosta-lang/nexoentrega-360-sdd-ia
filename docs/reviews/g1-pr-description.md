# Ampliar especificaciones G1 y preparar revisión independiente

Las cinco SPEC iniciales tenían entradas/procesos/salidas sin desarrollar, 21 AC y ninguna asignación de RNF04. Este cambio conserva historias y criterios iniciales, amplía a 57 AC y agrega supuestos, límites, efectos persistidos, trazabilidad y fronteras IA. El enunciado oficial/anexos aún no fue recibido; la cobertura RF/RNF es nominal y pendiente de cotejo.

Requisitos: RF01–RF12 y RNF01–RNF06 (asignación RNF provisional). Criterios: AC-01-01 a AC-01-10; AC-02-01 a AC-02-11; AC-03-01 a AC-03-13; AC-04-01 a AC-04-10; AC-05-01 a AC-05-13.

Tareas: auditoría inicial, ampliación SPEC, aclaraciones, controles de constitución, riesgos, límites IA, matriz por AC y verificador de JSON. No hay implementación de funcionalidades.

## Validación ejecutada

- `python scripts/export_specs.py`: cinco SPEC y 57 AC exportados.
- `python scripts/export_specs.py --check`: derivados coherentes.
- `python -m unittest discover -s scripts -p test_*.py -v`: nueve pruebas documentales aprobadas.
- Evidencia completa: `docs/evidence/g1-validation.md`.

Uso de IA: elaboración y revisión documental asistida por Codex; no se registra aprobación humana ni trabajo de integrantes. Diseño/código/pruebas de producto siguen pendientes.

## Revisión requerida

- Revisor humano distinto del autor: PENDIENTE.
- SHA revisado y fecha UTC: PENDIENTE.
- Hallazgos/decisiones: completar `docs/reviews/gate-review-G1.md`.
- Fuente oficial, matriz de permisos, RNF, whitelist IA y política idempotente: PENDIENTES.

No fusionar mientras la revisión humana esté pendiente; no declarar G1 aprobado por checks documentales.

## Preparación del equipo y cotejo oficial

Se conserva PR #1 y se agrega una comparación preparada por RF/RNF, inventario de archivos oficiales faltantes, lista verificable de revisión, plantilla personal con UTC/SHA/observaciones/decisión y plan de colaboración con instrucciones GitHub/VS Code. Carolina es la revisora G1 principal propuesta, apoyada por Edier, Conde José y Vega José. No acredita contribuciones ni aprobaciones realizadas.

El coordinador informa que dispone del enunciado; sus archivos y anexos aún no fueron recibidos por Codex. No se inicia implementación. Las pruebas documentales listadas arriba pertenecen a la ejecución anterior conservada; este cambio es documental y no declara nuevas pruebas de producto.

Se prepara `docs/examen-oficial/` con inventario de disponibilidad y recepción, instrucciones individuales para Carolina/Edier/Vega y preparación común. El plan añade un circuito explícito de aportes y revisiones cruzadas documentales para los cuatro integrantes, con PR futuros sin números inventados. PR #1 sigue en borrador y las cinco SPEC pendientes de revisión humana.
