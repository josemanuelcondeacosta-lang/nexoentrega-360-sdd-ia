# Secuencia: consulta de IA con fallo

Diagrama exigido por el enunciado §6 («consulta de IA con fallo»). Decisión de diseño en [ADR-004](../adr/ADR-004-integracion-ia-degradacion.md). Fronteras de confianza: el navegador y el proveedor de IA son **no confiables**; la API y la base de datos son confiables.

```mermaid
sequenceDiagram
    autonumber
    actor C as Coordinador (ORG-A)
    box rgba(200,80,80,0.12) No confiable
        participant W as Navegador
    end
    box rgba(80,140,200,0.12) Confiable - servidor
        participant A as API Laravel<br/>AiSummaryService
        participant V as AiOutputValidator
        participant F as FallbackSummarizer
        participant DB as Base de datos
    end
    box rgba(200,80,80,0.12) No confiable - externo
        participant P as Proveedor IA<br/>(AiProvider)
    end

    C->>W: Selecciona A-001 y A-002 y pide resumen
    W->>A: POST /api/ai/summaries {requestIds}
    A->>A: Sesión válida y rol Coordinador (401 / 403)
    A->>A: 1–20 IDs únicos (422)
    A->>DB: Cargar solicitudes de ORG-A por ID
    DB-->>A: Solicitudes
    A->>A: Todas de ORG-A (404) y ENVIADA (409)
    A->>A: Proyectar solo requestId, zona, prioridad,<br/>fechaRequerida, estado, observaciones
    A->>P: complete(prompt, timeout = 5 s)

    alt Respuesta a tiempo
        P-->>A: Texto crudo
        A->>V: Validar JSON, esquema y pertenencia de IDs
        alt Salida válida
            V-->>A: OK
            A->>DB: Guardar borrador (origen proveedor y modelo) e intento válido
            A-->>W: 200 borrador «Asistencia de IA»
        else Salida inválida (JSON roto, campo extra, ID ajeno o repetido, límites)
            V-->>A: Rechazo con motivo
            A->>F: Resumen determinista de la selección
            F-->>A: summary, recommendations = []
            A->>DB: Guardar borrador (origen fallback) e intento inválido con motivo sanitizado
            A-->>W: 200 mensaje controlado + resumen alternativo
        end
    else Timeout a los 5 s o error del proveedor
        P--xA: Sin respuesta / error
        A->>F: Resumen determinista de la selección
        F-->>A: summary, recommendations = []
        A->>DB: Guardar borrador (origen fallback) e intento timeout o error
        A-->>W: 200 mensaje controlado + resumen alternativo
    end

    Note over A,DB: Ninguna rama escribe inventario, estado ni prioridad
    W-->>C: Muestra el borrador con texto escapado

    C->>W: Aprueba el borrador
    W->>A: POST /api/ai/summaries/{id}/approve
    A->>DB: Transacción: estado APROBADO + evento de auditoría (RF09)
    DB-->>A: Confirmado
    A-->>W: 200 aprobado (segunda aprobación: 409)
```

## Lectura del diagrama

| Paso | Requisito | Qué garantiza |
|---|---|---|
| 3–7 | RF02, RF10, CL-19 | Rechazos 401, 403, 422, 404 y 409 antes de llamar al proveedor |
| 8 | RF10 | El proveedor recibe solo los seis campos autorizados |
| 9 | RF12 | Plazo de 5 s |
| Rama «salida inválida» | RF11 | El esquema y la pertenencia se validan en el servidor; el texto del modelo no se ejecuta |
| Rama «timeout o error» | RF12, RNF06 | Mensaje controlado y resumen determinista; el resto del sistema sigue funcionando |
| Nota | RF12, §10 | El modelo nunca escribe inventario ni invoca operaciones privilegiadas |
| Aprobación | RF09, RF12 | Evento de auditoría en la misma transacción; sin efectos sobre el negocio |

El diagrama debe coincidir con el código entregado (§6); si la implementación cambia, se actualiza en el mismo PR.
