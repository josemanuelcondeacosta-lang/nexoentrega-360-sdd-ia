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
- No se ha implementado código, ejecutado pruebas, abierto PR ni revisado el proyecto.
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
