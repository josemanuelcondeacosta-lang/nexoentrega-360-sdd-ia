# Intervención de IA

Formato del anexo oficial `08_ai-log-template.md`.

- **ID y fecha UTC:** AI-008, 2026-10-14T18:31Z a 18:40Z (aprox.)
- **Autor:** Vega José (202321256198)
- **Etapa:** implementación de calidad y CI (cierre de G1/G2)
- **Herramienta y modelo conocido:** Claude (Anthropic), modelo Claude Opus 5.5, en claude.ai
- **RF y AC:** RNF04, RNF06; diferencia D-20 del cotejo; tarea TSK-09 de `docs/tasks.md`; checklist CI-02 y CI-11 de `vega-G1.md`
- **Contexto y versiones de archivos:** ramas `docs/g2-vega-ia` (`833e9b9`) y `docs/consolidated-review` (`4b75798`); scripts `export_specs.py`, `build_coverage.py`, `build_openapi.py`, `validate_openapi.py`; `contracts/requirements-docs.lock`

## Objetivo

Crear el primer workflow de GitHub Actions: comprobar en cada PR que las SPEC, sus JSON, la matriz, el contrato OpenAPI y los documentos oficiales son coherentes, y detectar secretos.

## Prompt enviado

> Soy Vega, ayudame con lo que me falta

## Salida relevante

- `.github/workflows/verificacion-documental.yml` con dos jobs:
  - **documentos:** `export_specs.py --check` (sin regenerar antes, como exige el anexo 18), `build_coverage.py --check`, pruebas del verificador, `sha256sum -c` de `docs/examen-oficial/`, y, si existen en la rama, instalación de `requirements-docs.lock`, `build_openapi.py --check` y `validate_openapi.py`.
  - **secretos:** gitleaks sobre todo el historial, con permisos de solo lectura y sin comentarios automáticos.
- Los pasos del contrato usan `hashFiles(...)` para que el workflow funcione también en ramas que todavía no tienen los scripts de José.

## Decisión humana

| Cambio | Decisión de Vega (aceptada / corregida / descartada) | Motivo | Fecha UTC |
|---|---|---|---|
| Workflow de CI documental | Aprobada | Revisé que el workflow comprueba las SPEC sin regenaralizarlas antes, la matriz, las pruebas, la integridad de los axesos, el contrato OpenAPI y los secretos con gitleaks | 2026-10-14 |

## Verificación

Ejecutada por el asistente en su espacio de trabajo el 2026-10-14:

- `actionlint` sobre el workflow: sin errores.
- Pasos del job «documentos» ejecutados en copias limpias con un entorno virtual nuevo:
  - `docs/g2-vega-ia`: SPEC y JSON coherentes, matriz coherente, 14 pruebas OK, integridad OK (pasos del contrato omitidos porque esa rama no tiene los scripts).
  - `docs/consolidated-review`: lo mismo, más `requirements-docs.lock` instalado, «Contrato integrado coherente con base y fragmento IA» y «OpenAPI 3.1.1 válido: 19 rutas, 21 operaciones».
- gitleaks 8.24.2 sobre el historial completo (30 commits, todas las ramas): «no leaks found».

Primera ejecución real en GitHub (2026-10-14): ambos jobs en verde, «SPEC, matriz, contrato e integridad» (12 s) y «Detección de secretos» (13 s, «No leaks detected»). Dos advertencias: checkout@v4, setup-python@v5 y gitleaks-action@v2 usan Node.js 20, obsoleto. Enlace: https://github.com/josemanuelcondeacosta-lang/nexoentrega-360-sdd-ia/pull/11/checks

## Límites

No hay pruebas de aplicación, base de datos ni E2E: se añadirán al workflow cuando exista el proyecto Laravel. La revisión de dependencias (`composer audit`, `npm audit`) queda pendiente hasta que existan lockfiles del producto. Sin datos reales ni secretos.
