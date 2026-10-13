"""Pruebas del verificador documental; no son pruebas de producto."""
import shutil
import tempfile
import unittest
from pathlib import Path

from export_specs import ROOT, export


class ExportSpecsTests(unittest.TestCase):
    def setUp(self):
        self.temp = tempfile.TemporaryDirectory()
        self.addCleanup(self.temp.cleanup)
        self.root = Path(self.temp.name)
        shutil.copytree(ROOT / 'specs', self.root / 'specs')

    def alter(self, old, new):
        path = self.root / 'specs' / 'SPEC-01.md'
        path.write_text(path.read_text(encoding='utf-8').replace(old, new, 1), encoding='utf-8')

    def test_roundtrip(self):
        count, criteria = export(self.root)
        self.assertEqual(count, 5)
        self.assertGreater(criteria, 21)
        self.assertEqual(export(self.root, check=True), (count, criteria))

    def test_stale_json(self):
        export(self.root)
        path = self.root / 'specs' / 'generated' / 'SPEC-01.json'
        path.write_text('{}', encoding='utf-8')
        with self.assertRaisesRegex(ValueError, 'no coincide'):
            export(self.root, check=True)

    def test_missing_spec(self):
        (self.root / 'specs' / 'SPEC-05.md').unlink()
        with self.assertRaisesRegex(ValueError, 'exactamente'):
            export(self.root)

    def test_duplicate_block(self):
        path = self.root / 'specs' / 'SPEC-01.md'
        with path.open('a', encoding='utf-8') as stream:
            stream.write('\n```spec-json\n{}\n```\n')
        with self.assertRaisesRegex(ValueError, 'exactamente un bloque'):
            export(self.root)

    def test_duplicate_json_key(self):
        self.alter('"id": "SPEC-01",', '"id": "SPEC-01", "id": "SPEC-01",')
        with self.assertRaisesRegex(ValueError, 'duplicada'):
            export(self.root)

    def test_wrong_id(self):
        self.alter('"id": "SPEC-01"', '"id": "SPEC-99"')
        with self.assertRaisesRegex(ValueError, 'ID no coincide'):
            export(self.root)

    def test_markdown_json_divergence(self):
        self.alter('- **AC-01-01:** Sin credenciales', '- **AC-01-01:** Con credenciales')
        with self.assertRaisesRegex(ValueError, 'divergen'):
            export(self.root)

    def test_check_does_not_create_output_directory(self):
        shutil.rmtree(self.root / 'specs' / 'generated')
        with self.assertRaisesRegex(ValueError, 'no coincide'):
            export(self.root, check=True)
        self.assertFalse((self.root / 'specs' / 'generated').exists())

    def test_failed_validation_does_not_partially_export(self):
        export(self.root)
        target = self.root / 'specs' / 'generated' / 'SPEC-01.json'
        original = target.read_bytes()
        path = self.root / 'specs' / 'SPEC-05.md'
        path.write_text(path.read_text(encoding='utf-8').replace('"inputs": [', '"inputs": null, "oldInputs": ['), encoding='utf-8')
        with self.assertRaisesRegex(ValueError, 'inputs incompleto'):
            export(self.root)
        self.assertEqual(target.read_bytes(), original)


if __name__ == '__main__':
    unittest.main()
