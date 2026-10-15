"""Vincula los 73 AC a evidencia real, conservando los límites de verificación."""
import csv
import json
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
SHA = '6a751d477e2e10939f0081050fb9bb996ede3d05'
BASE = 'docs/evidence/runtime/candidate/'
MAPPINGS = {
    'SPEC-01': ('Identity', 'apps/nexo/tests/Feature/NexoFlowTest.php', 'phpunit.xml', 'Falta completar todas las variantes de identidad, revocación y snapshots del AC.'),
    'SPEC-02': ('Requests', 'apps/nexo/tests/Feature/NexoFlowTest.php', 'phpunit.xml', 'Faltan límites completos, edición y combinaciones negativas de cada AC.'),
    'SPEC-03': ('Inventory', 'apps/nexo/tests/Feature/TransactionIntegrityTest.php; apps/nexo/tests/Feature/NexoFlowTest.php', 'phpunit.xml', 'Revisar todas las variantes por rol, estado, clave y organización; suite parcial respecto al AC completo.'),
    'SPEC-04': ('Queries', 'apps/nexo/tests/Feature/NexoFlowTest.php', 'phpunit.xml', 'Faltan oráculos independientes de filtros/conteos y variantes completas del AC.'),
    'SPEC-05': ('AI', 'apps/nexo/tests/Feature/AiAssistantTest.php', 'phpunit.xml', 'La evidencia automatizada debe cotejarse con cada cláusula; aceptación humana pendiente.'),
}
MANUAL = {'AC-01-13', 'AC-02-11', 'AC-03-16', 'AC-04-14'}


def main():
    target = ROOT / BASE / 'ac-evidence.csv'
    with target.open('w', encoding='utf-8-sig', newline='') as stream:
        writer = csv.writer(stream)
        writer.writerow(['AC', 'Requirements', 'SpecVersion', 'CodePath', 'Tests', 'Evidence', 'SourceSha', 'Status', 'RemainingVerification'])
        for source in sorted((ROOT / 'specs/generated').glob('SPEC-*.json')):
            spec = json.loads(source.read_text(encoding='utf-8'))
            module, tests, report, remaining = MAPPINGS[spec['id']]
            for ac in spec['acceptanceCriteria']:
                ac_id = ac['id']
                ac_tests, ac_report, gap = tests, report, remaining
                status = 'PARTIAL_EVIDENCE_NOT_ACCEPTED'
                if ac_id in MANUAL:
                    ac_tests, ac_report = 'tests/e2e/flow.spec.js', 'playwright.json'
                    gap = 'Automatización a 390/1366 px ejecutada; inspección manual y decisión personal pendientes.'
                elif ac_id in {'AC-03-03', 'AC-03-08', 'AC-03-17'}:
                    ac_tests, ac_report = 'tests/integration/races.mjs', 'races.json'
                    gap = '20 rondas ejecutadas por carrera; no demuestra todos los intercalados posibles ni aceptación humana.'
                elif ac_id == 'AC-04-09':
                    ac_tests, ac_report = 'tests/load/rnf03.js', 'carga-rnf03.json'
                    gap = 'Entorno local Docker compartido; revisar hardware y condiciones en verification.md.'
                elif ac_id == 'AC-04-12':
                    ac_tests, ac_report = 'scripts/verify_clean_clone.py', 'clean-clone.json'
                    gap = 'Clon remoto nuevo con capas Docker disponibles; no se midió una máquina inicialmente sin caché.'
                elif ac_id == 'AC-04-13':
                    status = 'PENDING_COMPLETE_VERIFICATION'
                    gap = 'Arranque/readiness disponible comprobados; falta prueba de caída de base y health/readiness 200/503.'
                elif ac_id == 'AC-01-10':
                    ac_tests = 'apps/nexo/tests/Feature/LogsSanitizedTest.php'
                elif ac_id == 'AC-05-17':
                    gap = 'El backend acepta el texto válido; falta comprobar window.__unsafe en navegador con esa salida.'
                if not (ROOT / BASE / ac_report).exists():
                    raise FileNotFoundError(ac_report)
                writer.writerow([ac_id, ';'.join(ac['requirements']), spec['version'],
                                 'apps/nexo/app/Modules/' + module, ac_tests, BASE + ac_report, SHA, status, gap])
    print('73 AC enlazados sin atribuir aceptación humana: ' + str(target))


if __name__ == '__main__':
    main()
