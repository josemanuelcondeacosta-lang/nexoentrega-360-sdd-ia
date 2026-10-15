Integra los aportes de backend/pruebas de Edier y de IA/carga de Vega mediante commits de merge, conservando sus PR originales. Corrige el error 500 ante UUID inválidos, alinea PHP con el lockfile y evita reutilizar respuestas IA cuando el contexto difiere de la captura. Las tareas documentales comparten una única fuente y las bitácoras conservan identificadores distintos.

Código probado: `6a751d477e2e10939f0081050fb9bb996ede3d05`; fuentes del Word/PDF: `4c4844894275f390763c28bd2f8a93f4ad730f0b`. Los commits posteriores de documentación y artefactos no se presentan como nuevas ejecuciones del código.

Verificación ejecutada:

- PostgreSQL: 49 pruebas PHP, 518 aserciones; cuatro recorridos de navegador a 390/1366 px.
- Veinte rondas de reserva y veinte de despacho/cancelación, ambos órdenes de victoria observados.
- Carga k6 con 2.000 solicitudes, 20 VU y 60s medidos: p95 278,3/215,2ms, errores 0%.
- Restauración en otra base: 17 tablas coinciden en cantidad y digest de contenido.
- Clon remoto nuevo listo en 21,36s con capas Docker disponibles; no mide una máquina inicialmente sin caché.
- [CI del código probado correcta](https://github.com/josemanuelcondeacosta-lang/nexoentrega-360-sdd-ia/actions/runs/38673257765).

Informes y límites: `docs/evidence/runtime/candidate/verification.md`. Matriz de 73 AC: `ac-evidence.csv` en esa carpeta. Word/PDF candidatos: `deliverables/candidato/`, con manifest SHA-256. Se conservan las ediciones anteriores.

Pendientes: revisión independiente de SPEC-02/03, ADR y correcciones documentales, variantes por AC e inspección manual. La captura de Vega tarda 18,9s y solo se presenta como replay declarado; no demuestra el plazo de 5s ni una llamada nueva. G1/G2 globales siguen pendientes; no se publican aprobaciones en nombre de integrantes, no se fusionan los PR originales ni se crea `release-examen`. Instrucciones personales en `docs/team/revision-candidata.md`.

Preparación y ejecución asistidas por Codex a solicitud del coordinador, quien confirmó haber avisado al equipo.
