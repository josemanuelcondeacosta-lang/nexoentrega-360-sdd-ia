# Respaldo y restauración (rollback)

Ejecutado por Edier Castro el 2026-10-15T02:40Z (aprox.) en Windows con Docker Desktop, PostgreSQL 17.6 (`postgres:17.6-bookworm`), sobre la base de demostración `nexo` de la rama `test/edier-transacciones` (app del PR #12 más las pruebas del PR #13).

## Procedimiento

```powershell
docker compose exec database pg_dump -U nexo -d nexo -Fc -f /tmp/nexo.dump
docker compose exec database createdb -U nexo nexo_restaurada
docker compose exec database pg_restore -U nexo -d nexo_restaurada /tmp/nexo.dump
```

Comparación: por cada tabla, número de filas y md5 de todas sus filas ordenadas (`md5(string_agg(t::text, '|' order by t::text))`), en `nexo` y en `nexo_restaurada`.

## Resultado

| Tabla | Filas | md5 (original = restaurada) |
|---|---|---|
| requests | 60 | 4fa4e5943567121423294a5546ea0af4 |
| request_lines | 60 | 364918dbc944bca72517938f49ea8f43 |
| inventory_items | 6 | ff6574e29be9615004b7516b192e7e77 |
| audit_events | 160 | a9b4254d37ffd57237baa4c040310c64 |
| idempotency_records | 60 | 1d251d1b820b5d3114c72aeb9f8f48e9 |
| users | 8 | 66386a89aff8e46034ada6eee62fa7db |

- `Compare-Object` entre ambas salidas: sin diferencias.
- En `nexo_restaurada` existen la restricción `stock_nonnegative` y el trigger `audit_immutable`.
- `php artisan migrate:status` con `DB_DATABASE=nexo_restaurada`: las 4 migraciones en estado Ran; la aplicación se conecta a la base restaurada.

## Conclusión y límites

El respaldo `pg_dump -Fc` se restaura en una base nueva con contenido idéntico, restricciones e inmutabilidad de auditoría. Rollback de datos: restaurar un respaldo verificado en una base nueva y cambiar `DB_DATABASE`, como indica el runbook (no se usa `migrate:rollback`). Datos de demostración pequeños; el archivo de respaldo no se versiona.