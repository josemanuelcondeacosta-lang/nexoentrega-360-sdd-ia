"""Cronometra clon remoto, construcción y arranque en un proyecto Docker aislado."""
import argparse
import datetime
import http.client
import json
import os
from pathlib import Path
import subprocess
import time
import urllib.error
import urllib.request


def main():
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--destination', required=True)
    parser.add_argument('--output', required=True)
    parser.add_argument('--branch', default='integration/examen-candidate')
    parser.add_argument('--project', default='nexo-cleanclone-verified')
    parser.add_argument('--port', type=int, default=8002)
    args = parser.parse_args()
    destination = Path(args.destination).resolve()
    if destination.exists():
        parser.error('El destino debe ser nuevo; no se elimina ningún clon existente.')
    output = Path(args.output).resolve()
    output.parent.mkdir(parents=True, exist_ok=True)
    environment = dict(os.environ, APP_PORT=str(args.port))
    started = time.monotonic()
    utc = datetime.datetime.now(datetime.timezone.utc).isoformat()
    stages = {}
    with output.with_suffix('.log').open('w', encoding='utf-8') as log:
        def run(name, command):
            before = time.monotonic()
            subprocess.run(command, check=True, env=environment, stdout=log, stderr=subprocess.STDOUT)
            stages[name] = round(time.monotonic() - before, 3)
        run('cloneSeconds', ['git', 'clone', '--single-branch', '--branch', args.branch,
                            'https://github.com/josemanuelcondeacosta-lang/nexoentrega-360-sdd-ia.git', str(destination)])
        sha = subprocess.check_output(['git', '-C', str(destination), 'rev-parse', 'HEAD'], text=True).strip()
        compose = ['docker', 'compose', '-p', args.project, '--project-directory', str(destination)]
        run('buildSeconds', compose + ['build', 'app'])
        run('upSeconds', compose + ['up', '-d'])
        for attempt in range(90):
            try:
                with urllib.request.urlopen(f'http://127.0.0.1:{args.port}/ready', timeout=2) as response:
                    if response.status == 200:
                        break
            except (urllib.error.URLError, TimeoutError, ConnectionError, http.client.RemoteDisconnected):
                time.sleep(1)
        else:
            raise RuntimeError('La instancia no quedó lista; consultar el log y Docker.')
    total = round(time.monotonic() - started, 3)
    result = dict(sourceSha=sha, startedUtc=utc, totalSeconds=total, under15Minutes=total <= 900,
                  project=args.project, port=args.port, stages=stages, downloadsIncluded=True,
                  cachedDockerLayers=True, firstEverDownloadBenchmark=False,
                  limitation='Clon nuevo; imágenes/capas Docker previamente disponibles. No representa una máquina sin caché.')
    output.write_text(json.dumps(result, ensure_ascii=False, indent=2) + '\n', encoding='utf-8')
    print(json.dumps(result, ensure_ascii=False, indent=2))
    return 0 if total <= 900 else 1


if __name__ == '__main__':
    raise SystemExit(main())
