# Intervención de IA

Formato del anexo oficial `08_ai-log-template.md`.

- **ID y fecha UTC:** AI-012, 2026-10-15T01:50Z a 02:10Z (aprox.)
- **Autor:** Vega José (josevegalago2006-cpu)
- **Etapa:** validación y operación (TSK-09 y TSK-12)
- **Herramienta y modelo conocido:** Claude (Anthropic), modelo Claude Opus 5.5, en claude.ai
- **RF y AC:** RNF01, RNF03, RNF06; AC-04-09; hallazgo R-E03 de la revisión de Edier al PR #11
- **Contexto y versiones de archivos:** rama `feat/ia-vega` (`5dce221`, PR #14); `.github/workflows/verificacion-documental.yml` de la rama `ci/vega-verificacion-documental` (`435651b`, PR #11); `QueryController.php` (listados y tablero); canal de logs `nexo`

## Objetivo

Preparar la prueba de carga RNF03, integrar el CI documental en la rama de la aplicación y comprobar que los logs no contienen secretos.

## Prompt enviado

> Vamos a hacer lo mio

(Después de pedir «aparte de la revisiones, que mas queda por hacer?».)

## Salida relevante

- `php artisan nexo:seed-carga` (`app/Modules/Operations/LoadSeed.php`): 2.000 solicitudes ficticias en BORRADOR y ENVIADA con su evento SEND; idempotente; no toca inventario.
- `tests/load/rnf03.js`: k6 con 5 s de calentamiento y 60 s medidos, 20 usuarios virtuales, listados y tablero; umbrales por endpoint p95 ≤ 700 ms y errores < 1 % solo en la fase medida; resumen en `docs/evidence/runtime/carga-rnf03.json`.
- Servicio `k6` en `compose.yaml` (perfil `carga`, imagen `grafana/k6:1.8.2`) y método en `docs/evidence/runtime/carga-rnf03.md`.
- `.github/workflows/verificacion-documental.yml` integrado en la rama de la app (R-E03): SPEC, matriz, integridad `sha256sum`, contrato y gitleaks sobre todo el historial.
- `tests/Feature/LogsSanitizedTest.php`: después de iniciar sesión, enviar observaciones con instrucciones y llamar al proveedor con una clave de prueba, los logs no contienen contraseñas, la clave, la cookie de sesión ni el texto de las observaciones, y sí el correlationId.
- Corrección encontrada al probar: k6 borra las cookies en cada iteración por defecto y las consultas daban 401; se añadió `noCookiesReset: true`.

## Decisión humana

| Cambio | Decisión de Vega (aceptada / corregida / descartada) | Motivo | Fecha UTC |
|---|---|---|---|
| Prueba de carga RNF03 (seed, script k6 y servicio) | PENDIENTE | | |
| CI documental integrado y prueba de logs | PENDIENTE | | |

## Verificación

Ejecutada por el asistente en su espacio de trabajo el 2026-10-15 (UTC), con PHP 8.3 y PostgreSQL 16 locales y el servidor de PHP con 4 workers (el proyecto usa PHP 8.5 y PostgreSQL 17.6 en Docker):

- `nexo:seed-carga`: 2.000 solicitudes (ORG-A 1.080 BORRADOR y 720 ENVIADA; ORG-B 120 y 80), 800 eventos SEND; una segunda ejecución no repite.
- k6 1.8.2, ejecución completa de 65 s: listado p95 197,8 ms y mediana 80,4 ms; tablero p95 202,5 ms y mediana 69,1 ms; errores 0,00 %; umbrales cumplidos.
- `LogsSanitizedTest`: correcta (15 aserciones). `actionlint` sobre los dos workflows: sin errores.

La medición válida para la entrega es la de Vega en su equipo, con su hardware reportado, y queda en `carga-rnf03.json`.

## Límites

Mide la API con el servidor de desarrollo de PHP, no un despliegue de producción. La pausa de 1 s por iteración representa el tiempo de lectura de un usuario; el requisito no fija la tasa de peticiones. Sin datos reales ni secretos.
