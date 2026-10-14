# Contratos G2 propuestos

`openapi.yaml` une rutas base y `openapi-ia.yaml` v0.2.0, conservando exactamente rutas y componentes IA del commit `833e9b99ef97d2be23b49a8a1778f54265c99a0a` de `docs/g2-vega-ia` (corrección CSRF de Vega). `ai-output.schema.json` describe salida del modelo; no sustituye el envoltorio de respuesta HTTP. No hay implementación ni revisión G2 global aprobada.

Base usa nombres de campos españoles de SPEC 0.3 (NORMAL/ALTA y fecha RFC3339), IDs string y prefijo `/api`. Generación y validación:

```powershell
python -m venv .venv-docs
.\.venv-docs\Scripts\python.exe -m pip install -r contracts/requirements-docs.lock
.\.venv-docs\Scripts\python.exe scripts/build_openapi.py
.\.venv-docs\Scripts\python.exe scripts/build_openapi.py --check
.\.venv-docs\Scripts\python.exe scripts/validate_openapi.py
```

Las versiones son herramientas documentales, no dependencias del producto Laravel. Para cambiar base, editar `scripts/build_openapi.py`; para cambiar IA, revisar su fragmento y regenerar. No permitir colisiones silenciosas.

## Pendientes antes de implementar

- ADR-002: política final de errores idempotentes (propuesta 200/409 almacenados; no 401/403/404/422/500), TTL/conservación y transacción real.
- ADR-003 propone cookie revocable y Bcrypt costo 12; revisión humana del ADR pendiente. Base y fragmento IA ya declaran X-XSRF-TOKEN obligatorio y 419 en todas las escrituras. Bootstrap `/api/auth/csrf` y precedencia CSRF/autenticación siguen diseño, no middleware implementado. 419 es propuesta Laravel, no decisión del docente.
- CL-16/17/18: el coordinador reporta respuesta afirmativa de la profesora. Inventario: Coordinador/Bodega/Auditor, solo su organización. Auditoría: Auditor/Coordinador de su organización; Solicitante solo sus solicitudes; Bodega sin acceso. Tablero Solicitante propio, sin inventario. Fecha/canal y mensaje original pendientes; renovar revisión del contrato/SPEC modificados.
- Validar fecha al enviar, SKU activo y distinto, pertenencia, unicidad de IDs IA y relaciones físico/reservado no se resuelve solo con JSON Schema: controles semánticos de servidor pendientes.
- Auditoría solo lectura por API; errores sanitizados y correlationId. Liveness y readiness públicas sin secretos; readiness no depende de IA.

Este documento usa [OpenAPI 3.1.1](https://spec.openapis.org/oas/v3.1.1.html). La validación del contrato no demuestra que esas rutas existan ni cumplan los RF.

Regresiones documentales: el validador detecta una omisión artificial de CSRF en una escritura IA aun si OpenAPI sigue válido, y comprueba 401/403/404 del historial. No sustituye pruebas HTTP reales.
