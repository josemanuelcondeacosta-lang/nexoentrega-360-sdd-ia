# Constitución del Proyecto — NexoEntrega 360

* **Última actualización:** 2026-10-14
* **Responsable de revisión:** Carolina Cantillo
* **Ajustes de hallazgos:** R-C01 (Gobernanza de especificaciones) y R-C02 (Reglas de revisión humana e independencia de roles).

## 1. Principios de Spec-Driven Development (SDD)
1. Ninguna funcionalidad se programa sin una especificación previa (SPEC v0.4 o superior) aprobada.
2. Cada SPEC debe contar con una revisión humana independiente firmada con autor, fecha UTC y SHA.

## 2. Gobernanza y Flujo de Trabajo (R-C01 / R-C02)
- El autor de una SPEC o módulo no puede actuar como su propio revisor independiente.
- Todo cambio en los criterios de aceptación (AC) requiere la actualización explícita en la matriz de trazabilidad y la re-ejecución de scripts de verificación.
- La incorporación a main requiere la revisión y aprobación de los PRs correspondientes por parte del equipo.