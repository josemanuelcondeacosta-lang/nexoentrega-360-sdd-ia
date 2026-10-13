# Fuentes y alcance de G1

1. Enunciado oficial y anexos: **NO RECIBIDOS**. Máxima autoridad cuando se proporcionen.
2. Misión del usuario «DESARROLLAR Y ENTREGAR NEXOENTREGA 360»: recibida el 2026-10-13. Describe el trabajo y límites; no prueba que sea copia del examen.
3. Borrador inicial del repositorio: commit `1aeacbf681aa711c78c4a4488f5d7bb2acaf43cd`.

La versión 0.2-draft amplía el borrador sin convertir supuestos en acuerdos. Los números RF se mantienen siguiendo la misión; las etiquetas RNF son provisionales hasta cotejar sus textos exactos. Ningún requisito se marca cerrado por no existir su fuente oficial. Si el examen contradice una propuesta, actualizar SPEC, matriz y pruebas previstas antes de aprobar.

| Propuesta transversal | Fuente/confirmación requerida |
|---|---|
| SOLICITANTE, COORDINADOR, BODEGA, AUDITOR y permisos detallados | Roles inferidos de borradores; CL-09 |
| Fecha calendario, zona America/Bogota, observaciones ≤1000 | CL-02/03 |
| Zonas por organización, prioridades ALTA/MEDIA/BAJA | CL-01/07/10 |
| Ámbito, respuestas conservadas y frontera exacta TTL idempotente | CL-04/11 |
| Whitelist de seis campos IA y nombres del contrato | CL-12 y anexos |
| RNF04 como rendimiento; semántica de otros RNF | CL-13 |

## Permisos propuestos (sin aprobación)

Todas las operaciones requieren sesión y organización resuelta por servidor. Un recurso ajeno se trata como inexistente (404); 403 indica operación prohibida dentro del alcance visible. Si se rechaza por rol antes de buscar el recurso, no revelar si el ID existe. Confirmar precedencia con CL-09.

| Operación | Solicitante | Coordinador | Bodega | Auditor |
|---|---|---|---|---|
| Crear/editar/enviar borrador | Solo propio | No | No | No |
| Consultar solicitudes | Propias | Organización | Organización | Organización |
| Reservar | No | Organización | No | No |
| Cancelar ENVIADA/RESERVADA | No, pendiente docente | Organización | No | No |
| Despachar/entregar | No | No | Organización | No |
| Consultar inventario | No, pendiente docente | Organización | Organización | Organización |
| Consultar historial | Propias | Organización | Organización | Organización |
| Tablero | Propias, pendiente docente | Organización | Organización | Organización |
| Generar/aprobar IA | No | Organización | No | No |

La matriz no habilita implementación: requiere confirmación expresa y revisión de SPEC-01/03/04/05.
