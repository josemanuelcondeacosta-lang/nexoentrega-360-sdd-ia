#!/usr/bin/env python3
"""Valida y exporta las cinco SPEC; --check es de solo lectura."""
import argparse
import json
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
EXPECTED = {f'SPEC-{i:02}' for i in range(1, 6)}


def unique_object(pairs):
    result = {}
    for key, value in pairs:
        if key in result:
            raise ValueError(f'Clave JSON duplicada: {key}')
        result[key] = value
    return result


def read_spec(path):
    text = path.read_text(encoding='utf-8')
    blocks = re.findall(r'^```spec-json[^\S\n]*\n(.*?)^```[^\S\n]*$', text, re.M | re.S)
    if len(blocks) != 1:
        raise ValueError(f'{path.name}: se requiere exactamente un bloque spec-json')
    obj = json.loads(blocks[0], object_pairs_hook=unique_object)
    if not isinstance(obj, dict) or obj.get('id') != path.stem:
        raise ValueError(f'{path.name}: ID no coincide')
    for key in ('version', 'title', 'userStory'):
        if not isinstance(obj.get(key), str) or not obj[key].strip():
            raise ValueError(f'{path.name}: {key} debe ser texto no vacío')
    for key in ('inputs', 'process', 'outputs', 'rules', 'edgeCases'):
        values = obj.get(key)
        if not isinstance(values, list) or not values or any(not isinstance(v, str) or not v.strip() or v.startswith('Pendiente:') for v in values):
            raise ValueError(f'{path.name}: {key} incompleto')
    req = obj.get('requirements')
    if not isinstance(req, dict):
        raise ValueError(f'{path.name}: requirements inválido')
    for key, prefix, maximum in [('rf', 'RF', 12), ('rnf', 'RNF', 6)]:
        values = req.get(key)
        allowed = {f'{prefix}{i:02}' for i in range(1, maximum + 1)}
        if not isinstance(values, list) or not values or any(not isinstance(v, str) or v not in allowed for v in values) or len(set(values)) != len(values):
            raise ValueError(f'{path.name}: {key} inválido')
    criteria = obj.get('acceptanceCriteria')
    if not isinstance(criteria, list) or not criteria:
        raise ValueError(f'{path.name}: faltan AC')
    for i, ac in enumerate(criteria, 1):
        expected_id = f'AC-{path.stem[-2:]}-{i:02}'
        if not isinstance(ac, dict) or ac.get('id') != expected_id:
            raise ValueError(f'{path.name}: ID AC inválido; esperado {expected_id}')
        if ac.get('status') != 'draft' or not isinstance(ac.get('expected'), str) or not ac['expected'].strip():
            raise ValueError(f'{path.name}: AC sin esperado o estado de borrador')
        if f"- **{ac['id']}:** {ac['expected']}" not in text:
            raise ValueError(f'{path.name}: AC Markdown/JSON divergen: {ac["id"]}')
    return obj


def export(root=ROOT, check=False):
    paths = sorted((root / 'specs').glob('SPEC-*.md'))
    if {p.stem for p in paths} != EXPECTED:
        raise ValueError('Se requieren exactamente SPEC-01 a SPEC-05')
    objects = [read_spec(path) for path in paths]
    for key, prefix, count in [('rf', 'RF', 12), ('rnf', 'RNF', 6)]:
        actual = {req for obj in objects for req in obj['requirements'][key]}
        if actual != {f'{prefix}{i:02}' for i in range(1, count + 1)}:
            raise ValueError(f'Cobertura nominal incompleta: {prefix}')
    out = root / 'specs' / 'generated'
    extras = {p.stem for p in out.glob('SPEC-*.json')} - EXPECTED
    if extras:
        raise ValueError(f'JSON derivados inesperados: {sorted(extras)}')
    if not check:
        out.mkdir(parents=True, exist_ok=True)
    bad = []
    for obj in objects:
        target = out / (obj['id'] + '.json')
        data = json.dumps(obj, ensure_ascii=False, indent=2) + '\n'
        if check:
            if not target.exists() or target.read_text(encoding='utf-8') != data:
                bad.append(target.name)
        else:
            target.write_text(data, encoding='utf-8')
    if bad:
        raise ValueError('JSON no coincide con fuente: ' + ', '.join(bad))
    if not check:
        print('SPEC JSON exportados')
    return len(objects), sum(len(obj['acceptanceCriteria']) for obj in objects)


if __name__ == '__main__':
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--check', action='store_true')
    args = parser.parse_args()
    try:
        specs, criteria = export(check=args.check)
    except (ValueError, OSError) as error:
        parser.exit(1, f'{error}\n')
    print(f'{specs} SPEC y {criteria} AC validados; JSON ' + ('coherentes' if args.check else 'exportados'))
