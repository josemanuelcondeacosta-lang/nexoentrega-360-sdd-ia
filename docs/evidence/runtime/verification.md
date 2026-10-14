# Verificación de aplicación provisional

Fecha de trabajo: 2026-10-14, America/Bogota. Ejecución por Codex en equipo del coordinador; no es aprobación humana.

Entorno: Windows, Docker Desktop con contenedores Linux, PHP 8.5.0, Laravel bloqueado en composer.lock, PostgreSQL 17.6, Node 22.22.2, Playwright 1.56.1: Chromium 141 definitivo; Edge en intentos previos. CI Ubuntu. Datos y cuentas ficticios. Suite PHP usa base separada nexo_test; navegador usa nexo de demostración.

## Resultados

Código y configuración candidatos: **f33183aa72c261bb513cf8aada1113605b461262**. Los commits posteriores de este paquete solo consolidan evidencia y documentos; no acreditan aceptación humana.

| Comprobación ejecutada | Resultado observado | Evidencia |
|---|---|---|
| PostgreSQL real / suite Laravel | 7 correctas, 57 aserciones; repetición local 18,24 s; CI 0,61 s | phpunit.xml y phpunit-ci.xml |
| Chromium 141 / Playwright | 4 correctas, 1,6 min; dos escenarios por viewport 1366×900 y 390×844 | playwright.json y PNG |
| Reserva HTTP concurrente | 200/409, un RESERVE, stock 4/3/1 y tras cancelar 4/0/4 | concurrency.json |
| Pint Linux | 35 archivos correctos | Ejecución local y CI |
| Composer audit / npm audit | Sin avisos de vulnerabilidad detectados | CI y npm-audit.json |
| Escáner limitado de secretos | Sin patrones detectados en archivos versionables | scripts/check_secrets.py y CI |
| CI del candidato | Dos ejecuciones terminadas success, incluyen PHP, navegador, concurrencia y validación documental | github-actions.json |

CI: [push 38631036099](https://github.com/josemanuelcondeacosta-lang/nexoentrega-360-sdd-ia/actions/runs/38631036099) y [PR 38631043185](https://github.com/josemanuelcondeacosta-lang/nexoentrega-360-sdd-ia/actions/runs/38631043185). No constituyen cierre de todos los AC.

Navegador: recorrido Solicitante → Coordinador → Bodega → Coordinador → Auditor hasta ENTREGADA; ingreso por Tab/Enter, foco inicial del formulario, reserva de 500 unidades rechazada sin alterar estado ENVIADA ni inventario, POST IA sin CSRF devuelve 419 y sin desbordamiento horizontal en ambos anchos. Capturas muestran datos ficticios acumulados por pruebas.

La ejecución definitiva usa Chromium aislado de Playwright. Intentos anteriores con Edge fueron interrumpidos por bloqueo de carga; no se contabilizan como suite correcta. Se retiró la fuente remota y la interfaz funciona con fuentes locales. No se declara rendimiento p95 a partir de estos tiempos de prueba.

Verificación documental ejecutada: cinco SPECS, 73 AC, 14 tests documentales correctos. OpenAPI integrado válido: 20 rutas y 22 operaciones; dos ejemplos IA válidos y tres casos negativos rechazados. Esto valida documentación, no el proveedor IA.

Primera suite PostgreSQL: 7 pruebas, 57 aserciones correctas. Cubre recorrido con auditoría y stock, rechazo parcial sin cambios, repetición/conflicto de clave, cancelación y despacho inválido, aislamiento por organización/rol, aprobación de fallback sin stock y cantidades inválidas. Reporte JUnit en phpunit.xml.

Ensayo concurrente HTTP real: dos solicitudes ORG-B compiten por KIT-ALIMENTO (físico 4, cada solicitud pide 3): respuestas 200/409, stock 4/3/1, un evento RESERVE, cancelación restaura 4/0/4. No equivale a prueba de carga ni a todos los intercalados posibles.

Hallazgos corregidos durante ejecución: servidor Laravel perdía variables de conexión al arrancar sin --no-reload; formulario cerraba antes de refrescar lista. Fallos adicionales del arnés: selector ambiguo Zona, nombre accesible del botón con flecha y captura de inventario antes de cargarse. Se conservan como historial de ejecución; no se ocultan bajo una aprobación general.

## Brechas y límites

No se declara cumplimiento global RF01–RF12/RNF01–RNF06. Falta proveedor LLM real y validación estricta de su salida, carga 20VU/60s y p95, pruebas exhaustivas de TTL/fallos transaccionales/concurrencia, restauración de backup, revisión manual accesible y decisiones humanas G1/G2 sobre el candidato. El recorrido de teclado automatizado no demuestra conformidad WCAG. CI remoto no se declara correcto sin observar su ejecución.

La matriz documental enlaza requisitos con pruebas planificadas; no equivale a cobertura de aplicación. Los casos observados arriba son un subconjunto, y cualquier AC sin evidencia ejecutada permanece sin verificar. spec-vs-code.csv indexa los 73 AC y módulos; todos conservan estado NOT_VERIFIED_IN_FULL hasta cotejo individual del escenario completo.
