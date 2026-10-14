#!/usr/bin/env python3
"""Valida y exporta las cinco SPEC desde su bloque canónico; --check es de solo lectura.

Fuente única: un bloque JSON por SPEC entre los marcadores del starter oficial
(`<!-- spec-data:start -->` / `<!-- spec-data:end -->`). La estructura sigue el anexo
13_spec-structured-template.json y añade los campos que exige la sección 8 del
enunciado (actores, alcance, exclusiones, supuestos, preguntas, dependencias) y, por
cada AC, sus requisitos y el tipo de escenario. Solo usa biblioteca estándar.
"""
import argparse
import json
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
SPEC_IDS = [f'SPEC-{i:02}' for i in range(1, 6)]
ALL_REQUIREMENTS = [f'RF{i:02}' for i in range(1, 13)] + [f'RNF{i:02}' for i in range(1, 7)]
KINDS = {'positivo', 'negativo', 'limite', 'permiso', 'fallo'}
MARKER = re.compile(r'<!-- spec-data:start -->\s*```json\s*\n(.*?)\n\s*```\s*<!-- spec-data:end -->', re.S)
PLACEHOLDER = re.compile(r'\[[^\]\s][^\]]*\]')
TEXT_FIELDS = ('version', 'name', 'status', 'description')
TEXT_LISTS = ('actors', 'scope', 'exclusions', 'process', 'businessRules', 'edgeCases', 'assumptions', 'dependencies')


def unique_object(pairs):
    result = {}
    for key, value in pairs:
        if key in result:
            raise ValueError(f'clave JSON duplicada: {key}')
        result[key] = value
    return result


def text(value, where):
    if not isinstance(value, str) or not value.strip():
        raise ValueError(f'{where}: debe ser texto no vacío')
    if value.startswith('Pendiente:') or PLACEHOLDER.search(value):
        raise ValueError(f'{where}: contiene un marcador de plantilla sin completar')
    return value


def text_list(value, where, allow_empty=False):
    if not isinstance(value, list) or (not value and not allow_empty):
        raise ValueError(f'{where}: debe ser una lista no vacía')
    for i, item in enumerate(value, 1):
        text(item, f'{where}[{i}]')
    return value


def read_spec(path):
    name = path.name
    blocks = MARKER.findall(path.read_text(encoding='utf-8'))
    if len(blocks) != 1:
        raise ValueError(f'{name}: se requiere exactamente un bloque spec-data JSON')
    obj = json.loads(blocks[0], object_pairs_hook=unique_object)
    if not isinstance(obj, dict) or obj.get('id') != path.stem:
        raise ValueError(f'{name}: ID no coincide con el nombre del archivo')
    number = path.stem[-2:]
    for key in TEXT_FIELDS:
        text(obj.get(key), f'{name}: {key}')

    story = obj.get('story')
    if not isinstance(story, dict) or story.get('id') != f'HU-{number}':
        raise ValueError(f'{name}: story.id debe ser HU-{number}')
    for key in ('as', 'want', 'soThat'):
        text(story.get(key), f'{name}: story.{key}')

    requirements = obj.get('requirements')
    if (not isinstance(requirements, list) or not requirements
            or any(r not in ALL_REQUIREMENTS for r in requirements)
            or len(set(requirements)) != len(requirements)):
        raise ValueError(f'{name}: requirements inválido (RF01–RF12 / RNF01–RNF06, sin repetir)')

    for key in TEXT_LISTS:
        text_list(obj.get(key), f'{name}: {key}')
    text_list(obj.get('openQuestions'), f'{name}: openQuestions', allow_empty=True)

    inputs = obj.get('inputs')
    if not isinstance(inputs, list) or not inputs:
        raise ValueError(f'{name}: inputs debe ser una lista no vacía')
    for i, item in enumerate(inputs, 1):
        if not isinstance(item, dict):
            raise ValueError(f'{name}: inputs[{i}] debe ser objeto')
        text(item.get('field'), f'{name}: inputs[{i}].field')
        text(item.get('type'), f'{name}: inputs[{i}].type')
        text_list(item.get('validation'), f'{name}: inputs[{i}].validation')

    outputs = obj.get('outputs')
    if not isinstance(outputs, list) or not outputs:
        raise ValueError(f'{name}: outputs debe ser una lista no vacía')
    for i, item in enumerate(outputs, 1):
        if not isinstance(item, dict):
            raise ValueError(f'{name}: outputs[{i}] debe ser objeto')
        text(item.get('condition'), f'{name}: outputs[{i}].condition')
        text(item.get('result'), f'{name}: outputs[{i}].result')

    criteria = obj.get('acceptanceCriteria')
    if not isinstance(criteria, list) or not criteria:
        raise ValueError(f'{name}: faltan AC')
    covered = set()
    for i, ac in enumerate(criteria, 1):
        expected_id = f'AC-{number}-{i:02}'
        if not isinstance(ac, dict) or ac.get('id') != expected_id:
            raise ValueError(f'{name}: ID AC inválido; esperado {expected_id}')
        refs = ac.get('requirements')
        if not isinstance(refs, list) or not refs or len(set(refs)) != len(refs):
            raise ValueError(f'{name}: {expected_id} sin requisitos')
        outside = [r for r in refs if r not in requirements]
        if outside:
            raise ValueError(f'{name}: {expected_id} referencia requisitos ausentes de la SPEC: {outside}')
        covered.update(refs)
        if ac.get('kind') not in KINDS:
            raise ValueError(f'{name}: {expected_id} kind debe ser uno de {sorted(KINDS)}')
        for key in ('given', 'when', 'then'):
            text(ac.get(key), f'{name}: {expected_id}.{key}')
    missing = [r for r in requirements if r not in covered]
    if missing:
        raise ValueError(f'{name}: requisitos sin AC: {missing}')
    return obj


def load_all(root=ROOT):
    paths = sorted((root / 'specs').glob('SPEC-*.md'))
    if [p.stem for p in paths] != SPEC_IDS:
        raise ValueError('Se requieren exactamente SPEC-01 a SPEC-05')
    objects = [read_spec(path) for path in paths]
    covered = {r for obj in objects for ac in obj['acceptanceCriteria'] for r in ac['requirements']}
    missing = [r for r in ALL_REQUIREMENTS if r not in covered]
    if missing:
        raise ValueError(f'Requisitos sin AC en ninguna SPEC: {missing}')
    return objects


def export(root=ROOT, check=False):
    objects = load_all(root)
    out = root / 'specs' / 'generated'
    extras = {p.stem for p in out.glob('SPEC-*.json')} - set(SPEC_IDS)
    if extras:
        raise ValueError(f'JSON derivados inesperados: {sorted(extras)}')
    rendered = {obj['id']: json.dumps(obj, ensure_ascii=False, indent=2) + '\n' for obj in objects}
    if check:
        bad = [f'{i}.json' for i, data in rendered.items()
               if not (out / f'{i}.json').exists() or (out / f'{i}.json').read_text(encoding='utf-8') != data]
        if bad:
            raise ValueError('JSON no coincide con fuente: ' + ', '.join(bad))
    else:
        out.mkdir(parents=True, exist_ok=True)
        for spec_id, data in rendered.items():
            (out / f'{spec_id}.json').write_text(data, encoding='utf-8', newline='\n')
    return len(objects), sum(len(obj['acceptanceCriteria']) for obj in objects)


if __name__ == '__main__':
    parser = argparse.ArgumentParser(description=__doc__.splitlines()[0])
    parser.add_argument('--check', action='store_true', help='solo comprobar; no escribe archivos')
    args = parser.parse_args()
    try:
        specs, criteria = export(check=args.check)
    except (ValueError, OSError) as error:
        parser.exit(1, f'{error}\n')
    print(f'{specs} SPEC y {criteria} AC validados; JSON ' + ('coherentes' if args.check else 'exportados'))
