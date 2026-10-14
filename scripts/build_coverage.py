"""Genera la matriz de cobertura y la traza RF/RNF desde las SPEC validadas; --check no escribe.

Los vínculos requisito → AC salen del campo `requirements` de cada AC en la fuente única,
no de un mapa mantenido a mano.
"""
import argparse
import csv
import io
import sys

from export_specs import ALL_REQUIREMENTS, ROOT, load_all

SOURCES = {
    **{r: 'OF-01 §2, p. 3' for r in ('RF01', 'RF02')},
    **{f'RF{i:02}': 'OF-01 §3, p. 4' for i in range(3, 8)},
    **{f'RF{i:02}': 'OF-01 §4, p. 5' for i in range(8, 13)},
    **{f'RNF{i:02}': 'OF-01 §5, p. 6' for i in range(1, 7)},
}
MODULES = {
    'SPEC-01': 'auth / policy',
    'SPEC-02': 'requests / frontend',
    'SPEC-03': 'inventory / transitions / idempotency',
    'SPEC-04': 'queries / audit / ops',
    'SPEC-05': 'ai-adapter / review',
}
TEST_KINDS = {
    'SPEC-01': 'API + PostgreSQL + seguridad',
    'SPEC-02': 'dominio + API + PostgreSQL + E2E',
    'SPEC-03': 'PostgreSQL + concurrencia + reloj controlado',
    'SPEC-04': 'API + PostgreSQL; carga y operación según AC',
    'SPEC-05': 'contrato + fixtures + API + PostgreSQL',
}
MATRIX = ROOT / 'docs' / 'coverage-matrix.md'
TRACE = ROOT / 'docs' / 'evidence' / 'traceability.csv'


def cell(value):
    return value.replace('|', '\\|').replace('\n', ' ')


def build(objects):
    by_requirement = {r: [] for r in ALL_REQUIREMENTS}
    for obj in objects:
        for ac in obj['acceptanceCriteria']:
            for req in ac['requirements']:
                by_requirement[req].append((obj, ac))

    lines = [
        '# Matriz de cobertura y verificación — G1',
        '',
        'Archivo generado por `python scripts/build_coverage.py`; no editar a mano. Estado: **BORRADOR 0.3 / SIN APROBACIÓN HUMANA**. '
        'Cotejada contra el enunciado oficial (OF-01) y sus anexos; las diferencias y correcciones están en '
        '[cotejo-oficial-G1.md](reviews/cotejo-oficial-G1.md). Cobertura documental: no demuestra cumplimiento de la aplicación.',
        '',
        '## Requisito → fuente oficial → historia y SPEC → criterios',
        '',
        '| Requisito | Fuente oficial | SPEC (historia) | AC | Tipos de escenario | Estado |',
        '|---|---|---|---|---|---|',
    ]
    for req in ALL_REQUIREMENTS:
        pairs = by_requirement[req]
        specs = []
        for obj, _ in pairs:
            label = f"{obj['id']} ({obj['story']['id']})"
            if label not in specs:
                specs.append(label)
        acs = ', '.join(ac['id'] for _, ac in pairs)
        kinds = ', '.join(sorted({ac['kind'] for _, ac in pairs}))
        lines.append(f'| {req} | {SOURCES[req]} | {"; ".join(specs)} | {acs} | {kinds} | Especificado y cotejado; revisión humana pendiente |')

    lines += [
        '',
        '## Plan de verificación por criterio',
        '',
        'El resultado esperado se fija antes de ejecutar. Los identificadores T-AC nombran pruebas **planificadas**, no archivos existentes. '
        'Módulos y tareas son propuestas para G2. No hay PR de implementación, SHA probado ni evidencia de producto.',
        '',
        '| AC | Requisitos | Tipo | Escenario (dado / cuando) | Resultado esperado | Módulo y prueba prevista | Observado | PR / SHA / evidencia |',
        '|---|---|---|---|---|---|---|---|',
    ]
    for obj in objects:
        for ac in obj['acceptanceCriteria']:
            scenario = cell(f"Dado {ac['given']}; cuando {ac['when']}")
            lines.append(
                f"| {ac['id']} | {', '.join(ac['requirements'])} | {ac['kind']} | {scenario} | {cell(ac['then'])} | "
                f"{MODULES[obj['id']]}; T-{ac['id']} ({TEST_KINDS[obj['id']]}) | No ejecutado | Pendiente |")

    lines += [
        '',
        '## Dependencias y responsables propuestos',
        '',
        '| Tarea | Dependencias obligatorias | Responsable principal propuesto |',
        '|---|---|---|',
        '| TASK-01 Identidad | SPEC-01 aprobada + G2 revisado | José Conde; revisión de otro integrante |',
        '| TASK-02 Solicitudes/UI | TASK-01 + SPEC-02 aprobada | José Conde; Carolina revisa trazabilidad |',
        '| TASK-03 Inventario | TASK-01/02 + SPEC-03 aprobada | Edier; revisión de otro integrante |',
        '| TASK-04 Consultas/auditoría | SPEC-04 aprobada; auditoría integrada en TASK-02/03 | Edier; revisión de otro integrante |',
        '| TASK-05 IA | TASK-01/02/04 + SPEC-05 aprobada | José Vega; revisión de otro integrante |',
        '| Validación y CI | Contratos G2 y módulos revisados | José Vega; todos aportan pruebas reales |',
        '',
        'Carolina coordina el cotejo de requisitos y SPEC. Ninguna fila acredita autoría, trabajo o revisión ya realizada.',
        '',
    ]
    matrix = '\n'.join(lines)

    buffer = io.StringIO()
    writer = csv.writer(buffer, lineterminator='\n')
    writer.writerow(['Requirement', 'SpecPathAndVersion', 'AcceptanceCriterion', 'ADRorContract', 'Task',
                     'CodePath', 'TestID', 'PR', 'Evidence', 'Status'])
    for req in ALL_REQUIREMENTS:
        pairs = by_requirement[req]
        spec_paths = []
        for obj, _ in pairs:
            ref = f"specs/{obj['id']}.md@{obj['version']}"
            if ref not in spec_paths:
                spec_paths.append(ref)
        writer.writerow([req, '; '.join(spec_paths), '; '.join(ac['id'] for _, ac in pairs),
                         'Pendiente G2', 'Pendiente G2', '', '; '.join(f"T-{ac['id']}" for _, ac in pairs),
                         '', '', 'Especificado; sin implementar ni probar'])
    return matrix, '\ufeff' + buffer.getvalue()


def main():
    parser = argparse.ArgumentParser(description=__doc__.splitlines()[0])
    parser.add_argument('--check', action='store_true', help='solo comprobar; no escribe archivos')
    args = parser.parse_args()
    objects = load_all()
    matrix, trace = build(objects)
    targets = [(MATRIX, matrix), (TRACE, trace)]
    if args.check:
        stale = [str(p.relative_to(ROOT)) for p, content in targets
                 if not p.exists() or p.read_text(encoding='utf-8') != content]
        if stale:
            print('Matriz desactualizada: ' + ', '.join(stale), file=sys.stderr)
            return 1
        print('Matriz y traza coherentes con las SPEC')
        return 0
    for path, content in targets:
        path.parent.mkdir(parents=True, exist_ok=True)
        path.write_text(content, encoding='utf-8', newline='\n')
    print(f'Matriz preparada: {sum(len(o["acceptanceCriteria"]) for o in objects)} AC; traza RF/RNF en {TRACE.relative_to(ROOT)}')
    return 0


if __name__ == '__main__':
    sys.exit(main())
