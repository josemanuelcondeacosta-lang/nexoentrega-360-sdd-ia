# Material oficial del examen

Carpeta preparada para recibir el enunciado y sus anexos. **No contiene todavía documentos oficiales.** El resumen del coordinador y la misión de trabajo están disponibles como contexto, pero no sustituyen la fuente oficial.

Consultar [inventario](inventario.md) y [cotejo preparado](../reviews/cotejo-oficial-G1.md).

## Recepción

1. Facilitar el enunciado íntegro original (PDF/DOCX; si es una página del aula, exportación completa con identificación).
2. Facilitar todos los anexos referenciados, conservando nombres y versiones. No se conoce aún su número ni sus nombres exactos.
3. Facilitar correcciones y respuestas oficiales del docente, si existen; indicar expresamente cuando no existan documentos adicionales.
4. Guardar los archivos aquí conservando los originales. No sobrescribir versiones: usar subcarpetas de versión si sus nombres coinciden.
5. Registrar en inventario.md nombre, procedencia, versión/fecha, ruta, hash SHA-256 y estado de recepción. Un hash identifica el archivo; no demuestra su procedencia oficial.
6. Revisar condiciones de distribución antes de subir originales a GitHub. Pueden compartirse con el equipo por el canal permitido y registrarse mediante referencia si no corresponde publicarlos.

PowerShell, sustituyendo el nombre por el archivo recibido:

```powershell
Get-FileHash -LiteralPath 'docs/examen-oficial/nombre-original.pdf' -Algorithm SHA256
```

No cambiar el estado a «recibido» hasta comprobar que el archivo existe, se abre y contiene todas sus páginas. La recepción no equivale a cotejo ni a aprobación humana de G1. Mantener las cinco SPEC pendientes hasta revisión válida.
