# Estrategia de pruebas — G2

- **Versión:** 0.1 (2026-10-14)
- **Autor:** Vega José, con asistencia de IA (AI-004)
- **Base:** enunciado §9 (CI), §11 (plan mínimo de pruebas), anexo `10_test-report-template.md`, SPEC 0.3-draft, ADR-004. Tecnología según ADR-001: Laravel con PostgreSQL o MySQL; las herramientas se ajustan si ADR-001 cambia.
- **Estado:** propuesta pendiente de revisión G2.

## Principios

1. Cada prueba nace de un AC aprobado y lleva su ID en el nombre (por ejemplo, `test_AC_03_08_reservas_concurrentes`).
2. **Base real**: integración y concurrencia contra el mismo motor de producción (PostgreSQL o MySQL en contenedor), nunca SQLite en memoria ni mocks de inventario (§11).
3. **Oráculos independientes**: el estado y las cantidades esperadas se escriben antes de ejecutar, como constantes del test, sin reutilizar el código de producción (anexo `acceptance-oracles.md`).
4. **Efectos persistidos**: además del código HTTP, se consulta la base (estado, físico, reservado, eventos, resultado idempotente).
5. **Datos limpios**: cada escenario restablece el seed de `fixture-scenarios.json`; ninguna prueba depende de otra.
6. **Reloj controlado**: `Carbon::setTestNow('2030-01-10T14:00:00Z')` o `travelTo()`, para fechas, TTL de 24 h y timeout.
7. **Resultados honestos**: un test omitido o un check desactivado queda visible; esperado y observado se registran por separado.

## Niveles y herramientas

| Familia (§11) | Qué cubre | Herramienta en Laravel | AC principales |
|---|---|---|---|
| Dominio | Cantidades 1–500, 1–10 líneas, SKU repetido, fechas, matriz de transiciones | PHPUnit o Pest (unitarias) | AC-02-02, 02-04, 02-06, 02-07, 03-07 |
| API y base real | Reserva total, rechazo sin efectos parciales, cancelación, despacho, idempotencia y conflicto | Pruebas HTTP de Laravel (`$this->postJson`) con `DatabaseMigrations` contra el contenedor | AC-03-01 a 03-15 |
| Concurrencia | Dos reservas simultáneas que compiten por stock | Dos procesos reales (Laravel `Process::pool` o peticiones paralelas a `php artisan serve`) con barrera; ≥ 20 repeticiones; sin envolver en transacción de prueba | AC-03-03, 03-08 |
| Seguridad | 401, 403 por rol, 404 entre organizaciones, aislamiento en listados, auditoría e IA, logs sin secretos | Pruebas HTTP con las ocho cuentas del seed | AC-01-01 a 01-12, 04-11, 05-11 |
| IA | Respuesta válida, JSON inválido, campo extra, ID ajeno, timeout, fallo, inyección | `FixtureProvider` con los siete fixtures; proveedor espía | AC-05-01 a 05-14 |
| E2E | Solicitante crea y envía; coordinador reserva; bodega despacha; coordinador entrega; cancelación libera stock | Playwright (o Laravel Dusk) sobre el candidato de entrega | Recorrido de SPEC-02 a SPEC-04 |
| Regresión | Cada defecto corregido conserva un test que lo reproduce | Mismo framework; etiqueta del defecto en el nombre | Según defectos |
| Operación | Clon limpio, migración, seed, health y readiness, carga, móvil y teclado | Script de arranque cronometrado; k6 para RNF03; revisión manual 390/1366 px | AC-04-09, 04-12, 04-13, 01-13, 02-11, 03-16, 04-14 |

## Pruebas del asistente de IA

Resultado comprobado de los siete fixtures oficiales contra `ai-output.schema.json` y la selección {A-001, A-002}:

| Fixture | Esquema JSON | Pertenencia de IDs | Resultado esperado del sistema |
|---|---|---|---|
| AI-VALID | Válido | Válida | Borrador mostrado |
| AI-JSON-BAD | JSON roto | — | Fallback |
| AI-FOREIGN-ID | **Válido** | **Inválida (B-001)** | Fallback |
| AI-EXTRA | Inválido (`execute`) | — | Fallback |
| AI-TIMEOUT | — (5001 ms) | — | Corte a 5 s y fallback |
| AI-ERROR | — (error del proveedor) | — | Fallback |
| AI-INJECTION | **Válido** | **Inválida (B-001)** | Fallback; nada se ejecuta |

AI-FOREIGN-ID y AI-INJECTION pasan el esquema: solo la validación de pertenencia en código los rechaza. Por eso esa validación tiene pruebas propias y no se delega al esquema (ADR-004).

Casos adicionales a los fixtures: límites 600/5/240/1–3 (V-06, V-07), `requestId` repetido (V-04), salida válida con marcado para probar el escape (V-09b), selección inválida sin invocar al proveedor (V-13) y aprobación con snapshot (V-14). Detalle en `docs/reviews/aportes/vega-G1.md`.

## Integración continua (GitHub Actions)

Pasos según el checklist CI-01 a CI-12 de `vega-G1.md`. Primer workflow previsto en G3:

| Paso | Comando orientativo |
|---|---|
| Instalación | `composer install --no-interaction --prefer-dist` (con `composer.lock`) y `npm ci` |
| SPEC | `python scripts/export_specs.py --check` y `python scripts/build_coverage.py --check` (sin regenerar antes) |
| Formato y análisis | `vendor/bin/pint --test` y `vendor/bin/phpstan analyse` |
| Pruebas | `php artisan test` con servicio `postgres` (o `mysql`) del runner; `AI_PROVIDER=fixture` y sin `AI_API_KEY` |
| Contrato | Validación de `contracts/openapi.yaml` y del esquema de IA |
| Build | `npm run build` |
| Dependencias y secretos | `composer audit`, `npm audit` y gitleaks |
| E2E | Playwright solo sobre el candidato de entrega |

## Evidencia por prueba

Cada ejecución relevante se registra con el formato del anexo 10: test y AC, commit, versiones, fecha y entorno, datos y precondiciones, oráculo escrito antes, comando, resultado observado, defectos y enlace al reporte de CI. Se guarda en `evidence/tests/`.

## Responsables propuestos

| Área | Responsable | Revisión |
|---|---|---|
| IA, seguridad y CI | Vega José | Edier |
| Inventario, concurrencia e idempotencia | Edier | Vega José |
| Validaciones de solicitudes (RF03, RF04) | Carolina | José Conde |
| E2E e interfaz | José Conde | Carolina |

Todos aportan pruebas reales (§9). Asignaciones sujetas al plan y las tareas de G2.
