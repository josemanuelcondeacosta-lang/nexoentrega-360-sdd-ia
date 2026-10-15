# Prueba de carga RNF03 (AC-04-09)

Requisito oficial (§5): listados y tablero con p95 ≤ 700 ms y errores < 1 % con 20 usuarios virtuales durante 60 s, después de 5 s de calentamiento, sobre 2.000 solicitudes sembradas; excluir IA; reportar hardware y herramienta.

## Método

- **Datos:** `php artisan nexo:seed-carga` siembra 2.000 solicitudes ficticias (90 % ORG-A, 10 % ORG-B; BORRADOR y ENVIADA, con su evento SEND). No reserva ni toca inventario y no se repite si ya existen.
- **Herramienta:** k6 1.8.2 en la imagen `grafana/k6:1.8.2` (servicio `k6` de `compose.yaml`, perfil `carga`).
- **Script:** `tests/load/rnf03.js`. Escenario `calentamiento` de 5 s y escenario `medicion` de 60 s, ambos con 20 usuarios virtuales que alternan Coordinador y Auditor de ORG-A. Cada iteración consulta `GET /api/requests` (página aleatoria de 20, con filtros de estado, prioridad o zona) y `GET /api/dashboard`, y espera 1 s.
- **Umbrales**, solo sobre la fase medida y por endpoint: `p(95)<=700` en `http_req_duration` y `rate<0.01` en `http_req_failed`. El inicio de sesión se etiqueta aparte y no cuenta.
- **IA excluida:** ninguna petición toca `/api/ai/*`.

## Ejecución

```powershell
docker compose up -d --build
docker compose exec app php artisan nexo:seed-carga
docker compose --profile carga run --rm k6
```

k6 imprime p95, mediana y errores por endpoint y escribe el resultado completo en `docs/evidence/runtime/carga-rnf03.json`. El hardware del equipo que ejecutó la prueba queda en `docs/evidence/runtime/carga-rnf03-hardware.txt`.

## Resultado

Ver `carga-rnf03.json` (salida real de k6, con fecha UTC) y `carga-rnf03-hardware.txt`. La aplicación corre con el servidor de desarrollo de PHP y 4 workers dentro de Docker; no es un despliegue de producción.
