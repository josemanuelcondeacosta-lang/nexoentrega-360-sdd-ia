# Revisión G2 — paquete parcial de arquitectura

Estado: **PROPUESTO, SIN APROBACIÓN**. Rama `docs/g2-architecture`, basada en `origin/docs/g2-vega-ia` (`a13aa17`); no se fusionan PR G1 pendientes. G1 todavía pendiente por SPEC. Diseño documental permitido; implementación no iniciada.

| Artefacto | Estado / revisor propuesto |
|---|---|
| ADR-001 | Preparado por Codex; Edier revisa límites y transacciones, Vega seguridad |
| Contexto y componentes Mermaid | Preparados; revisar actores, fronteras y dependencias |
| OpenAPI integrado | Generado y validado documentalmente; Edier revisa base y Vega conservación del contrato IA |
| ADR-004 y secuencia IA | Se conservan desde rama de Vega; no se certifica su aprobación |
| ADR-002 y ADR-003 | Pendientes; no creados por este paquete |
| ERD, estados y secuencia de reserva | Pendientes en otras partes de G2; no creados por este paquete |
| Plan/tareas G2 completos | Pendientes del equipo; plan de colaboración no los sustituye |
| Constitución G1 | Corrección D-21 preparada; Carolina debe revisar |

## Lista de revisión

- [ ] Revisar ADR-001 frente a restricciones y alternativas; acordar versiones y estructura de módulos.
- [ ] Comprobar cada permiso contra examen y respuestas CL-16/17/18; evitar ampliar acceso sin decisión.
- [ ] Comprobar operación de cada transición, atomicidad y replay; cerrar ADR-002.
- [ ] Concretar sesión/CSRF común para base e IA, hash y expiración en ADR-003.
- [ ] Revisar schemas, paginación, filtros, errores, correlationId, ejemplos y rutas health/ready diferenciadas.
- [ ] Verificar que rutas/componentes IA coincidan con fragmento original y campos oficiales.
- [ ] Revisar Mermaid en visor y anotar resultado real; no hay render certificado por este paquete.
- [ ] Completar otros ADR, diagramas, plan y tareas pendientes; este paquete no constituye G2 completo.
- [ ] Registrar autor, revisor independiente, fecha UTC, SHA completo, observaciones, decisiones y enlaces reales.

Autor del paquete: Codex bajo solicitud de Conde José. Revisor humano: PENDIENTE. UTC/SHA revisado: PENDIENTE. Observaciones/decisión: PENDIENTE. No atribuir estos cambios a una contribución personal realizada por el coordinador.
