# NexoEntrega 360 — SDD con IA

Sistema académico para distribución comunitaria de kits en dos organizaciones de Barranquilla.

## Equipo
| Integrante | Código | Responsabilidad propuesta |
|---|---|---|
| Cantillo Carolina | 20231153656 | Especificaciones y documentación |
| Castro Edier | 202311254074 | API y datos |
| Conde José | 202321256268 | Interfaz e integración |
| Vega José | 202321256198 | Pruebas e integración de IA |

**Las responsabilidades deben ser confirmadas por el equipo.** Todos deben aportar código o pruebas y revisar el trabajo de otro integrante.

## Estado
- G1: **BORRADOR, SIN APROBACIÓN**.
- G1 ampliado a cinco historias y 57 AC; aplicación y pruebas de producto pendientes de aprobación.
- Se ejecutaron nueve pruebas del verificador documental; ver `docs/evidence/g1-validation.md`. No equivale a aprobación G1 ni cumplimiento de RF/RNF.
- Enunciado vinculante: plataforma oficial del examen. No sustituir por este README.

## Estructura
- `docs/constitution.md`: principios comprobables.
- `docs/clarifications/clarification-log.md`: preguntas y supuestos pendientes.
- `docs/risks/risk-register.md`: riesgos iniciales.
- `docs/reviews/gate-review-G1.md`: plantilla de revisión humana.
- `specs/SPEC-01.md` a `SPEC-05.md`: borradores basados en RF/RNF.
- `scripts/export_specs.py`: genera JSON derivado (nunca editar a mano).

## Antes de programar
1. Confirmar responsabilidades y resolver o asumir provisionalmente las aclaraciones.
2. Revisar los criterios de aceptación y añadir los faltantes para TODOS los RF/RNF.
3. Ejecutar `python scripts/export_specs.py`.
4. Revisar G1 por una persona distinta del autor, registrar fecha UTC y commit.
5. Aprobar la especificación relevante **antes** de implementar esa funcionalidad.

## Enlace del repositorio
https://github.com/josemanuelcondeacosta-lang/nexoentrega-360-sdd-ia

## Paquete de revisión G1

- [Auditoría inicial](docs/reviews/initial-audit.md) y [fuentes/supuestos](docs/requirements-source.md).
- [Matriz de cobertura por AC](docs/coverage-matrix.md), con pruebas previstas y resultados pendientes.
- [Fronteras de confianza IA](docs/ai-trust-boundaries.md).
- [Registro de revisión humana](docs/reviews/gate-review-G1.md) y [descripción de PR preparada](docs/reviews/g1-pr-description.md).
- [Cotejo oficial preparado](docs/reviews/cotejo-oficial-G1.md): archivos faltantes y comparación pendiente por RF/RNF.
- [Plan de colaboración](docs/team/plan-colaboracion.md): tareas por integrante, evidencias y pasos en GitHub/VS Code.
- [Material oficial pendiente](docs/examen-oficial/README.md) e [inventario de disponibilidad](docs/examen-oficial/inventario.md).
- Instrucciones individuales: [Carolina](docs/team/instrucciones-carolina.md), [Edier](docs/team/instrucciones-edier.md) y [Vega](docs/team/instrucciones-vega.md).

Validación documental reproducible (Python 3.11.9 usado; biblioteca estándar):

```sh
python scripts/export_specs.py
python scripts/export_specs.py --check
python scripts/build_coverage.py
python -m unittest discover -s scripts -p 'test_*.py' -v
```

Enunciado/anexos y revisión de un integrante faltan. Mantener `main` como integración; revisar rama `docs/g1-specifications` antes de fusionar. No crear tags de aplicación o entrega sin aplicación verificada.
