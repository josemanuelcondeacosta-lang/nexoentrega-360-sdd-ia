# Secuencia: reserva concurrente

Diagrama exigido por el enunciado §6 («reserva concurrente»). Decisión en [ADR-002](../adr/ADR-002-transacciones-idempotencia.md). Escenario del oráculo oficial: KIT-ALIMENTO de ORG-A con físico 10 y reservado 0; A-001 y A-002, ambas ENVIADA con 7 unidades, se reservan al mismo tiempo con claves distintas K1 y K2. No se fija cuál gana; el diagrama muestra el caso en que T1 obtiene primero el bloqueo del stock.

```mermaid
sequenceDiagram
    autonumber
    participant T1 as Petición T1<br/>reservar A-001 (K1)
    participant T2 as Petición T2<br/>reservar A-002 (K2)
    participant API as API Laravel<br/>módulo Inventory
    participant DB as PostgreSQL

    par Llegan a la vez
        T1->>API: POST /api/requests/A-001/reserve (K1)
    and
        T2->>API: POST /api/requests/A-002/reserve (K2)
    end
    Note over API: Ambas: sesión, organización (404), rol Coordinador (403), cuerpo y clave (422)

    API->>DB: T1: BEGIN, lock consultivo K1, buscar K1 (no existe)
    API->>DB: T2: BEGIN, lock consultivo K2, buscar K2 (no existe)
    API->>DB: T1: SELECT A-001 FOR UPDATE (ENVIADA)
    API->>DB: T2: SELECT A-002 FOR UPDATE (ENVIADA)
    API->>DB: T1: SELECT stock KIT-ALIMENTO FOR UPDATE
    DB-->>API: T1 obtiene el bloqueo: 10 / 0
    API->>DB: T2: SELECT stock KIT-ALIMENTO FOR UPDATE
    Note over DB: T2 espera: la fila está bloqueada por T1

    API->>API: T1: disponible 10 >= 7
    API->>DB: T1: reservado = 7, A-001 RESERVADA, evento, registro K1 = 200
    API->>DB: T1: COMMIT
    API-->>T1: 200 A-001 RESERVADA

    DB-->>API: T2 obtiene el bloqueo con la versión confirmada: 10 / 7
    API->>API: T2: disponible 3 < 7
    API->>DB: T2: registro K2 = 409 (sin tocar stock ni estado), COMMIT
    API-->>T2: 409 stock insuficiente

    Note over DB: Final: físico 10, reservado 7, disponible 3, A-001 RESERVADA, A-002 ENVIADA, exactamente un evento de reserva
```

## Por qué es correcto

| Riesgo | Cómo lo evita el diseño |
|---|---|
| Ambas reservas exitosas (doble promesa de stock) | La fila de stock se bloquea con `FOR UPDATE`; T2 solo puede leerla después de que T1 confirma, y en READ COMMITTED recibe la versión ya actualizada (reservado 7) |
| Reserva parcial con varias líneas | Todas las filas se comprueban antes de escribir; si alguna falla, no se modifica ninguna (RF05) |
| Deadlock entre dos solicitudes con varios SKU | Todas las transacciones bloquean en el mismo orden: solicitud y luego stock por SKU ascendente |
| Mismo pedido enviado dos veces con la misma clave | El bloqueo consultivo de la clave serializa ambas; la segunda encuentra el registro y repite la respuesta sin nuevos efectos |
| Fallo a mitad de la transacción | `ROLLBACK` total; ni stock, ni estado, ni evento, ni registro de idempotencia (AC-03-09) |

## Cómo se prueba (AC-03-03 y AC-03-08)

1. Restablecer el seed (10 / 0; A-001 y A-002 ENVIADA).
2. Lanzar dos procesos reales que esperan en una barrera y envían las dos reservas a la vez.
3. Oráculo escrito antes: una respuesta 200 y una 409 (sin importar cuál), stock 10 / 7 / 3 y un solo evento de reserva, comprobado con una consulta directa a PostgreSQL.
4. Repetir al menos 20 veces con datos limpios; cualquier repetición con dos 200 o reservado distinto de 7 es un defecto.
