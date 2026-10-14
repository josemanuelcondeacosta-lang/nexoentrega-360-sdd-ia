# Contexto — propuesta G2

ADR-001; RF01–12. G1 y G2 sin aprobación. API/Blade y PostgreSQL dentro del sistema; proveedor externo sin privilegios. Datos y credenciales ficticios para demo.

```mermaid
flowchart LR
    solicitante["Solicitante de ORG-A u ORG-B"] -->|"Crear, enviar y cancelar propias"| sistema
    coordinador["Coordinador de ORG-A u ORG-B"] -->|"Reservar, entregar y revisar IA"| sistema
    bodega["Bodega de ORG-A u ORG-B"] -->|"Consultar inventario y despachar"| sistema
    auditor["Auditor de ORG-A u ORG-B"] -->|"Consultar organización e historial"| sistema
    operador["Operador o evaluador"] -->|"Health y readiness"| sistema
    subgraph limiteSistema["NexoEntrega 360"]
        sistema["Laravel: UI y API autorizada"] -->|"Transacciones y lecturas aisladas"| postgres[("PostgreSQL")]
    end
    sistema -->|"Hasta 20 ENVIADA y seis campos autorizados"| proveedor["Proveedor LLM externo"]
    proveedor -->|"Texto no confiable: validar o fallback"| sistema
```

Toda petición lleva identidad del servidor y alcance de organización; los usuarios no acceden directamente a PostgreSQL o al proveedor. CL-16/17/18 no amplían los permisos mostrados. El evaluador consulta operación sin recibir secretos. No hay prueba de despliegue ni rendimiento asociada todavía.
