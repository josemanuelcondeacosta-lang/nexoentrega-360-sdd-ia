# Componentes y fronteras — propuesta G2

Todos los módulos comparten proceso Laravel y una base; los bloques no son microservicios. Flechas indican llamadas o acceso autorizado. ADR-001 define dependencias; ADR-002/003 pendientes concretan transacción y sesión.

```mermaid
flowchart TB
    browser["Navegador no confiable: Blade y formularios"] -->|"HTTPS y credencial de sesión"| gateway
    subgraph backend["Proceso Laravel: frontera de aplicación"]
        gateway["Rutas, validación y autorización"] --> identity["Identity: sesión, rol y organización"]
        gateway --> requests["Requests: borrador y envío"]
        gateway --> inventory["Inventory: reserva y transiciones"]
        gateway --> queries["Queries: filtros y agregados autorizados"]
        gateway --> ai["AI: selección y aprobación humana"]
        gateway --> operations["Operations: health, readiness y logs"]
        inventory --> requests
        inventory --> idempotency["Idempotency: ámbito y resultado"]
        requests --> audit["Audit: eventos append-only"]
        inventory --> audit
        ai --> audit
        ai -->|"Solo lectura autorizada"| requests
        ai --> adapter["Adaptador, validador y fallback"]
    end
    requests --> database[("PostgreSQL: organización en cada recurso")]
    inventory --> database
    idempotency --> database
    audit --> database
    queries --> database
    identity --> database
    ai --> database
    operations -->|"Readiness"| database
    adapter -->|"Payload permitido; deadline 5 s"| provider["Proveedor externo no confiable"]
    provider -->|"Salida sin herramientas privilegiadas"| adapter
```

La persistencia AI solo guarda resumen/aprobación/intento, no inventario. Requests/Inventory/Idempotency/Audit deben usar la misma transacción para cada transición; no llamadas HTTP internas ni colas para confirmar stock. Logs de rechazos son técnicos, no eventos de transición exitosa. Readiness no llama al LLM, por lo que un proveedor caído no impide usar el sistema. Identidad y filtros se aplican antes del replay idempotente. Texto IA se escapa en la UI.
