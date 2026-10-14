# Correcciones técnicas y evidencia documental

Elaboración: Codex a solicitud de Conde José, 2026-10-14, rama `docs/consolidated-review`. No se registra revisión o aprobación humana en nombre de Carolina.

| Hallazgo | Cambio preparado | Límite |
|---|---|---|
| R-V01: CSRF IA ausente | Copia exacta de `openapi-ia.yaml` v0.2.0 de Vega, `833e9b99ef97d2be23b49a8a1778f54265c99a0a`; integrado sin alterar sus rutas/componentes | Revisión independiente y middleware real pendientes |
| R-V02: historial 404 para mismo tenant | Contrato y AC-04-11 de SPEC-04 0.3.2-draft especifican 403 para rol/dueño prohibido dentro de organización, 404 para inexistente/interorganización | Nueva versión requiere revisión; no aserción HTTP ejecutada |
| R-V03: estrategia antigua | Copia actualizada de estrategia de Vega preservada desde consolidación | No prueba de concurrencia de producto ejecutada |
| H-E05: descripción de idempotencia en otras transiciones | Frase «Misma reserva con otra clave devuelve 409» solo aparece en reserve, no send/dispatch/deliver/cancel | Verificar transiciones e idempotencia reales al implementar |
| RC-03/04/05: plan/datos/tareas | Nombres canónicos, 14 tareas previstas, cobertura y dependencias; autoría diferenciada de responsabilidades | Propuesta de organización, no trabajo humano acreditado |

## Ejecución real

Windows/PowerShell, Python 3.11.9, dependencias `.venv-docs` fijadas en `contracts/requirements-docs.lock`. Comandos ejecutados sobre el árbol que introduce este informe:

```text
python scripts/export_specs.py --check
5 SPEC y 68 AC validados; JSON coherentes
python scripts/build_coverage.py --check
Matriz y traza coherentes con las SPEC
python -m unittest discover -s scripts -p 'test_*.py'
Ran 14 tests in 0.949s — OK
.venv-docs/Scripts/python.exe scripts/build_openapi.py --check
Contrato integrado coherente con base y fragmento IA
.venv-docs/Scripts/python.exe scripts/validate_openapi.py
OpenAPI 3.1.1 válido: 19 rutas, 21 operaciones; IA preservada;
2 ejemplos válidos y 3 casos negativos rechazados
CSRF en todas las escrituras y regresión por omisión detectada;
política de auditoría 401/403/404 coherente
git diff --check
Sin errores (avisos LF/CRLF del entorno)
```

Regresión documental R-V01: se crea copia en memoria que elimina el header CSRF de generar resumen; OpenAPI todavía la acepta estructuralmente, pero el control adicional la rechaza. Esto cubre el defecto documental que antes pasaba el validador genérico. No se escribe esa copia insegura a disco.

Control R-V02 comprueba política de error legible por máquina para ambas rutas de historial; esperado/observado real de API se verificará con tests futuros PostgreSQL y usuarios distintos de la misma organización y de ORG-B. JSON Schema no prueba autorización.

G1/G2 siguen pendientes de revisión de versiones cambiadas y otros hallazgos. El SHA candidato es el commit que incorpora estas correcciones, disponible en PR #10; no se inventa su aprobación. No hay pruebas de aplicación, PostgreSQL, IA real, UI, carga o CI remoto declaradas aprobadas.
