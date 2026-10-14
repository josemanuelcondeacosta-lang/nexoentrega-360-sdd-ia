"""Escaneo limitado de secretos en archivos versionables, sin imprimir su contenido."""
from pathlib import Path
import re
import subprocess

ROOT=Path(__file__).resolve().parents[1]
patterns=[re.compile(rb'gh[pousr]_[A-Za-z0-9]{30,}'),re.compile(rb'github_pat_[A-Za-z0-9_]{30,}'),re.compile(rb'AKIA[A-Z0-9]{16}'),
          re.compile(rb'-----BEGIN (?:RSA |EC |OPENSSH )?PRIVATE KEY-----'),re.compile(rb'AIza[A-Za-z0-9_-]{30,}')]
names=subprocess.check_output(['git','ls-files','--cached','--others','--exclude-standard','-z'],cwd=ROOT).decode().split('\0')
findings=[];checked=0
for name in sorted(set(names)):
    path=ROOT/name
    if not name or not path.is_file() or path.suffix.lower() in {'.pdf','.zip','.png','.jpg','.woff','.woff2'}:continue
    if path.name=='.env' or (path.name.startswith('.env.') and path.name!='.env.example'):findings.append((name,'archivo de entorno privado'));continue
    content=path.read_bytes();checked+=1
    if any(p.search(content) for p in patterns):findings.append((name,'patrón de credencial/private key'))
if findings:
    for name,reason in findings:print(name+': '+reason)
    raise SystemExit(1)
print(f'{checked} archivos escaneados; sin patrones de secretos detectados. No sustituye revisión humana de credenciales.')
