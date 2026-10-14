# Coordinación de la entrega

Paquete actual: provisional; PR #12 en borrador. Cualquier aprobación debe identificar el SHA revisado y venir de la persona que revisó.

| Responsable propuesto | Acción ejecutable | Evidencia requerida |
|---|---|---|
| Conde José | Abrir runbook, levantar Docker, recorrer desde solicitud hasta entrega y probar rechazo; revisar aporte ajeno | Comentario propio con SHA, pasos, resultado y decisión; PR de su corrección o prueba real |
| Carolina | Contrastar formularios, estados y errores contra las cinco SPECS, comprobar teclado y 390/1366 | Hallazgos reproducibles y revisión personal del SHA; prueba/corrección propia |
| Edier | Revisar bloqueos, transacciones, reserva parcial, idempotencia/TTL, aislamiento y restauración | Tests PostgreSQL adicionales, resultados y revisión del código de otro integrante |
| Vega | Completar LLM real y validación estricta con evidencias, revisar seguridad/CI/carga | PR con pruebas de salida válida/inválida, fallo/fallback, carga y CI real; revisión cruzada |

1. Publicar correcciones en PR separados; cada integrante aporta código o pruebas y revisa un aporte ajeno, sin autoaprobarse.
2. Registrar cierre G1/G2 y decisiones sobre SHA exactos. Resolver la brecha de proceso ante el equipo; no inventar revisión previa.
3. Ejecutar en entorno limpio dedicado el mismo candidato: suite PostgreSQL, E2E, concurrencia, validación IA real, carga exigida, dependencias/secretos, backup/restauración y aceptación manual. Guardar resultados, entorno y fallos.
4. Completar comparación de los 73 AC contra código y evidencia. Un enlace a una prueba planificada no acredita que haya pasado.
5. Regenerar el PDF común desde el candidato: cinco historias/SPECS, arquitectura/modelo/contratos, IA/verificación humana, trazabilidad/riesgos, pruebas/CI/ejecución/aceptación y contribuciones reales. Revisar el contenido completo con anexo 09 y la rúbrica.
6. Solo tras aceptación real, registrar SHA final, crear y publicar release-examen apuntando a ese commit; comprobar remotamente el tag. Verificar hash SHA-256 del PDF y que los cuatro adjunten el mismo archivo Examen_SDD_IA_Equipo_NexoEntrega360.pdf al aula.

Generación provisional reproducible: instalar `docs/delivery/requirements-pdf.lock` y ejecutar `python scripts/export_delivery_pdf.py --sha <SHA-candidato>`. El documento se rotula provisional. No sustituye la revisión final ni publica nada en el aula.
