# Revisión G2 — paquete parcial de arquitectura

Actualización de coordinación: Carolina aprobó plan/tareas del SHA c70e431; posterior ajuste de rangos AC requiere revisión. Paquete de cuatro ADR/diagramas/contrato aún requiere decisiones independientes de Edier/Vega. Ver `coordinacion-cierre-G1-G2.md`. No usar las tablas históricas posteriores como decisión del corte actual.

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

## Actualización de ubicación: consolidación documental

Actualización técnica posterior: fragmento IA v0.2.0 de Vega integrado; CSRF uniforme y 403/404 del historial corregidos; plan/tareas alineados con datos y dependencias. Ver evidencia de correcciones y revisión completa. Estado: corregido documentalmente, **revisión humana y pruebas de producto pendientes**. La frase «R-V01/02 siguen abiertos» del corte original ya no describe ausencia de corrección, sino pendiente de validación humana/implementación real.

Los estados de la tabla anterior describen el paquete parcial original. Esta rama reúne ahora ADR-002 de Edier, ADR-003 y plan/tareas de Carolina, datos/estados/secuencia reserva y estrategia actualizada de Vega. Se conservan fuentes y autorías sin presumir aprobación de sus versiones nuevas. Detalle y revisiones humanas encontradas: `docs/reviews/revision-completa.md`. R-V01/02 siguen abiertos; R-V03 tiene copia de estrategia corregida. No cerrar G2 globalmente.
