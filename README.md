# NexoEntrega 360 — SDD con IA

Paquete documental reunido para revisión: [índice](docs/README.md) y [revisión completa con procedencia y pendientes](docs/reviews/revision-completa.md). Rama de consolidación propuesta; no implica merge de PR ni aprobación G1/G2.

Sistema académico para distribución comunitaria de kits en dos organizaciones de Barranquilla.

## Aplicación provisional

Implementación adelantada por solicitud del coordinador; G1/G2 continúan pendientes de cierre humano. Laravel + PostgreSQL, interfaz por rol y pruebas reales. IA limitada a fallback, sin proveedor LLM real.

Ejecutar `docker compose up -d --build` y abrir http://127.0.0.1:8000. Consultar [cuentas y ejecución](docs/delivery/runbook-provisional.md) y [resultados observados](docs/evidence/runtime/verification.md). La referencia a 68 AC de la preparación inicial abajo es histórica: el paquete actual contiene 73 AC; no todos están verificados por aplicación.

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
- SPEC 0.3-draft cotejadas con el enunciado oficial: cinco historias y 68 AC en el formato del anexo 13; aplicación y pruebas de producto pendientes de aprobación.
- Se ejecutaron 14 pruebas del verificador documental; ver `docs/evidence/g1-validation.md`. No equivale a aprobación G1 ni cumplimiento de RF/RNF.
- Enunciado vinculante: `docs/examen-oficial/originales/Examen_Estudiantes_SDD_IA.pdf` y sus anexos. No sustituir por este README.

## Estructura
- `docs/examen-oficial/`: enunciado y anexos oficiales sin modificar, con inventario y SHA-256.
- `docs/constitution.md`: principios comprobables.
- `docs/clarifications/clarification-log.md`: preguntas y supuestos pendientes.
- `docs/risks/risk-register.md`: riesgos iniciales.
- `docs/reviews/gate-review-G1.md`: plantilla de revisión humana.
- `specs/SPEC-01.md` a `SPEC-05.md`: fuente única de cada SPEC (bloque `spec-data` del starter oficial).
- `scripts/export_specs.py`: valida y genera `specs/generated/*.json` (nunca editar a mano).
- `scripts/build_coverage.py`: genera `docs/coverage-matrix.md` y `docs/evidence/traceability.csv` desde las SPEC.

## Antes de programar
1. Confirmar responsabilidades y resolver o asumir provisionalmente las aclaraciones.
2. Revisar los criterios de aceptación y añadir los faltantes para TODOS los RF/RNF.
3. Ejecutar `python scripts/export_specs.py`.
4. Revisar G1 por una persona distinta del autor, registrar fecha UTC y commit.
5. Aprobar la especificación relevante **antes** de implementar esa funcionalidad.

## Enlace del repositorio
https://github.com/josemanuelcondeacosta-lang/nexoentrega-360-sdd-ia

## Diseño G2 parcial, pendiente de revisión

- [ADR-001: estructura y límites](docs/adr/ADR-001-estructura-limites.md): Laravel + PostgreSQL, monolito modular propuesto.
- [Contexto](docs/architecture/contexto.md) y [componentes/fronteras](docs/architecture/componentes.md), Mermaid.
- [Contrato integrado OpenAPI](contracts/openapi.yaml) y [comandos/pendientes](contracts/README.md).
- [Preguntas preparadas para el docente](docs/clarifications/envio-docente-CL16-18.md): no enviadas por Codex.
- [Revisión parcial G2](docs/reviews/gate-review-G2.md) y [evidencia documental](docs/evidence/g2-arquitectura-validacion.md).

G1 y G2 siguen pendientes; diseño no equivale a implementación aprobada.

## Paquete de revisión G1

- [Auditoría inicial](docs/reviews/initial-audit.md) y [fuentes/supuestos](docs/requirements-source.md).
- [Matriz de cobertura por AC](docs/coverage-matrix.md), con pruebas previstas y resultados pendientes.
- [Fronteras de confianza IA](docs/ai-trust-boundaries.md).
- [Registro de revisión humana](docs/reviews/gate-review-G1.md) y [descripción de PR preparada](docs/reviews/g1-pr-description.md).
- [Cotejo oficial](docs/reviews/cotejo-oficial-G1.md): RF01–RF12 y RNF01–RNF06 contra las SPEC, diferencias D-01 a D-24 y correcciones.
- [Plan de colaboración](docs/team/plan-colaboracion.md): tareas por integrante, evidencias y pasos en GitHub/VS Code.
- [Material oficial](docs/examen-oficial/README.md) e [inventario con hashes](docs/examen-oficial/inventario.md).
- [Bitácora de IA](docs/evidence/ai-log/): intervenciones registradas con el formato del anexo 08.
- Instrucciones individuales: [Carolina](docs/team/instrucciones-carolina.md), [Edier](docs/team/instrucciones-edier.md) y [Vega](docs/team/instrucciones-vega.md).

Validación documental reproducible (Python 3.11 o superior; biblioteca estándar):

```sh
python scripts/export_specs.py --check
python scripts/build_coverage.py --check
python -m unittest discover -s scripts -p "test_*.py" -v
```

Si una SPEC cambia: `python scripts/export_specs.py` y `python scripts/build_coverage.py`, y versionar los archivos generados.

Falta la revisión humana independiente. Mantener `main` como integración; revisar las ramas `docs/g1-specifications` y `docs/g1-edier-cotejo-oficial` antes de fusionar. No crear tags de aplicación o entrega sin aplicación verificada.
