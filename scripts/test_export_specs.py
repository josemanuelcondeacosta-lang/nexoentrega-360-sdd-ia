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

    def alter(self, old, new, spec='SPEC-01'):
        path = self.root / 'specs' / f'{spec}.md'
        content = path.read_text(encoding='utf-8')
        self.assertIn(old, content, 'el texto a sustituir debe existir en la fuente')
        path.write_text(content.replace(old, new, 1), encoding='utf-8')

    def test_roundtrip(self):
        count, criteria = export(self.root)
        self.assertEqual(count, 5)
        self.assertGreaterEqual(criteria, 57)
        self.assertEqual(export(self.root, check=True), (count, criteria))

    def test_stale_json(self):
        export(self.root)
        (self.root / 'specs' / 'generated' / 'SPEC-01.json').write_text('{}', encoding='utf-8')
        with self.assertRaisesRegex(ValueError, 'no coincide'):
            export(self.root, check=True)

    def test_missing_spec(self):
        (self.root / 'specs' / 'SPEC-05.md').unlink()
        with self.assertRaisesRegex(ValueError, 'exactamente'):
            export(self.root)

    def test_duplicate_block(self):
        with (self.root / 'specs' / 'SPEC-01.md').open('a', encoding='utf-8') as stream:
            stream.write('\n<!-- spec-data:start -->\n```json\n{}\n```\n<!-- spec-data:end -->\n')
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

    def test_ac_references_requirement_outside_spec(self):
        self.alter('"requirements": ["RF01", "RNF01"],\n      "kind": "negativo"',
                   '"requirements": ["RF03"],\n      "kind": "negativo"')
        with self.assertRaisesRegex(ValueError, 'ausentes de la SPEC'):
            export(self.root)

    def test_requirement_without_ac(self):
        self.alter('"requirements": ["RNF05"],\n      "kind": "positivo"',
                   '"requirements": ["RF01"],\n      "kind": "positivo"')
        with self.assertRaisesRegex(ValueError, 'requisitos sin AC'):
            export(self.root)

    def test_invalid_kind(self):
        self.alter('"kind": "negativo"', '"kind": "feliz"')
        with self.assertRaisesRegex(ValueError, 'kind'):
            export(self.root)

    def test_untyped_input(self):
        self.alter('"type": "string; correo ficticio', '"tipo": "string; correo ficticio')
        with self.assertRaisesRegex(ValueError, r'inputs\[1\]\.type'):
            export(self.root)

    def test_template_placeholder_rejected(self):
        self.alter('"want": "iniciar y cerrar sesión', '"want": "[acción] iniciar y cerrar sesión')
        with self.assertRaisesRegex(ValueError, 'marcador'):
            export(self.root)

    def test_ac_sequence(self):
        self.alter('"id": "AC-01-02"', '"id": "AC-01-03"')
        with self.assertRaisesRegex(ValueError, 'esperado AC-01-02'):
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
        self.alter('"inputs": [', '"inputs": null, "oldInputs": [', spec='SPEC-05')
        with self.assertRaisesRegex(ValueError, 'inputs'):
            export(self.root)
        self.assertEqual(target.read_bytes(), original)


if __name__ == '__main__':
    unittest.main()
