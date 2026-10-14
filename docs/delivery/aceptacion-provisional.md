# Acta provisional: entrega no aceptada como final

La implementación de esta rama se adelantó por instrucción expresa del coordinador «haz esto con lo que hay, después corrijo». Esto no equivale a aprobación G1/G2 ni elimina la exigencia de revisión previa del examen. La brecha de proceso se mantiene visible y el código requiere revisión independiente antes de integración.

Alcance: frontend y API locales para ocho cuentas ficticias, solicitudes, flujo de stock, consultas y auditoría. Asistente actualmente con fallback determinista declarado; no se atribuye una invocación a LLM real.

Estado de aceptación: **PROVISIONAL / NO ACEPTADO COMO ENTREGA FINAL**. El informe de ejecución consigna qué pruebas se ejecutaron y sus resultados. Revisor independiente, fecha de aceptación, firmas y decisiones finales: pendientes.

Brechas antes de release-examen: proveedor/reproducción real y validación completa IA, pruebas por todos los AC, carga exigida, escenarios adicionales de concurrencia/fallos/TTL, restauración y aceptación manual, aportes personales de código/pruebas de todos, convergencia y revisión humana final. Ya existen suite PostgreSQL, E2E responsive, un escenario concurrente, revisión automatizada limitada de secretos/dependencias y CI remoto correctos sobre el candidato indicado en verification.md; no prueban todos los requisitos. No generar aprobación ficticia ni tag release-examen para esconder brechas.

El PDF consolidado provisional permite revisar el estado; los cuatro deben entregar **el mismo PDF final** después de corregir y verificar esas brechas, no cuatro versiones distintas.
