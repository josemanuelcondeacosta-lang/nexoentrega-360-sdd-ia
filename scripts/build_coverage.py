"""Genera matriz documental por AC a partir de las SPEC validadas."""
from export_specs import ROOT, read_spec

MAP = {
    1: {'RF01': [1,4,5,6,8,10], 'RF02': [2,3,7,9], 'RNF01': [1,2,3,5,6,7,8,9,10], 'RNF06': [10]},
    2: {'RF03': [1,2,4,5,6,7], 'RF04': [3,8,9,10], 'RNF05': [11]},
    3: {'RF04': [5,6,7,13], 'RF05': [1,2,3,8,9], 'RF06': [5,6,13], 'RF07': [4,10,11,12], 'RNF02': [1,2,3,5,6,7,8,9,13]},
    4: {'RF08': [1,2,5,6,10], 'RF09': [3,4,7,8], 'RNF03': [9], 'RNF04': [9], 'RNF06': [3,7]},
    5: {'RF10': [1,5,10], 'RF11': [2,4,6,7,8,11,12], 'RF12': [3,9,13], 'RNF01': [5,8,11], 'RNF06': [4,12,13]},
}
MODULES = {1:'auth / policy', 2:'requests / frontend', 3:'inventory / transitions / idempotency', 4:'queries / audit', 5:'ai-adapter / review'}
KINDS = {1:'API + PostgreSQL + seguridad', 2:'dominio + API + PostgreSQL + E2E', 3:'PostgreSQL + concurrencia + reloj controlado', 4:'API + PostgreSQL; rendimiento para AC-04-09', 5:'contrato + adaptador simulado + API + PostgreSQL'}

def main():
    objects = [read_spec(ROOT / 'specs' / f'SPEC-{i:02}.md') for i in range(1,6)]
    lines = ['# Matriz de cobertura y verificación — G1',
        'Estado: **BORRADOR / SIN APROBACIÓN**. Cobertura nominal de RF01–RF12 y RNF01–RNF06; no demuestra cumplimiento del examen. Todas las asignaciones RNF son provisionales hasta CL-13. RNF03/RNF04 comparten rendimiento como propuesta de cotejo, no como interpretación oficial.',
        '## Requisito → historia → SPEC → criterios',
        '| Requisito | Historia | SPEC | AC propuestos | Cotejo oficial |',
        '|---|---|---|---|---|']
    for n, obj in enumerate(objects, 1):
        for req, numbers in MAP[n].items():
            ids = ', '.join(f'AC-{n:02}-{i:02}' for i in numbers)
            lines.append(f'| {req} | HU-{n:02} | {obj["id"]} | {ids} | Pendiente |')
    lines += ['', '## Plan de verificación por criterio',
        'El resultado esperado se fija antes de ejecución. IDs T-AC identifican pruebas PLANIFICADAS; no son archivos de test existentes. Módulos y tareas son propuestas para G2. Diseño/contrato pendiente G2 en todas las filas. No hay PR de implementación, SHA probado ni evidencia de producto.',
        '| AC | Requisitos propuestos | Tarea / módulo previsto | Prueba prevista | Resultado esperado | Observado / cumple | PR / SHA / evidencia |',
        '|---|---|---|---|---|---|---|']
    for n, obj in enumerate(objects, 1):
        for ac in obj['acceptanceCriteria']:
            index = int(ac['id'][-2:])
            requirements = ', '.join(req for req, numbers in MAP[n].items() if index in numbers)
            if not requirements:
                raise ValueError(f'AC sin requisito: {ac["id"]}')
            expected = ac['expected'].replace('|', '\\|')
            lines.append(f'| {ac["id"]} | {requirements} | TASK-{n:02} / {MODULES[n]} | T-{ac["id"]} / {KINDS[n]} | {expected} | No ejecutado / pendiente | Pendiente |')
    lines += ['', '## Dependencias y responsables propuestos',
        '| Tarea | Dependencias obligatorias | Responsable principal propuesto |',
        '|---|---|---|',
        '| TASK-01 Identidad | SPEC-01 aprobada + G2 revisado | José Conde; revisión de otro integrante |',
        '| TASK-02 Solicitudes/UI | TASK-01 + SPEC-02 aprobada | José Conde; Carolina revisa trazabilidad |',
        '| TASK-03 Inventario | TASK-01/02 + SPEC-03 aprobada | Edier; revisión de otro integrante |',
        '| TASK-04 Consultas/auditoría | SPEC-04 aprobada; auditoría debe integrarse en TASK-02/03 | Edier; revisión de otro integrante |',
        '| TASK-05 IA | TASK-01/02/04 + SPEC-05 aprobada | José Vega; revisión de otro integrante |',
        '| Validación y CI | Contratos G2 y módulos revisados | José Vega; todos aportan pruebas reales |',
        '', 'Carolina coordina cotejo de requisitos/SPEC. Ninguna fila acredita autoría, trabajo o revisión ya realizada. Registrar contribuciones reales por commit/PR/prueba cuando existan.']
    path = ROOT / 'docs' / 'coverage-matrix.md'
    path.write_text('\n\n'.join(lines[:4]) + '\n' + '\n'.join(lines[4:]) + '\n', encoding='utf-8')
    print(f'Matriz preparada: {sum(len(o["acceptanceCriteria"]) for o in objects)} AC')

if __name__ == '__main__':
    main()
