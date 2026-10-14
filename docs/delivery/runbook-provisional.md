# Ejecución y entrega provisional

Estado: aplicación en desarrollo solicitada por el coordinador antes del cierre de revisiones. No constituye G1/G2 aprobado ni aceptación final. Usa exclusivamente cuentas/datos ficticios.

## Arranque con PostgreSQL real

Requisitos: Docker Desktop activo (Linux containers), Git; Node 22 para tests de navegador. Desde la raíz:

```powershell
docker compose up -d --build
```

Abrir `http://localhost:8000`. Primer arranque instala dependencias fijadas, genera APP_KEY local, migra y siembra si no existen organizaciones. Reiniciar no elimina solicitudes ni vuelve a sembrar. La base persiste en volumen `nexo_data`. No usar `down -v` sobre datos que se quieran conservar.

Servicios: `GET /health` confirma proceso; `GET /ready` comprueba base y tabla final de migración. La IA no participa en readiness. Aplicación usa servidor PHP de desarrollo con varios workers; no es despliegue de producción. Contraseña PostgreSQL incluida es **ficticia y solo para evaluación local**; puerto DB no se publica al host.

## Cuentas ficticias

Contraseña de las ocho cuentas: `DemoNexo360!`. Logins: `org-a-solicitante@example.test`, `org-a-coordinador@example.test`, `org-a-bodega@example.test`, `org-a-auditor@example.test`, y los cuatro equivalentes `org-b-…`. Rol y organización vienen del servidor tras autenticar, no de un selector de permisos. Para regenerar datos usar una **base nueva de evaluación**, migraciones y `db:seed`; no borrar una base compartida para hacer una demo.

Recorrido: Solicitante crea borrador con fecha futura y envía → Coordinador reserva → Bodega despacha → Coordinador registra entrega → Auditor consulta historial. Cancelación pertenece al Solicitante dueño. Stock se actualiza en API; interfaz no calcula ni persiste decisiones de inventario. El catálogo solo expone nombres SKU activos propios, sin cantidades para Solicitante.

## Verificación

Crear base de pruebas independiente `nexo_test` en PostgreSQL del contenedor antes de la suite; no ejecutar tests sobre `nexo` de demostración:

```powershell
docker compose exec database psql -U nexo -d postgres -c "CREATE DATABASE nexo_test"
docker compose exec -e APP_ENV=testing -e DB_DATABASE=nexo_test app php artisan test
npm ci
npm run test:e2e
```

Crear la base solo si no existe. Los tests HTTP de Laravel usan una base real para comprobar efectos; su middleware CSRF se omite por modo testing del framework y se comprueba externamente con navegador. Los E2E operan sobre la demo y añaden solicitudes ficticias; cada ejecución consume algunas existencias. Usar entorno de evaluación limpio dedicado, no datos compartidos, al repetir todos los escenarios.

Instalar Edge para la configuración local de Playwright. Capturas/trazas/reporte se generan en test-results y playwright-report (ignorados en Git); seleccionar evidencia sanitizada para docs/evidence/runtime. La verificación por teclado automatizada no equivale a conformidad WCAG ni sustituye revisión manual.

## Operación, rollback y brechas

`docker compose logs app` y `docker compose exec app cat storage/logs/nexo.log`: logs JSON con correlationId, status y ruta patrón; no cuerpo/credenciales/tokens. No guardar dumps de sesión ni APP_KEY en evidencias. Mantener `.env` fuera de Git.

Para detener sin borrar datos: `docker compose stop`. Antes de modificar migraciones, crear backup `pg_dump` y probar restauración en otra base. Una imagen anterior de aplicación solo debe levantarse si es compatible con el esquema actual. Esta versión crea esquema inicial: no prometer rollback de datos mediante `migrate:rollback`; restaura solo un backup verificado en base nueva y cambia conexión tras comprobarlo. No se declara restauración probada hasta ejecutarla.

IA provisional: fallback determinista visible, aprobación con auditoría y sin inventario. **Pendientes:** proveedor real/replay y validador completo de salida IA, carga 20VU/60s, trazabilidad de todos AC, secret scan, revisión independiente y gates. Reportes de ejecución se registran separadamente. No crear `release-examen` ni declarar aceptación completa mientras existan incumplimientos.
