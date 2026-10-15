# Acta provisional: entrega no aceptada como final

La implementación de esta rama se adelantó por instrucción expresa del coordinador «haz esto con lo que hay, después corrijo». Esto no equivale a aprobación G1/G2 ni elimina la exigencia de revisión previa del examen. La brecha de proceso se mantiene visible y el código requiere revisión independiente antes de integración.

Alcance candidato: frontend y API locales para ocho cuentas ficticias, solicitudes, flujo de stock, consultas y auditoría; integración de los PR #13–15 en la rama candidata del PR #16. El asistente incluye proveedor, validación estricta, fallback y replay declarado de la captura original de Vega. No se atribuye una nueva llamada a LLM ni cumplimiento de 5s a la captura de 18,9s.

Estado de aceptación: **PROVISIONAL / NO ACEPTADO COMO ENTREGA FINAL**. El informe de ejecución consigna qué pruebas se ejecutaron y sus resultados. Revisor independiente, fecha de aceptación, firmas y decisiones finales: pendientes.

Resultados nuevos: 49 pruebas PHP, cuatro E2E, 20 rondas por carrera, carga de 2.000 solicitudes, clon remoto cronometrado y restauración comparada. Evidencia, SHA y límites en `docs/evidence/runtime/candidate/verification.md`. Brechas: variantes por AC señaladas en `ac-evidence.csv`, revisión independiente de SPEC-02/03, arquitectura y correcciones documentales, inspección manual y contribuciones personales pendientes de acreditar. No generar aprobación ficticia ni tag release-examen para esconder brechas.

El PDF consolidado provisional permite revisar el estado; los cuatro deben entregar **el mismo PDF final** después de corregir y verificar esas brechas, no cuatro versiones distintas.
