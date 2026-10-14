# Evidencia documental del paquete ADR-001 y contrato base

2026-10-14; Windows/PowerShell, Python 3.11.9, entorno `.venv-docs`, dependencias fijadas en `contracts/requirements-docs.lock`. Rama `docs/g2-architecture`; fuente IA `a13aa17` de `docs/g2-vega-ia`. SHA de esta ejecución: árbol de trabajo que introduce este archivo; el commit candidato se obtiene al versionarlo, no se inventa un SHA probado de aplicación.

Se leyó PDF oficial §§2–5/8/9/11 con pypdf 5.9.0 y anexos de constitution/ADR. Se conservaron SPEC 0.3-draft, aclaraciones y contrato IA de la rama del equipo.

Comandos efectivamente ejecutados:

```powershell
.\.venv-docs\Scripts\python.exe scripts/build_openapi.py
.\.venv-docs\Scripts\python.exe scripts/build_openapi.py --check
.\.venv-docs\Scripts\python.exe scripts/validate_openapi.py
```

Resultado observado: contrato generado; coherente con generador y fragmento IA; OpenAPI 3.1.1 válido, 19 rutas y 21 operaciones, 2 ejemplos IA válidos y 3 casos negativos rechazados (prioridad MEDIA, organizationId extra y cantidad 0). Verificación de rutas/componentes IA preservados, operationId únicos, rutas de transición, seguridad y ausencia de escrituras de auditoría por contrato.

Los casos son validaciones del documento, no llamadas HTTP. La unicidad por SKU, fecha contra reloj, permisos efectivos, transacciones, concurrencia y CSRF requieren futura implementación/pruebas reales. No hay prueba de aplicación, PostgreSQL, carga o CI declarada aprobada. Mermaid se entrega como fuente; render no verificado automáticamente. G1/G2 siguen sin aprobación.
