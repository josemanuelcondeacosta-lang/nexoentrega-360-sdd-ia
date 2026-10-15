# Verificación de la candidata de integración

Código probado: `6a751d477e2e10939f0081050fb9bb996ede3d05`, rama `integration/examen-candidate`. Ejecuciones del 2026-10-15 UTC (2026-10-14 en Colombia). Preparación y ejecución asistidas por Codex por instrucción del coordinador; no se atribuye revisión humana nueva.

GitHub ejecutó correctamente la [CI de aplicación de este SHA](https://github.com/josemanuelcondeacosta-lang/nexoentrega-360-sdd-ia/actions/runs/38673257765). El snapshot consultado y las decisiones anteriores se conservan en `docs/evidence/github/candidate-snapshot.json`. El [PR #16](https://github.com/josemanuelcondeacosta-lang/nexoentrega-360-sdd-ia/pull/16) está en borrador; los PR #1 y #12–15 permanecen conservados.

## Resultados ejecutados

| Comprobación | Resultado observado | Evidencia |
|---|---|---|
| PHP con PostgreSQL 17.6 | 49 pruebas, 518 aserciones, cero fallos/omisiones; 10,76s | `phpunit.xml` |
| Interfaz Chromium, 1366×900 y 390×844 | 4 recorridos correctos, 27,2s; entrega, recorrido de auditoría, teclado y rechazo sin efectos | `playwright.json` |
| Reserva simultánea inicial | 200/409, un evento, reservado 3 de físico 4; cancelar libera exactamente 3 | `concurrency.json` |
| Carreras de reserva | 20 rondas; ganadores primero/segundo: 11/9; stock 4/3/1 en cada ronda | `races.json` |
| Despacho contra cancelación | 20 rondas; despacho gana 11, cancelación 9; un solo evento exitoso por ronda | `races.json` |
| Carga k6 1.8.2 | 20 VU, 5s calentamiento y 60s medidos; 2.000 solicitudes de carga; p95 listado 278,3ms y tablero 215,2ms, errores 0% | `carga-rnf03.json`, `environment.json` |
| Backup y restauración | `pg_dump -Fc` y `pg_restore --no-owner --exit-on-error`; 17 tablas iguales en número y digest del contenido completo | `restore.json` |
| Clon remoto nuevo | 21,36s hasta `/ready` 200, incluidas construcción y arranque; capas Docker disponibles previamente | `clean-clone.json`, `clean-clone.log` |
| Documentación | 5 SPEC/73 AC coherentes; 14 pruebas Python correctas | Comandos reproducibles abajo |
| OpenAPI | 20 rutas, 22 operaciones; CSRF en escrituras y política 401/403/404 validadas | Comandos reproducibles abajo |
| Formato/dependencias | Pint correcto en 55 archivos; Composer audit sin avisos | Comandos reproducibles abajo |

Entorno: PHP 8.5.0, Laravel 12.70.0 y PostgreSQL 17.6 en Docker. Consultar hardware exacto y carga concurrente del host en `environment.json`. La medición de carga ocurrió mientras había otras instancias Docker y trabajo de construcción; no representa infraestructura de producción dedicada.

## Aislamiento y secuencia

Demo previa: puerto 8000 y volumen original conservados. Evaluación: proyecto Docker `nexo-candidate`, puerto 8001, volumen independiente; suite PHP en `nexo_test`. La variable `APP_ENV=testing` se pasó explícitamente porque el contenedor de demo define local. Las primeras ejecuciones con entorno local fallaron con 419; no se contabilizan como éxito. Se copiaron anexos/esquema al contenedor para comprobar igualdad sin omitir esa prueba. Sus finales de línea se igualaron a LF, sin cambios de contenido.

La prueba de interfaz se hizo con seed pequeño fresco. Se respaldó esa base y se restauró en `nexo_restore_candidate`; el informe conserva comparación de las 17 tablas y SHA-256 del dump. Después se reinicializó únicamente la base candidata para las carreras, y luego se añadieron las 2.000 solicitudes de carga. No se ejecutó `migrate:fresh` contra la demo original. El dump, que contiene hashes de las cuentas ficticias y sesiones de prueba, permanece fuera de Git en `test-results/candidate.dump`.

El ensayo de clon limpio usa un destino nuevo y otro proyecto Docker. Incluye cualquier descarga realizada durante esa ejecución, pero **no mide una máquina inicialmente sin caché ni separa su primera descarga**. Esa variante sigue pendiente para afirmar el AC completo. Los intentos previos abortados no se presentan como mediciones exitosas.

## IA y defectos corregidos

El replay anterior podía devolver recomendaciones de una captura con IDs coincidentes pero otras prioridades, observaciones, fechas o selección. Se exige ahora coincidencia de instrucciones y los seis campos de contexto; el orden de selección puede variar y las fechas equivalentes se normalizan a UTC. Ante diferencia se registra `replay_contexto_distinto` y se devuelve fallback. Cinco casos nuevos cubren diferencias y coincidencia reordenada.

Se conservan la captura original publicada por Vega y su salida sin modificaciones. Su latencia original de 18.915ms supera los 5s del producto: se utiliza como reproducción declarada, nunca como prueba de llamada en vivo dentro del plazo. El timeout de 5s se probó mediante un servidor TCP local sin respuesta. No se utilizó una clave ni se realizó una llamada nueva al proveedor externo. Por defecto `AI_PROVIDER=none` mantiene el fallback.

También se corrigieron identificadores no UUID a 404 antes de consultar PostgreSQL y la restricción de Composer a PHP 8.5, conservando versiones de paquetes. AI-013 conserva el registro frontend y permite importar AI-007/008 de Vega sin colisión.

## Comandos reproducibles

Desde la raíz, PowerShell:

```powershell
$env:APP_PORT='8001'
docker compose -p nexo-candidate build app
docker compose -p nexo-candidate up -d
# Crear nexo_test una sola vez en la base de evaluación:
docker compose -p nexo-candidate exec -T database createdb -U nexo nexo_test
docker compose -p nexo-candidate cp docs app:/docs
docker compose -p nexo-candidate cp contracts app:/contracts
docker compose -p nexo-candidate exec -T -e APP_ENV=testing -e DB_DATABASE=nexo_test app php artisan test
docker compose -p nexo-candidate exec -T app php vendor/bin/pint --test
$env:E2E_BASE_URL='http://127.0.0.1:8001'
npm ci
npx playwright install chromium
npm run check:frontend
npm run test:e2e
```

Las carreras requieren seed pequeño fresco y consumen stock; ejecutarlas en otra base de evaluación, no sobre datos personales ni inmediatamente después de E2E sin resembrar. Con la aplicación candidata detenida, `migrate:fresh --seed` solo está autorizado en esa base desechable, nunca en la demo original. El script no decide por sí mismo qué base puede borrarse.

```powershell
node tests/integration/concurrency.mjs
node tests/integration/races.mjs
docker compose -p nexo-candidate exec -T app php artisan nexo:seed-carga
docker compose -p nexo-candidate --profile carga run --rm -e RESULTS_DIR=/results/candidate k6
.venv-docs/Scripts/python.exe scripts/export_specs.py --check
.venv-docs/Scripts/python.exe scripts/build_coverage.py --check
.venv-docs/Scripts/python.exe -m unittest discover -s scripts -p 'test_*.py'
.venv-docs/Scripts/python.exe scripts/build_openapi.py --check
.venv-docs/Scripts/python.exe scripts/validate_openapi.py
```

## Pendientes verificables

La matriz `ac-evidence.csv` enlaza los 73 AC con código, pruebas y reportes, indicando brechas por criterio; no declara aceptación integral. Faltan, entre otras variantes, límites completos de solicitudes, todos los permisos/estados, oráculos independientes de filtros/conteos, caída de base para readiness, inspección manual de accesibilidad y XSS en navegador. Las comprobaciones backend de marcado válido no sustituyen esa prueba del navegador.

G1: decisión independiente sobre SPEC-02/03 y correcciones de constitution/plan. G2: revisión de ADR-001 corregido, complemento ADR-003, modelo/ADR-002 por persona distinta de sus autores y paquete integrado. Carolina, Edier y Vega deben publicar sus propias decisiones con SHA, UTC, alcance y observaciones. El coordinador debe publicar personalmente su revisión de un aporte ajeno. El aviso al grupo fue confirmado por el coordinador en esta conversación, sin inventar una copia del mensaje original.

La implementación adelantada sigue registrada como riesgo de proceso. Este paquete no cierra G1/G2, no fusiona los PR originales y no crea `release-examen`. El PDF/Word candidato consolida lo existente para revisión; la entrega oficial depende de las decisiones y verificaciones restantes.
