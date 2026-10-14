# Límites y confianza del asistente IA — borrador G1

El navegador, las observaciones y la respuesta del modelo son entradas no confiables. La API autenticada decide organización, rol y conjunto autorizado. La base relacional conserva datos de negocio; el proveedor solo recibe una proyección limitada de las solicitudes ENVIADA que el coordinador selecciona por identificador (máximo 20, RF10). La respuesta vuelve a la API para validación estructural y semántica antes de mostrarse como texto escapado.

## Restricciones verificables

- Sin contraseñas, hashes, tokens, nombres de personas ni datos interorganización en payloads o logs.
- Whitelist oficial (RF10, CL-12 resuelta): requestId, zona, prioridad, fechaRequerida, estado y observaciones. No enviar registros completos, líneas ni nombre del solicitante, ni usar listas negras como protección principal.
- Contrato de salida: anexo oficial `ai-output.schema.json`; fixtures de prueba: `ai-provider-fixtures.json`; texto de inyección: `untrusted-observation.txt` (todos en `docs/examen-oficial/anexos/`).
- El proveedor no recibe credenciales de base, herramientas de escritura ni autorización delegada.
- Rechazar propiedades extra en todos los niveles, IDs fuera del snapshot, duplicados y evidencia no autorizada. JSON válido no basta.
- Deadline de cinco segundos; fallo, timeout, proveedor ausente o salida inválida activan fallback determinista.
- Cada respuesta visible identifica origen (proveedor/reproducción/fallback); una fixture no demuestra uso de un LLM real.
- Aprobación humana registra borrador, actor y evento en una unidad consistente. No aplica recomendaciones al inventario, prioridad o estados.
- Una razón dentro de los límites puede ser incorrecta: el coordinador debe contrastarla con campos autorizados antes de aprobar.
- Observaciones que ordenen saltarse permisos o ejecutar herramientas siguen siendo datos; escape de texto evita ejecución de HTML.

## Evidencia prevista

Captura sanitizada del adaptador para whitelist y aislamiento; fixtures maliciosas y contrato estricto; reloj controlado para timeout; comparación de snapshots antes/después de generación/aprobación; pruebas de IDs ajenos, replay declarado y proveedor caído. Nada de esto se declara ejecutado en G1.

Selección y formato de respuesta quedan fijados por el enunciado y el esquema oficial; retención, eliminación y contenido del resumen alternativo son decisiones del equipo (CL-05, CL-08). No guardar prompts íntegros como evidencia si contienen información sensible. Los datos de demostración serán ficticios.
