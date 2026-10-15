# Cotejo del documento consolidado con la entrega oficial

Fuente: instrucciones de entrega aportadas en captura por el coordinador y anexo oficial `09_pdf-delivery-template.md`. Este cotejo verifica el contenido del documento; no aprueba la aplicación ni sustituye la rúbrica completa.

| Contenido solicitado en el aula | Desarrollo en el documento | Estado y límite |
|---|---|---|
| Portada con equipo e integrantes | Portada NexoEntrega 360, cuatro nombres/códigos y fecha | Incluido. Nombre exacto del curso y proyecto integrador de origen pendientes de confirmar para el anexo 09 |
| Cinco historias y especificaciones | Apartado 2: cinco historias, entradas, proceso, salidas, reglas y 73 AC, versión/estado y fuentes | Incluido; revisión humana por alcance/SHA, no aprobación global |
| Arquitectura, modelo y contratos | Apartado 3: seis diagramas, entidades, cuatro ADR justificados y operaciones OpenAPI | Incluido; revisión de arquitectura actualizada pendiente |
| Bitácora IA con decisiones y verificaciones humanas | Apartado 4: intervenciones originales, contexto, prompts y salidas conservados; apartado 9: decisiones publicadas y sus límites | Incluido con evidencia existente; no se inventan firmas ni verificaciones personales |
| Trazabilidad, SPEC versus código y riesgos | Apartados 5/6: matriz por AC dentro del documento, código/pruebas/reportes/limitaciones y riesgos con controles | Incluido; variantes y riesgos residuales pendientes se identifican |
| Pruebas, CI, ejecución y aceptación | Apartados 5/7/8: esperado/observado, entorno, comandos, CI, runbook, recuperación y estado de aceptación | Incluido; acta de aceptación final pendiente y limitaciones del clon explícitas |
| Aporte de cada integrante, enlaces y versión final | Apartados 1/9: tabla por persona con artefactos, PR, revisión ajena y evidencia; SHA de fuentes y código probado | Incluido para la candidata; versión final aceptada, baseline-v1 y release-examen pendientes de cierre real |

## Entrega en el aula

Cada integrante debe adjuntar **el mismo PDF consolidado del equipo**, dentro del plazo de la actividad. Nombre indicado por el coordinador: `Examen_SDD_IA_Equipo_Jose_Conde_Jose_Vega_Carolina_Cantillo_Edier_Castro.pdf`. Antes de enviarlo, los cuatro deben comprobar el mismo SHA-256 contra el manifest de la edición aceptada. El documento debe desarrollar el contenido; los enlaces solo lo complementan.

No se requiere sustentación ni video. Las notas del sitio interactivo no constituyen la entrega oficial. No se incorpora un cambio de requisitos del día 4. El plazo exacto no consta en la captura aportada: consultarlo en el aula, sin inventar una fecha.

## Identificación y acceso pendientes

El repositorio es `https://github.com/josemanuelcondeacosta-lang/nexoentrega-360-sdd-ia`. Verificar acceso de la docente antes de enviar. La URL `http://127.0.0.1:8000` solo funciona en el equipo donde se ejecuta; no es una URL pública para evaluación remota. Usar el runbook y el repositorio con acceso concedido, o documentar la URL de despliegue cuando exista. No se ha comprobado acceso desde la cuenta de la docente.

Esta edición es candidata. El SHA de fuentes del documento y el SHA del código probado se indican por separado; el commit que contiene los binarios puede ser posterior. `baseline-v1`, `release-examen`, SHA final aceptado y fecha/decisión del acta solo se completarán cuando realmente existan. Los hashes del PDF/Word están en `deliverables/candidato/manifest.json`; no se confunden con el SHA de Git.

## Condiciones que no se pueden completar solo redactando

Faltan decisiones humanas independientes sobre SPEC-02/03 y correcciones de arquitectura/documentación; revisión personal ajena del coordinador; evidencia de participación pendiente de cada persona y verificaciones detalladas por AC. La implementación adelantada sigue registrada como brecha de proceso y no puede convertirse retrospectivamente en revisión previa.

Las 49 pruebas de backend, cuatro E2E, carreras, carga y restauración ejecutadas son evidencia técnica del corte identificado; no acreditan aceptación integral ni todos los requisitos. La matriz mantiene los casos pendientes. El PDF candidato no debe presentarse como entrega final ya aceptada.
