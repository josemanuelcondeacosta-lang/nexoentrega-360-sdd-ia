# ADR-004 Integración del asistente de IA y degradación

Formato del anexo oficial `06_adr-template.md`.

- **Estado:** propuesto
- **Fecha y versión:** 2026-10-14, v0.1 (G2)
- **Autor:** Vega José, con asistencia de IA (registro AI-004)
- **Depende de:** ADR-001 (estructura y tecnología; el equipo acordó Laravel con PostgreSQL el 2026-10-14; falta registrarlo en ADR-001) y ADR-003 (identidad y sesión)

## Contexto y restricciones

El coordinador pide un resumen de hasta 20 solicitudes ENVIADA de su organización (RF10). La salida del modelo debe cumplir `contracts/ai-output.schema.json` y la validación de pertenencia (RF11). El resumen es un borrador que solo el coordinador aprueba, y aprobarlo no cambia inventario, estado ni prioridad (RF12). Restricciones vinculantes:

- Plazo de 5 s; salida inválida, timeout o fallo del proveedor devuelven un mensaje controlado y un resumen determinista alternativo (RF12).
- El proveedor recibe solo requestId, zona, prioridad, fecha requerida, estado y observaciones (RF10).
- El modelo no recibe capacidad de escribir inventario ni de invocar operaciones privilegiadas; un prompt que lo prohíbe no basta (§10).
- CI usa fixtures deterministas, sin servicio de pago (RNF06, AC-05-14).
- Debe demostrarse una salida de un LLM real que pase por el mismo adaptador y validador (§10). El equipo eligió una **API con plan gratuito**.
- Las observaciones son datos no confiables (`untrusted-observation.txt`, OWASP LLM01).

## Alternativas consideradas

**Arquitectura de la integración**

| Opción | Descripción | Ventajas | Desventajas |
|---|---|---|---|
| A. Llamada directa | El controlador llama al proveedor y muestra la respuesta | Poco código | No se puede probar sin red; acopla al proveedor; validación dispersa; incumple «proveedor intercambiable» (§10) |
| **B. Adaptador + validador + fallback** | Interfaz `AiProvider` con varias implementaciones; un servicio orquesta proyección, plazo, validación y fallback | Intercambiable; CI con fixtures; validación en un único punto; degradación controlada | Más clases y pruebas |
| C. Llamada desde el navegador | El frontend llama al proveedor | Sin carga en el servidor | Expone la clave; el servidor no valida pertenencia; descartada por seguridad |

**Origen de la salida de LLM real**

| Opción | Ventajas | Desventajas |
|---|---|---|
| **API con plan gratuito** (elegida por el equipo) | Invocación real y demostrable; sin costo | Requiere clave en `.env`; cuotas y errores 429; los planes gratuitos cambian |
| Modelo local (Ollama) | Sin clave ni cuota | Instalación pesada; depende del hardware de cada integrante |
| Reproducción declarada | Reproducible y sin clave | No demuestra invocación en vivo |

**Proveedor de la API gratuita.** Gemini (modelos Flash del plan gratuito de Google AI Studio) o Groq. Ambos ofrecían plan gratuito en 2026, pero sus límites cambian: se comprueban en la consola del proveedor al implementar y no se fijan en el código.

## Criterios de elección

1. Cumplir RF10–RF12 y RNF06 con pruebas deterministas.
2. Seguridad: clave fuera del repositorio y del navegador; validación en el servidor.
3. Demostrar una salida real sin depender de ella para que el sistema funcione.
4. Mínima complejidad que cumpla lo anterior.

## Decisión y justificación

Se adopta la **opción B** con estas piezas (nombres orientativos en Laravel):

| Pieza | Responsabilidad |
|---|---|
| `AiProvider` (interfaz) | `complete(string $prompt, int $timeoutMs): string`; devuelve el texto crudo o lanza `AiTimeoutException` / `AiProviderException` |
| `GeminiProvider` / `GroqProvider` | Cliente HTTP de Laravel con `timeout(5)`; clave leída de `config('ai.key')` (variable `AI_API_KEY` en `.env`) |
| `FixtureProvider` | Devuelve el `rawOutput` o simula el `transport` de `ai-provider-fixtures.json`; lo usa CI |
| `ReplayProvider` | Devuelve una respuesta real capturada y guardada en `evidence/ai-replay/` con entrada, salida, modelo, fecha y origen |
| `NullProvider` | Sin proveedor configurado: lanza `AiProviderException` para activar el fallback |
| `AiOutputValidator` | 1) JSON estricto; 2) esquema `ai-output.schema.json`; 3) en código: cada `requestId` pertenece a la selección y no se repite |
| `FallbackSummarizer` | Resumen determinista con conteos por prioridad y zona y la fecha requerida más próxima; `recommendations` vacío (CL-08) |
| `AiSummaryService` | Orquesta: autoriza Coordinador, valida selección, proyecta seis campos, construye el prompt, llama con plazo, valida, guarda borrador u origen fallback |

El proveedor se elige con `AI_PROVIDER=gemini|groq|replay|fixture|none` en `.env`. El valor por defecto en `.env.example` es `none`, así que la demo funciona sin clave usando el fallback.

**Uso del LLM real.** La API gratuita se usa en una demostración manual. La respuesta obtenida se guarda además como captura de reproducción para que la evidencia sea repetible aunque la cuota o la clave no estén disponibles cuando el docente evalúe. La captura se declara como reproducción, sin afirmar una invocación en vivo (§10).

**Reglas de validación de la selección** (orden propuesto en H-V02, CL-19): formato de la lista 1–20 IDs únicos (422) → pertenencia a la organización (404) → estado ENVIADA (409). El proveedor no se invoca si falla cualquiera.

**Construcción del prompt.** Instrucciones fijas del sistema y, separado, un bloque JSON con los seis campos. Las observaciones van dentro de ese bloque como datos. Se pide responder solo JSON con el formato del esquema y, si el proveedor lo permite, se activa su modo de salida JSON. Esto reduce errores, pero **no sustituye** la validación en código.

**Plazo.** El cliente HTTP corta a los 5 s. La prueba usa reloj controlado y una tolerancia máxima propuesta de 250 ms (H-V01, por confirmar en el plan de pruebas).

**Persistencia (CL-05).** Tabla de resúmenes: id, organización, coordinador, IDs seleccionados, summary, recommendations, origen (tipo, proveedor, modelo, fixture), estado BORRADOR o APROBADO, aprobado por y fecha UTC. Tabla de intentos: fecha, origen, resultado (válido, inválido, timeout, error) y motivo sanitizado; nunca el prompt con secretos ni la clave.

**Aprobación.** En una transacción: estado APROBADO, aprobador, fecha UTC y evento de auditoría con los campos de RF09. Un resumen alternativo también se puede aprobar, conservando su origen (H-V03). Aprobar dos veces devuelve 409 sin segundo evento (H-V04).

**Presentación.** `summary` y `reason` se muestran con escape de Blade (`{{ }}`), nunca con `{!! !!}`. La pantalla etiqueta el borrador como «Asistencia de IA» y muestra su origen.

## Consecuencias y riesgos

- (+) CI y la demo funcionan sin red ni clave (RNF06); el flujo principal no depende de la IA.
- (+) Cambiar de proveedor solo requiere otra implementación de `AiProvider`.
- (−) Más código y pruebas que una llamada directa.
- Riesgo: cuota agotada o error 429 del plan gratuito → fallback controlado; evidencia real en la captura de reproducción.
- Riesgo: fuga de la clave → solo en `.env` (ignorado por Git), detección de secretos en CI (CI-11) y nunca en logs.
- Riesgo: el plan gratuito puede usar los datos enviados para mejorar el servicio → solo se envían datos ficticios del examen.
- Riesgo: inyección de instrucciones → el modelo no tiene herramientas; la salida se valida contra el esquema y la selección; las acciones solo ocurren por operaciones normales del coordinador.

## RF y AC afectados

RF02, RF09, RF10, RF11, RF12, RNF01, RNF06; AC-05-01 a AC-05-14; hallazgos H-V01 a H-V04 y H-V07.

## Verificación de la decisión

| Prueba prevista | AC | Cómo |
|---|---|---|
| Payload solo con seis campos y la selección | AC-05-01 | Proveedor espía que guarda el prompt recibido |
| Siete fixtures oficiales | AC-05-02, 03, 06, 07, 08, 14 | `FixtureProvider` con cada id; resultado igual a su campo `expected` |
| Selección inválida no invoca al proveedor | AC-05-05 | Proveedor espía con contador de llamadas en 0 |
| Plazo de 5 s | AC-05-03 | Reloj controlado; corte ≤ 5 s + tolerancia |
| Marcado escapado con salida válida | AC-05-08 (V-09b) | Prueba de vista: el HTML contiene `&lt;script&gt;` |
| Aprobación sin efectos de negocio | AC-05-04, 05-12 | Snapshot de solicitudes e inventario antes y después |
| Salida real por el mismo validador | AC-05-13 | `ReplayProvider` con la captura real |
| CI sin red ni clave | AC-05-14 | El workflow no define `AI_API_KEY` y usa `AI_PROVIDER=fixture` |

## Autor y revisión humana

- Autor: Vega José (asistido por IA, AI-004).
- Revisión humana: **pendiente** de una persona distinta del autor en la revisión G2.
