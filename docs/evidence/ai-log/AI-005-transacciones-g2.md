# Intervención de IA

Formato del anexo oficial `08_ai-log-template.md`.

- **ID y fecha UTC:** AI-005, 2026-10-14T17:00Z a 17:10Z (aprox.)
- **Autor:** Castro Edier (202311254074)
- **Etapa:** diseño (G2)
- **Herramienta y modelo conocido:** Claude (Anthropic), modelo Claude Opus 5.5, en claude.ai
- **RF y AC:** RF02, RF04–RF07, RF09, RNF02; AC-03-01 a AC-03-16, AC-04-03, AC-04-08
- **Contexto y versiones de archivos:** rama `docs/g2-architecture` en `e63ebf5` (ADR-001 y `contracts/openapi.yaml` de José Conde, ADR-004 de Vega); SPEC-03 0.3-draft; anexos `acceptance-oracles.md` y `fixture-scenarios.json`; hallazgos H-E01 a H-E03 de `docs/reviews/aportes/edier-G1.md`

## Objetivo

Preparar la parte de Edier en G2: ADR-002 (transacciones, concurrencia e idempotencia), modelo de datos, diagrama de estados y secuencia de reserva concurrente, alineados con ADR-001 y el contrato OpenAPI.

## Prompt enviado

> soy edier, qué tengo que hacer?

Seguido de «si» para empezar por la parte de G2. Decisión de Edier antes de redactar, ante una pregunta de la IA: control de concurrencia mediante **bloqueo pesimista** (`SELECT … FOR UPDATE`).

## Salida relevante

- `docs/adr/ADR-002-transacciones-idempotencia.md`: compara cuatro estrategias de concurrencia y tres almacenes de idempotencia; decide bloqueo pesimista en orden fijo, tabla de idempotencia en la misma transacción con bloqueo consultivo por clave, retención de 24 h y restricciones `CHECK` en la base. Resuelve H-E01 (fallo antes del commit → 500 sin registro), H-E02 (carrera despacho/cancelación) y H-E03 (409 guardado).
- `docs/architecture/datos.md`: diagrama entidad-relación de 9 tablas con restricciones e índices.
- `docs/architecture/estados.md`: diagrama de estados, matriz de transiciones (permitidas y 409) y efectos con el oráculo 10/7/3.
- `docs/architecture/secuencia-reserva-concurrente.md`: secuencia de dos reservas simultáneas con resultado final 10/7/3.
- Observación H-E05 sobre el contrato: las descripciones de `dispatch`, `deliver` y `cancel` mencionan una clave de idempotencia que esas operaciones no usan.

## Decisión humana

Registrada por Edier tras revisar cada archivo:

| Cambio | Decisión de Edier (aceptada / corregida / descartada) | Motivo | Fecha UTC |
|---|---|---|---|
| ADR-002 | Aceptada | En la reserva, el paso 10 comprueba todas las líneas antes de escribir y el paso 11 solo actualiza si todas cumplen: coincide con «todas o ninguna» (RF05, p. 4) | 2026-10-14 |
| Modelo de datos | Aceptada | `inventory_items` tiene `reservado <= fisico` y ambos `>= 0` | 2026-10-14 |
| Diagrama de estados | Aceptada | Cancela solo el Solicitante dueño y la entrega la registra el Coordinador, igual que la matriz del §2 (p. 3) | 2026-10-14 |
| Secuencia de reserva concurrente | Aceptada | Resultado final 10 / 7 / 3, igual que `acceptance-oracles.md` | 2026-10-14 |

## Verificación

Ejecutada por el asistente en su espacio de trabajo el 2026-10-14:

- Los tres diagramas Mermaid se renderizaron sin errores con `@mermaid-js/mermaid-cli` 11.4.2. El primer intento de la secuencia falló porque los `;` dentro de los mensajes se interpretan como separadores; se reemplazaron por comas.
- Las cantidades del diagrama de estados y de la secuencia coinciden con `acceptance-oracles.md` (10/7/3, 10/0/10 y 3/0/3).
- Rutas, cabecera `Idempotency-Key` y política 200/409 comparadas con `contracts/openapi.yaml` (`e63ebf5`).

Verificación humana pendiente: Edier debe revisar los diagramas en GitHub y comprobar las reglas contra la p. 4 del enunciado.

## Límites

Nada se implementó ni se ejecutó contra PostgreSQL. El comportamiento de `FOR UPDATE` en READ COMMITTED y el reintento de deadlocks de Laravel deben confirmarse con las pruebas de concurrencia. Sin datos reales ni secretos.
