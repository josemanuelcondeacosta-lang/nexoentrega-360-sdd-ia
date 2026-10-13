# Preparación común y publicación del aporte G1

Necesitas Git, VS Code, Python 3.11 y tu cuenta personal de GitHub con acceso al repositorio. Conde José gestiona el acceso si falta. No compartir credenciales ni atribuir trabajo a otra persona.

## Abrir el proyecto

En terminal PowerShell de VS Code, para un clon nuevo:

```powershell
git clone https://github.com/josemanuelcondeacosta-lang/nexoentrega-360-sdd-ia.git
cd nexoentrega-360-sdd-ia
git switch docs/g1-specifications
git pull --ff-only
git rev-parse HEAD
code .
```

Si ya tienes clon, empieza con `git status`, conserva cambios propios y actualiza la rama sin reset destructivo. Anota SHA antes de revisar.

```powershell
python --version
python scripts/export_specs.py --check
python -m unittest discover -s scripts -p 'test_*.py' -v
[DateTime]::UtcNow.ToString('yyyy-MM-ddTHH:mm:ssZ')
```

Estas son comprobaciones documentales, no pruebas de aplicación. Registra tu resultado real, incluso si falla. Para registrar salida en tu archivo de evidencia, copia el texto de la terminal con comando y contexto, sin secretos.

## Crear aporte individual

Ejecuta **solo la línea correspondiente a tu nombre**, sobre `docs/g1-specifications` actualizado:

```powershell
git switch -c docs/g1-carolina-review
```

```powershell
git switch -c docs/g1-edier-review
```

```powershell
git switch -c docs/g1-vega-review
```

En VS Code crea `docs/reviews/aportes/` y dentro tu archivo individual indicado en la guía. Usa la plantilla siguiente y completa solo lo ocurrido:

```text
Nombre / cuenta GitHub:
Autor del artefacto revisado / asistencia IA:
SHA completo revisado:
Fecha UTC:
Fuentes disponibles (archivo/versión/página); fuentes pendientes:
SPEC / AC revisados:
Comprobación concreta y resultado observado de revisión documental:
Hallazgo, evidencia, corrección propuesta o pregunta pendiente:
Comandos ejecutados / entorno / salida real:
Casos de producto previstos: datos, acción y esperado:
Resultado observado de producto: no ejecutado
Decisión por SPEC: pendiente / requiere cambios / aprobada con evidencia
Enlace a comentario o revisión GitHub:
PR ajeno revisado / SHA / comentario propio: pendiente hasta realizarlo
```

Antes del commit comprueba `git config user.name` y `git config user.email`. Si son incorrectos, configura **tus** datos reales en tu clon; no usar identidad de compañeros. Añade solo tu archivo, según corresponda:

```powershell
git add docs/reviews/aportes/carolina-G1.md
```

```powershell
git add docs/reviews/aportes/edier-G1.md
```

```powershell
git add docs/reviews/aportes/vega-G1.md
```

Después:

```powershell
git diff --cached --check
git diff --cached
git commit -m "docs(g1): registrar aporte documental y evidencia propia"
git push -u origin HEAD
```

En GitHub abre **Compare & pull request**: base `docs/g1-specifications`, compare tu rama. En descripción identifica tarea, archivos, comandos realmente ejecutados, SHA revisado, asistencia IA y pendientes. Pide revisión a la persona indicada en tu guía. Los números de estos PR se conocerán solo al crearlos.

Mantén PR #1 en borrador. Las revisiones preliminares se registran mediante **Files changed → Review changes → Comment**; no marcan G1 aprobado. No cambies a Ready ni hagas merge. Cuando haya correcciones, revisa el nuevo SHA. El registro G1 definitivo espera cotejo oficial y revisión humana independiente.
