#!/usr/bin/env python3
"""Exporta el bloque spec-json de cada Markdown a specs/generated/ (fuente única)."""
from pathlib import Path
import json,re,sys
root=Path(__file__).resolve().parents[1]
out=root/'specs'/'generated'
out.mkdir(exist_ok=True)
check='--check' in sys.argv
bad=[]
for f in sorted((root/'specs').glob('SPEC-*.md')):
 match=re.search(r'```spec-json\s*\n(.*?)\n```',f.read_text(encoding='utf-8'),re.S)
 if not match: raise SystemExit(f'Falta spec-json en {f}')
 obj=json.loads(match.group(1)); target=out/(f.stem+'.json')
 data=json.dumps(obj,ensure_ascii=False,indent=2)+'\n'
 if check:
  if not target.exists() or target.read_text(encoding='utf-8')!=data: bad.append(str(target))
 else: target.write_text(data,encoding='utf-8')
if bad: raise SystemExit('JSON no coincide con fuente: '+', '.join(bad))
print('SPEC JSON coherentes' if check else 'SPEC JSON exportados')
