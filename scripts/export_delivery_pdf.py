"""Consolida un PDF PROVISIONAL, con SHA real y brechas; no aprueba la entrega."""
import argparse
from html import escape
from pathlib import Path
import subprocess
import textwrap

from reportlab.lib import colors
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.pdfbase import pdfmetrics
from reportlab.pdfbase.ttfonts import TTFont
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, PageBreak, Preformatted, Image

ROOT=Path(__file__).resolve().parents[1]

def main():
    parser=argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--sha',required=True)
    args=parser.parse_args()
    sha=subprocess.check_output(['git','rev-parse',args.sha],cwd=ROOT,text=True).strip()
    if len(sha)!=40: raise SystemExit('SHA inválido')
    font=Path('C:/Windows/Fonts/arial.ttf')
    if font.exists():
        pdfmetrics.registerFont(TTFont('Nexo',str(font)))
        pdfmetrics.registerFontFamily('Nexo',normal='Nexo',bold='Nexo',italic='Nexo',boldItalic='Nexo')
        face='Nexo'
    else: face='Helvetica'
    styles=getSampleStyleSheet()
    for style in styles.byName.values():style.fontName=face
    styles['BodyText'].fontSize=9;styles['BodyText'].leading=13
    styles.add(ParagraphStyle('CodeSmall',fontName='Courier',fontSize=6.3,leading=8,spaceAfter=5))
    output=ROOT/'deliverables/provisional/Examen_SDD_IA_Equipo_NexoEntrega360.pdf'
    output.parent.mkdir(parents=True,exist_ok=True)
    parts=[Paragraph('NexoEntrega 360',styles['Title']),Spacer(1,20),Paragraph('DOCUMENTO PROVISIONAL — NO ES ACEPTACIÓN FINAL',styles['Heading1']),
        Paragraph('Equipo: Cantillo Carolina · Castro Edier · Conde José · Vega José',styles['BodyText']),
        Paragraph('Fecha: 14 de octubre de 2026. Elaboración y pruebas asistidas por Codex.',styles['BodyText']),
        Paragraph('Versión de código: '+sha,styles['BodyText']),
        Paragraph('Repositorio: https://github.com/josemanuelcondeacosta-lang/nexoentrega-360-sdd-ia',styles['BodyText']),
        Paragraph('Tag de examen release-examen: NO CREADO. Revisiones G1/G2 y requisitos restantes visibles en el informe. Este PDF no transforma pruebas pendientes en aprobadas.',styles['BodyText']),PageBreak()]
    groups=[('Estado, resultados y ejecución',['docs/evidence/runtime/verification.md','docs/delivery/runbook-provisional.md','docs/delivery/participacion-verificada.md']),
        ('Especificaciones y cinco historias',[f'specs/SPEC-{n:02}.md' for n in range(1,6)]),
        ('Arquitectura, datos y contratos',[str(p.relative_to(ROOT)).replace('\\','/') for p in sorted((ROOT/'docs/adr').glob('ADR-*.md'))]+[str(p.relative_to(ROOT)).replace('\\','/') for p in sorted((ROOT/'docs/architecture').glob('*.md'))]+['contracts/README.md','contracts/openapi.yaml','contracts/ai-output.schema.json']),
        ('Uso de IA y revisión',[str(p.relative_to(ROOT)).replace('\\','/') for p in sorted((ROOT/'docs/evidence/ai-log').glob('AI-*.md'))]+['docs/reviews/coordinacion-cierre-G1-G2.md']),
        ('Trazabilidad, riesgos y aceptación',['docs/coverage-matrix.md','docs/evidence/runtime/spec-vs-code.csv','docs/risks/risk-register.md','docs/delivery/aceptacion-provisional.md','docs/delivery/checklist-entrega.md'])]
    for title,files in groups:
        parts.extend([Paragraph(title,styles['Heading1']),Spacer(1,10)])
        for filename in files:
            p=ROOT/filename
            if not p.exists():raise SystemExit('Falta artefacto: '+filename)
            parts.append(Paragraph(escape(filename),styles['Heading2']))
            code=False;block=[]
            for line in p.read_text(encoding='utf-8-sig').splitlines():
                if line.startswith('```'):
                    if code:
                        wrapped='\n'.join('\n'.join(textwrap.wrap(l,103,replace_whitespace=False,drop_whitespace=False)) if l else '' for l in block)
                        parts.append(Preformatted(wrapped,styles['CodeSmall']));block=[]
                    code=not code;continue
                if code:block.append(line);continue
                if not line.strip():parts.append(Spacer(1,4));continue
                heading=len(line)-len(line.lstrip('#'))
                style=styles['Heading3'] if heading else styles['BodyText']
                parts.append(Paragraph(escape(line.lstrip('# ').replace('**','').replace('`','')),style))
            if code and block: parts.append(Preformatted('\n'.join(block),styles['CodeSmall']))
            parts.append(PageBreak())
    for p in sorted((ROOT/'docs/evidence/runtime').glob('*.png')):
        parts.append(Paragraph('Captura: '+escape(p.name),styles['Heading2']))
        image=Image(str(p));ratio=min(480/image.imageWidth,650/image.imageHeight)
        image.drawWidth=image.imageWidth*ratio;image.drawHeight=image.imageHeight*ratio
        parts.extend([image,PageBreak()])
    def footer(canvas,doc):
        canvas.setFont(face,7);canvas.setFillColor(colors.HexColor('#53695b'))
        canvas.drawString(40,24,'NexoEntrega 360 · PROVISIONAL · '+sha[:12]);canvas.drawRightString(555,24,str(doc.page))
    SimpleDocTemplate(str(output),pagesize=A4,rightMargin=40,leftMargin=40,topMargin=42,bottomMargin=40).build(parts,onFirstPage=footer,onLaterPages=footer)
    print(output)

if __name__=='__main__': main()
