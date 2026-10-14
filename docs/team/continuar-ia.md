# Continuar la integración de IA

Entrega preparada para Vega José, según reparto propuesto del equipo. No acredita trabajo ni revisión ya realizados por él.

## Base compartida

Repositorio: https://github.com/josemanuelcondeacosta-lang/nexoentrega-360-sdd-ia

La aplicación está en **feat/provisional-frontend-integration**, PR #12 en borrador. No partir de main: todavía no contiene esta integración. Código base comprobado antes de este traspaso: 9949787241120369e741d4ebf746f978678c11a4. Hacer fetch para obtener también esta guía. Las revisiones humanas pendientes siguen vigentes; este traspaso no aprueba G1/G2.

## Empezar en VS Code

Para una copia nueva, en la terminal:

```powershell
git clone https://github.com/josemanuelcondeacosta-lang/nexoentrega-360-sdd-ia.git
cd nexoentrega-360-sdd-ia
git fetch origin
git switch -c feat/ia-vega origin/feat/provisional-frontend-integration
code .
docker compose up -d --build
```

Si ya existe una copia con trabajo propio, revisar `git status` y guardar esos cambios antes de cambiar de rama; no usar reset ni borrar archivos. La rama feat/ia-vega es propuesta: si ya existe, continuarla o elegir otro nombre sin sobrescribirla.

Abrir http://127.0.0.1:8000. Coordinador: org-a-coordinador@example.test, contraseña ficticia DemoNexo360!; segunda organización org-b-coordinador@example.test. Para el asistente seleccionar solicitudes ENVIADA. El fallback actual no es un LLM real.

## Leer antes de cambiar código

- specs/SPEC-05.md y los requisitos oficiales RF10–RF12, RNF01/RNF06 y §10 del PDF oficial.
- docs/adr/ADR-004-integracion-ia-degradacion.md.
- contracts/openapi-ia.yaml y contracts/ai-output.schema.json.
- docs/examen-oficial/anexos/ai-provider-fixtures.json y untrusted-observation.txt.
- apps/nexo/app/Modules/AI/AiController.php: hoy crea solamente fallback determinista.
- apps/nexo/public/app.js: sección Asistente, visualización y aprobación.
- apps/nexo/tests/Feature/NexoFlowTest.php y .github/workflows/aplicacion-provisional.yml.

## Entregable concreto

1. Proponer proveedor intercambiable y validador, manteniendo rutas y contratos. Publicar revisión independiente de la SPEC/decisiones aplicables antes de incorporar la funcionalidad; registrar los cambios de alcance en lugar de inventar aprobación previa.
2. Preparar salida de un LLM real mediante API, modelo local o reproducción declarada de una respuesta real. Si se usa replay, guardar contexto ficticio, respuesta, origen/modelo conocido y fecha; pasarlo por el mismo adaptador/validador. No presentar fixtures escritos a mano como salida real.
3. Enviar únicamente requestId, zona, prioridad, fechaRequerida, estado y observaciones de 1–20 solicitudes ENVIADA seleccionadas de la organización autenticada. No enviar nombres, contraseñas ni sesiones.
4. Validar estrictamente: summary hasta 600 caracteres; hasta cinco recomendaciones; IDs únicos del conjunto seleccionado; reason hasta 240; evidenceFields con 1–3 claves permitidas; sin propiedades extra. Presentar texto escapado, sin ejecutarlo.
5. Cortar la llamada a los 5 segundos y responder de forma controlada con fallback ante salida inválida, error o timeout. Mantener origen, intento persistido y aprobación del Coordinador con auditoría, sin escribir solicitudes ni inventario.
6. Completar frontend de resumen/recomendaciones y estados de carga/error/aprobación de acuerdo con el contrato. Claves de proveedor solo en entorno de backend: nunca en JavaScript, Git ni evidencias. .env.example solo contiene nombres y valores vacíos de credenciales.
7. Añadir pruebas deterministas de respuesta válida, JSON inválido, extras, ID ajeno, inyección, timeout, fallo, permisos y aprobación sin efectos de stock. CI no debe depender de una API pagada o de red externa de IA.

## Comprobar y publicar

Seguir docs/delivery/runbook-provisional.md para crear nexo_test separada de la base de demostración. No ejecutar pruebas destructivas sobre nexo ni borrar volúmenes compartidos.

```powershell
docker compose exec -e APP_ENV=testing -e DB_DATABASE=nexo_test app php artisan test
npm ci
npx playwright install chromium
npm run test:e2e
git status
```

Tras añadir código/pruebas propios y verificar los resultados, revisar los archivos concretos con `git diff`, añadir únicamente los pertinentes y publicar desde la cuenta del integrante:

```powershell
git add <archivos-revisados>
git commit -m "feat: adaptador y validacion del asistente IA"
git push -u origin feat/ia-vega
```

En GitHub crear PR **base: feat/provisional-frontend-integration**, **compare: feat/ia-vega**. Indicar RF/AC, qué cambió, origen real/replay, pruebas con resultado y SHA, uso de IA y pendientes. Solicitar revisión a Conde José y a otro integrante; no autoaprobar. Adjuntar evidencia sanitizada, actualizar bitácora y trazabilidad. No fusionar ni crear release-examen antes del cierre real del equipo.
