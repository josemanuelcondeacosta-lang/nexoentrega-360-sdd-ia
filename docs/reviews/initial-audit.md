# Auditoría inicial — G1

Fecha de elaboración: 2026-10-13 (America/Bogota). Autor de esta preparación: asistente Codex; no representa revisión de un integrante.

Referencia inspeccionada: `1aeacbf681aa711c78c4a4488f5d7bb2acaf43cd`. Rama de trabajo: `docs/g1-specifications`. Árbol inicialmente limpio. Se leyeron los 17 archivos de proyecto: README, ignore, cinco Markdown SPEC, sus cinco JSON derivados, constitución, aclaraciones, riesgos, plantilla G1 y exportador. No se encontró AGENTS.md dentro del proyecto.

| Hallazgo | Evidencia inicial | Acción G1 | Estado |
|---|---|---|---|
| Fuente oficial ausente | Solo misión y documentos de borrador | Solicitar enunciado/anexos; no cerrar equivalencia de RF/RNF | Bloqueo humano |
| Cinco historias ya existentes | Una por SPEC | Preservar y asignar HU-01 a HU-05 | Preparado |
| Entradas/procesos/salidas incompletos | Marcadores «Pendiente» en los cinco bloques | Especificar escenarios, límites, HTTP y persistencia | Borrador ampliado |
| Solo 21 AC iniciales | 4+4+5+4+4 | Conservar IDs y ampliar cobertura | Borrador ampliado |
| RNF04 sin asignación | No aparece en ninguna SPEC inicial | Vincular provisionalmente rendimiento en SPEC-04, pendiente cotejo oficial | Pendiente confirmar |
| Ocho preguntas sin respuesta | CL-01 a CL-08 | Conservarlas y agregar permisos, prioridades, TTL, whitelist y RNF | Pendiente docente |
| Exportador solo comprueba igualdad | No valida estructura ni número de SPEC | Añadir validación semántica y casos negativos | Verificación automatizable |
| Sin matriz de cobertura | Archivo inexistente | Trazar RF/RNF y cada AC a módulos/pruebas propuestas | Preparado; evidencia pendiente |
| Sin aprobación G1 | Plantilla explícitamente no aprobada | Preparar checklist y hallazgos para revisor independiente | Bloqueo humano |
| Sin aplicación ni pruebas de producto | No hay frontend/backend/base/CI | Respetar punto de control antes de implementar | Pendiente G1/G2 |

No hay defectos de aplicación registrados: la aplicación aún no existe. Los hallazgos anteriores son documentales. Ningún integrante tiene contribución o revisión acreditada por esta auditoría. No existen tags de entrega, aceptación final ni PDF final.

## Criterio de avance

El equipo debe revisar los artefactos versionados y sus supuestos; el docente debe proporcionar fuente oficial o resolver dudas. G1 solo puede aprobarse con revisión humana real ligada al SHA revisado. La preparación G2 puede avanzar sin implementar funcionalidades, pero no se presume aprobación G1 ni G2.
