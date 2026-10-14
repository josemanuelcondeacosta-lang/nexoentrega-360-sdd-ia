# Material oficial del examen

Esta carpeta contiene el enunciado y los anexos **descargados de la plataforma oficial del examen** (sección «05 Plantillas y anexos»), sin modificaciones y con sus nombres originales. La plataforma indica que estos archivos se descargan «para incorporarlos al repositorio del equipo».

| Carpeta / archivo | Contenido | Tipo |
|---|---|---|
| `originales/` | `Examen_Estudiantes_SDD_IA.pdf` y `Anexos_Estudiantes_SDD_IA.zip`, tal como se descargaron | **Oficial** (archivo emitido por el docente) |
| `anexos/` | Los 27 archivos extraídos del ZIP, sin cambios de nombre ni contenido | **Oficial** (extracción byte a byte del ZIP) |
| `SHA256SUMS.txt` | Hash SHA-256 de cada archivo de `originales/` y `anexos/` | Nota del equipo (verificación) |
| `inventario.md` | Registro de recepción, procedencia, hash e integridad | Nota del equipo |

No hay transcripciones en esta carpeta: el PDF descargado contiene el enunciado íntegro (15 páginas) y la plataforma declara que «el PDF contiene el mismo enunciado». Cualquier transcripción, resumen o captura futura debe guardarse en `transcripciones/` e identificarse como tal.

## Reglas de uso

1. No editar ni renombrar archivos de `originales/` o `anexos/`. Si el docente publica una versión nueva, guardarla en una subcarpeta con fecha (`v2026-10-XX/`) sin sobrescribir.
2. Copiar a `contracts/`, `seeds/` o `docs/` solo **copias** de trabajo de los anexos; el original permanece aquí.
3. Verificar integridad desde la raíz del repositorio:

```sh
cd docs/examen-oficial && sha256sum -c SHA256SUMS.txt
```

En PowerShell: `Get-FileHash -LiteralPath 'docs/examen-oficial/originales/Examen_Estudiantes_SDD_IA.pdf' -Algorithm SHA256`.

Un hash identifica el archivo; la procedencia oficial la acredita la plataforma del examen. La recepción no equivale a cotejo ni a aprobación G1: ver [inventario](inventario.md) y [cotejo oficial](../reviews/cotejo-oficial-G1.md).
