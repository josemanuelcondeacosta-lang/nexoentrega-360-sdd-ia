# Intervención de IA

Formato del anexo oficial `08_ai-log-template.md`.

- **ID y fecha UTC:** AI-010, 2026-10-14T22:15Z a 2026-10-15T00:20Z (aprox.)
- **Autor:** Castro Edier (eblanquiceth13-cmyk)
- **Etapa:** revisión crítica (G1/G2, PR #10, #11 y #12) e implementación de pruebas
- **Herramienta y modelo conocido:** Claude (Anthropic), modelo Claude Opus 5.5, en claude.ai
- **RF y AC:** RF04–RF07, RF09, RNF02; AC-03-03, AC-03-09, AC-03-10, AC-03-11, AC-03-12 (complemento), AC-03-17
- **Contexto y versiones de archivos:** PR #10 (`0886219`: SPEC-03 0.4, ADR-001, contrato); PR #11 (`435651b`: CI documental de Vega); PR #12 (`8ff8018`: aplicación provisional de José, `TransitionController.php`, migración de dominio, `NexoFlowTest.php`, `tests/integration/concurrency.mjs`); ADR-002 propio

## Objetivo

Guiar las revisiones de Edier a los PR #10, #11 y #12 y escribir las pruebas que faltaban según su hallazgo R-E04 y R-E05 al PR #12.

## Prompt enviado

> soy edier, que mas me falta terminar de eso que han hecho?

Continuación en la misma sesión: «ayudame con lo mio, empecemos».

## Salida relevante

- **Revisiones:** comprobaciones con comandos `git show` y plantillas de comentario para el PR #10 (H-E01, H-E02, H-E05 y ADR-001), el PR #11 (workflow y ejecución) y el PR #12 (orden de autorización, bloqueos, idempotencia, restricciones de base y pruebas).
- **Correcciones señaladas a Edier:** en el PR #10 respondió «todo está perfecto», pero el ADR-001 no fija las versiones de PHP, Laravel y PostgreSQL (hallazgo R-E02). En el PR #11 y el PR #12 dio respuestas generales; el asistente le mostró el resultado de cada punto y le pidió confirmarlo en sus archivos antes de publicar.
- **Pruebas nuevas** (rama `test/edier-transacciones`, base `feat/provisional-frontend-integration`):
  - `apps/nexo/tests/Feature/TransactionIntegrityTest.php`:
    - AC-03-09: un trigger temporal en la base de pruebas hace fallar la auditoría; se espera 500 con correlationId sin detalles internos, solicitud ENVIADA, stock 10/0/10, sin evento ni registro idempotente; al quitar el trigger, la misma clave reserva (10/7/3).
    - Retención de 24 h con reloj controlado: a las 23 h 59 min repite código y cuerpo; a las 24 h exactas deja de repetir y responde 409 INVALID_STATE sin segunda reserva.
    - Misma clave textual de otro usuario de ORG-B: 404 y sin registro propio.
  - `tests/integration/races.mjs` (HTTP real, varios workers):
    - R-E05 / AC-03-03: dos reservas que compiten por KIT-ALIMENTO de ORG-B, 20 rondas, cada una con un 200 y un 409, stock 4/3/1 y un solo evento.
    - AC-03-17: despacho de Bodega y cancelación del Solicitante a la vez; ronda del oráculo (10/7 → 3/0/3 o 10/0/10) y 19 rondas más con KIT-AGUA; un 200 y un 409, stock coherente y un solo evento exitoso.
  - `.github/workflows/aplicacion-provisional.yml`: `races.mjs` se ejecuta después de `concurrency.mjs` y antes de los E2E.

## Decisión humana

| Cambio | Decisión de Edier (aceptada / corregida / descartada) | Motivo | Fecha UTC |
|---|---|---|---|
| Revisión del PR #10 (comentario `#issuecomment-6180730745`) | Aceptada | Comprobé H-E01, H-E02 y H-E05 en el diff; añadí R-E02 por las versiones sin fijar del ADR-001 | 2026-10-14T23:23Z |
| Revisión del PR #11 | Aceptada | Revisé el workflow y la ejecución número 3 en verde; añadí O-1 y R-E03 | 2026-10-14T23:48Z |
| Revisión del PR #12 (comentario `#issuecomment-6181132070`) | Aceptada | Comprobé orden, bloqueos, idempotencia y restricciones contra mi ADR-002; añadí R-E04 y R-E05 | 2026-10-14T23:56Z |
| Pruebas `TransactionIntegrityTest.php` y `races.mjs`, y paso del CI | Aceptada | Las ejecuté en mi PC con Docker y pasaron; cubren R-E04 y R-E05 | 2026-10-15T00:27Z |

## Verificación

Ejecutada por el asistente en su espacio de trabajo el 2026-10-15 (UTC), sobre `8ff8018` más estos cambios. Entorno distinto al del proyecto: PHP 8.3.6 con `composer install --ignore-platform-req=php` (el lockfile pide PHP ≥ 8.4.1) y PostgreSQL 16.15 local, en lugar de PHP 8.5 y PostgreSQL 17.6 en Docker.

- `php artisan test` sobre `nexo_test`: 11 pruebas, 110 aserciones correctas (8 existentes y 3 nuevas).
- Pruebas de mutación: con `gt` cambiado a `gte` en la expiración, la prueba de 24 h falla (200 en lugar de 409); con la auditoría movida fuera de la transacción, la prueba de AC-03-09 falla. Código restaurado después.
- Servidor con `PHP_CLI_SERVER_WORKERS=4` sobre base recién sembrada: `concurrency.mjs` correcto y `races.mjs` correcto en 20 rondas por carrera. Ganaron ambas peticiones en distintas rondas (reservas 13/7; despacho 11 y cancelación 9), lo que muestra intercalado real.
- `vendor/bin/pint --test` sobre la prueba nueva: correcto. `actionlint` sobre el workflow: sin errores.

Ejecución de Edier, 2026-10-15T00:27Z (aprox.), Windows con Docker Desktop (PHP 8.5.0, PostgreSQL 17.6): `php artisan test` sobre `nexo_test` → 11 pruebas, 110 aserciones correctas; `node tests/integration/races.mjs` → passed, 20 rondas por carrera; reservas ganadas 10 y 10; despacho 11 y cancelación 9; en la ronda del oráculo ganó la cancelación (10/0/10).

La ejecución válida para el proyecto es la de Edier con Docker y la del CI de GitHub sobre el SHA del PR; deben registrarse con sus resultados reales.

## Límites

Las carreras usan peticiones simultáneas (`Promise.all`) y no una barrera dentro del servidor; el orden lo decide el sistema. `races.mjs` exige una base recién sembrada y consume existencias de KIT-AGUA de ORG-A. No cubre carga ni la prueba de restauración de respaldo. `composer.json` declara `php ^8.2`, pero el lockfile requiere PHP ≥ 8.4.1 (relacionado con R-E02). Sin datos reales ni secretos.

## Continuación (2026-10-15, misma sesión)

Prompts: «soy edier, que debo hacer?». Salida: comprobaciones y plantilla para revisar el PR #14 de Vega, y procedimiento de respaldo y restauración con comparación md5 por tabla (`docs/evidence/runtime/respaldo-restauracion.md`).

| Cambio | Decisión de Edier | Motivo | Fecha UTC |
|---|---|---|---|
| Revisión del PR #14 (aprobado con O-E1) | Aceptada | Comprobé tablas que escribe la IA, orden 403/422/404/409, captura sin clave y CI en verde | 2026-10-15T02:35Z |
| Prueba de respaldo y restauración | Aceptada | La ejecuté en mi PC: 6 tablas idénticas (filas y md5), restricción y trigger restaurados, 4 migraciones Ran | 2026-10-15T02:42Z |
