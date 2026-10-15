// Prueba de carga RNF03 / AC-04-09: listados y tablero, 20 usuarios virtuales durante 60 s medidos tras 5 s de
// calentamiento, sobre 2.000 solicitudes sembradas (php artisan nexo:seed-carga). La IA queda excluida.
// Umbrales por endpoint, solo sobre la fase medida: p95 <= 700 ms y errores < 1 %.
// Uso con Docker: docker compose --profile carga run --rm k6
import http from 'k6/http';
import { check, sleep } from 'k6';

const BASE = __ENV.BASE_URL || 'http://127.0.0.1:8000';
const PASSWORD = 'DemoNexo360!'; // cuenta ficticia publicada para evaluación (seed)
const VUS = 20;

export const options = {
  scenarios: {
    calentamiento: { executor: 'constant-vus', vus: VUS, duration: '5s', tags: { fase: 'calentamiento' } },
    medicion: { executor: 'constant-vus', vus: VUS, duration: '60s', startTime: '5s', tags: { fase: 'medicion' } },
  },
  thresholds: {
    'http_req_duration{fase:medicion,endpoint:listado}': ['p(95)<=700'],
    'http_req_duration{fase:medicion,endpoint:tablero}': ['p(95)<=700'],
    'http_req_failed{fase:medicion,endpoint:listado}': ['rate<0.01'],
    'http_req_failed{fase:medicion,endpoint:tablero}': ['rate<0.01'],
  },
  summaryTrendStats: ['avg', 'min', 'med', 'p(90)', 'p(95)', 'max'],
  // Conserva la sesión entre iteraciones: k6 borra las cookies en cada iteración por defecto.
  noCookiesReset: true,
};

export function setup() {
  const ready = http.get(`${BASE}/ready`, { tags: { endpoint: 'preparacion' } });
  if (ready.status !== 200) throw new Error(`La aplicación no está lista en ${BASE} (HTTP ${ready.status})`);
}

let loggedIn = false;
const filtros = ['', '&estado=ENVIADA', '&estado=BORRADOR', '&prioridad=ALTA', '&zona=NORTE', '&zona=SUR'];

function login() {
  // Coordinador y Auditor de ORG-A se alternan por usuario virtual; ambos consultan listados y tablero.
  const role = __VU % 2 === 0 ? 'auditor' : 'coordinador';
  http.get(`${BASE}/api/auth/csrf`, { tags: { endpoint: 'sesion' } });
  const token = decodeURIComponent((http.cookieJar().cookiesForURL(BASE)['XSRF-TOKEN'] || [''])[0]);
  const res = http.post(`${BASE}/api/auth/login`, JSON.stringify({ login: `org-a-${role}@example.test`, password: PASSWORD }), {
    headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-XSRF-TOKEN': token },
    tags: { endpoint: 'sesion' },
  });
  loggedIn = check(res, { 'inicio de sesión 200': (r) => r.status === 200 });
}

export default function () {
  if (!loggedIn) {
    login();
    return;
  }
  const params = { headers: { Accept: 'application/json' } };
  const page = 1 + Math.floor(Math.random() * 90);
  const listado = http.get(`${BASE}/api/requests?page=${page}&pageSize=20${filtros[__ITER % filtros.length]}`, { ...params, tags: { endpoint: 'listado' } });
  check(listado, { 'listado 200': (r) => r.status === 200 });
  const tablero = http.get(`${BASE}/api/dashboard`, { ...params, tags: { endpoint: 'tablero' } });
  check(tablero, { 'tablero 200': (r) => r.status === 200 });
  sleep(1);
}

function metric(data, name) {
  const m = data.metrics[name];
  return m ? m.values : null;
}

export function handleSummary(data) {
  const result = {
    requisito: 'RNF03 / AC-04-09',
    herramienta: `k6 ${__ENV.K6_VERSION || 'ver salida de k6 version'}`,
    baseUrl: BASE,
    usuariosVirtuales: VUS,
    calentamientoS: 5,
    medicionS: 60,
    pausaPorIteracionS: 1,
    listado: { duracionMs: metric(data, 'http_req_duration{fase:medicion,endpoint:listado}'), errores: metric(data, 'http_req_failed{fase:medicion,endpoint:listado}') },
    tablero: { duracionMs: metric(data, 'http_req_duration{fase:medicion,endpoint:tablero}'), errores: metric(data, 'http_req_failed{fase:medicion,endpoint:tablero}') },
    umbrales: Object.fromEntries(Object.entries(data.metrics).filter(([, m]) => m.thresholds).map(([k, m]) => [k, Object.fromEntries(Object.entries(m.thresholds).map(([t, v]) => [t, v.ok]))])),
    generadoUtc: new Date().toISOString(),
  };
  const out = __ENV.RESULTS_DIR || '.';
  const fmt = (v) => (v == null ? 'n/d' : v.toFixed(1));
  const line = (name, r) => `${name}: p95=${fmt(r.duracionMs && r.duracionMs['p(95)'])} ms, mediana=${fmt(r.duracionMs && r.duracionMs.med)} ms, errores=${r.errores ? (r.errores.rate * 100).toFixed(2) : 'n/d'} %`;
  const text = [`RNF03 — ${result.generadoUtc}`, line('Listado', result.listado), line('Tablero', result.tablero),
    `Umbrales: ${Object.values(result.umbrales).every((t) => Object.values(t).every(Boolean)) ? 'CUMPLIDOS' : 'NO CUMPLIDOS'}`].join('\n');
  return {
    stdout: text + '\n',
    [`${out}/carga-rnf03.json`]: JSON.stringify(result, null, 2) + '\n',
  };
}
