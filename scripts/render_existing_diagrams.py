"""Renderiza los seis diagramas existentes; no cambia su contenido Mermaid."""
import hashlib
import json
import os
from pathlib import Path
import re
import subprocess

ROOT = Path(__file__).resolve().parents[1]
FIGURES = ROOT / 'docs/architecture/figures'
TEMP = ROOT / 'test-results/mermaid'
FIGURES.mkdir(parents=True, exist_ok=True)
TEMP.mkdir(parents=True, exist_ok=True)
bundles = list((Path(os.environ['LOCALAPPDATA']) / 'npm-cache/_npx').glob('*/node_modules/mermaid/dist/mermaid.min.js'))
bundle = next((p for p in bundles if json.loads((p.parent.parent / 'package.json').read_text(encoding='utf-8'))['version'] == '12.2.0'), None)
if bundle is None:
    raise SystemExit('Resolver Mermaid 12.2.0 con npm exec --package=@mermaid-js/mermaid-cli@12.0.0 -- mmdc --version')
records = []
jobs = []
for source in sorted((ROOT / 'docs/architecture').glob('*.md')):
    diagrams = re.findall(r'```mermaid\s*\n(.*?)```', source.read_text(encoding='utf-8'), flags=re.S)
    if len(diagrams) != 1:
        raise SystemExit(f'{source.name}: se esperaba un diagrama, encontrados {len(diagrams)}')
    draft = TEMP / (source.stem + '.mmd')
    draft.write_text(diagrams[0], encoding='utf-8')
    out = FIGURES / (source.stem + '.png')
    jobs.append({'input': str(draft), 'output': str(out)})
    records.append({'source': source.relative_to(ROOT).as_posix(), 'sourceSha256': hashlib.sha256(source.read_bytes()).hexdigest(), 'figure': out.name})
config = TEMP / 'render.json'
config.write_text(json.dumps({'bundle': str(bundle), 'jobs': jobs}), encoding='utf-8')
subprocess.run(['node', 'scripts/render_diagrams.cjs', str(config)], cwd=ROOT, check=True, timeout=180)
for record in records:
    record['figureSha256'] = hashlib.sha256((FIGURES / record['figure']).read_bytes()).hexdigest()
(FIGURES / 'manifest.json').write_text(json.dumps({'renderer': 'Mermaid 12.2.0 + Playwright 1.56.1 / Chromium', 'figures': records}, indent=2) + '\n', encoding='utf-8')
print(f'{len(records)} diagramas originales renderizados sin modificar fuentes.')
