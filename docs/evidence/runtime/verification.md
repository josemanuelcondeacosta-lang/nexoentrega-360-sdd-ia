# Verificación de aplicación provisional

Fecha de trabajo: 2026-10-14, America/Bogota. Ejecución por Codex en equipo del coordinador; no es aprobación humana.

Entorno: Windows, Docker Desktop con contenedores Linux, PHP 8.5.0, Laravel bloqueado en composer.lock, PostgreSQL 17.6, Node 22.22.2, Playwright 1.56.1 con Edge. Datos y cuentas ficticios. Suite PHP usa base separada nexo_test; navegador usa nexo de demostración.

## Resultados

El cierre de esta ejecución y sus reportes se registrará al finalizar los procesos. No usar un proceso iniciado como prueba superada.

Verificación documental ejecutada: cinco SPECS, 73 AC, 14 tests documentales correctos. OpenAPI integrado válido: 20 rutas y 22 operaciones; dos ejemplos IA válidos y tres casos negativos rechazados. Esto valida documentación, no el proveedor IA.

Primera suite PostgreSQL: 7 pruebas, 57 aserciones correctas. Cubre recorrido con auditoría y stock, rechazo parcial sin cambios, repetición/conflicto de clave, cancelación y despacho inválido, aislamiento por organización/rol, aprobación de fallback sin stock y cantidades inválidas. Reporte JUnit en phpunit.xml.

Ensayo concurrente HTTP real: dos solicitudes ORG-B compiten por KIT-ALIMENTO (físico 4, cada solicitud pide 3): respuestas 200/409, stock 4/3/1, un evento RESERVE, cancelación restaura 4/0/4. No equivale a prueba de carga ni a todos los intercalados posibles.

Hallazgos corregidos durante ejecución: servidor Laravel perdía variables de conexión al arrancar sin --no-reload; formulario cerraba antes de refrescar lista. Fallos adicionales del arnés: selector ambiguo Zona, nombre accesible del botón con flecha y captura de inventario antes de cargarse. Se conservan como historial de ejecución; no se ocultan bajo una aprobación general.

## Brechas y límites

No se declara cumplimiento global RF01–RF12/RNF01–RNF06. Falta proveedor LLM real y validación estricta de su salida, carga 20VU/60s y p95, pruebas exhaustivas de TTL/fallos transaccionales/concurrencia, restauración de backup, revisión manual accesible y decisiones humanas G1/G2 sobre el candidato. El recorrido de teclado automatizado no demuestra conformidad WCAG. CI remoto no se declara correcto sin observar su ejecución.

La matriz documental enlaza requisitos con pruebas planificadas; no equivale a cobertura de aplicación. Los casos observados arriba son un subconjunto, y cualquier AC sin evidencia ejecutada permanece sin verificar.
