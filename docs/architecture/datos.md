# Modelo de datos — G2

Vista de datos exigida por el enunciado §6. Motor: PostgreSQL (ADR-001). Restricciones transaccionales en [ADR-002](../adr/ADR-002-transacciones-idempotencia.md); tablas del asistente en [ADR-004](../adr/ADR-004-integracion-ia-degradacion.md). Nombres de tablas y columnas orientativos para las migraciones de Laravel.

```mermaid
erDiagram
    organizations ||--o{ users : "tiene"
    organizations ||--o{ inventory_items : "posee"
    organizations ||--o{ requests : "agrupa"
    users ||--o{ requests : "crea (solicitante)"
    requests ||--|{ request_lines : "contiene 1-10"
    inventory_items ||--o{ request_lines : "referenciado por"
    organizations ||--o{ audit_events : "registra"
    users ||--o{ audit_events : "actor"
    users ||--o{ idempotency_records : "envía"
    organizations ||--o{ ai_summaries : "agrupa"
    users ||--o{ ai_summaries : "coordinador"
    ai_summaries ||--o{ ai_attempts : "intentos"

    organizations {
        string id PK "ORG-A, ORG-B"
        string name
    }
    users {
        bigint id PK
        string organization_id FK
        string email UK "login ficticio"
        string password "hash"
        string role "SOLICITANTE, COORDINADOR, BODEGA, AUDITOR"
    }
    inventory_items {
        string organization_id PK "FK"
        string sku PK
        boolean active
        int fisico "CHECK >= 0"
        int reservado "CHECK >= 0 y <= fisico"
    }
    requests {
        uuid id PK
        string organization_id FK
        string codigo "UNIQUE por organizacion"
        bigint solicitante_id FK
        string estado "CHECK 6 estados"
        string zona
        string prioridad "NORMAL, ALTA"
        timestamptz fecha_requerida
        string observaciones "max 1000"
        timestamptz created_at
        timestamptz updated_at
    }
    request_lines {
        uuid request_id PK "FK"
        string sku PK
        string organization_id "FK compuesta"
        int cantidad "CHECK 1-500"
    }
    audit_events {
        uuid id PK
        string organization_id FK
        bigint actor_id FK
        string resource_type
        string resource_id
        string action
        timestamptz occurred_at "UTC"
        uuid correlation_id
        jsonb antes
        jsonb despues
    }
    idempotency_records {
        bigint id PK
        string organization_id "UK compuesta"
        bigint user_id "UK compuesta"
        string operation "UK compuesta"
        string idempotency_key "UK compuesta, 1-128"
        string request_hash "SHA-256"
        smallint response_status "200 o 409"
        jsonb response_body
        timestamptz created_at
        timestamptz expires_at "created_at + 24 h"
    }
    ai_summaries {
        uuid id PK
        string organization_id FK
        bigint coordinador_id FK
        jsonb request_ids
        string summary "max 600"
        jsonb recommendations
        jsonb origen
        string estado "BORRADOR, APROBADO"
        bigint aprobado_por
        timestamptz aprobado_at
    }
    ai_attempts {
        uuid id PK
        uuid summary_id FK
        string resultado "valido, invalido, timeout, error"
        string motivo "sanitizado"
        timestamptz created_at
    }
```

## Restricciones e índices

| Tabla | Restricción | Requisito |
|---|---|---|
| `inventory_items` | `CHECK (fisico >= 0 AND reservado >= 0 AND reservado <= fisico)`; disponible = fisico − reservado se calcula, no se guarda | RF05, RNF02 |
| `requests` | `CHECK (estado IN ('BORRADOR','ENVIADA','RESERVADA','DESPACHADA','ENTREGADA','CANCELADA'))`; `CHECK (prioridad IN ('NORMAL','ALTA'))`; `UNIQUE (organization_id, codigo)`; `UNIQUE (id, organization_id)` para la FK compuesta | RF03, RF04 |
| `request_lines` | `PRIMARY KEY (request_id, sku)` (sin SKU repetidos); `CHECK (cantidad BETWEEN 1 AND 500)`; FK `(request_id, organization_id)` → `requests`; FK `(organization_id, sku)` → `inventory_items` | RF02, RF03 |
| `audit_events` | Trigger que rechaza `UPDATE` y `DELETE`; índice `(organization_id, resource_id, occurred_at)` | RF09 |
| `idempotency_records` | `UNIQUE (organization_id, user_id, operation, idempotency_key)`; índice en `expires_at` para la limpieza | RF07 |
| Todas las de negocio | `organization_id` obligatorio; toda consulta filtra por la organización de la identidad | RF02 |

Las restricciones no sustituyen las validaciones de la aplicación (que devuelven 422 o 409 con mensaje claro); son una segunda barrera que impide datos imposibles aunque haya un error de código.

## Correspondencia con el contrato y el seed

| Contrato (`openapi.yaml`) | Columna |
|---|---|
| `Solicitud.codigo`, `estado`, `zona`, `prioridad`, `fechaRequerida`, `observaciones` | `requests.*` |
| `LineaSolicitud.sku`, `cantidad` | `request_lines.*` |
| `InventarioItem.fisico`, `reservado`, `disponible`, `activo` | `inventory_items.*` (disponible calculado) |
| `EventoAuditoria.actorId`, `organizationId`, `resourceId`, `action`, `occurredAt`, `correlationId` | `audit_events.*` |

El seed pequeño traduce `fixture-scenarios.json`: 2 organizaciones, 8 usuarios, 6 filas de inventario (por ejemplo, ORG-A KIT-ALIMENTO 10/0) y las solicitudes A-001, A-002 y B-001. El seed de carga de 2.000 solicitudes es independiente (§12).
