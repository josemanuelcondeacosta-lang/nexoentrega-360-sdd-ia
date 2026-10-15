// Carreras HTTP reales contra PostgreSQL (SPEC-03 0.4; hallazgos R-E04 y R-E05 de la revisión de Edier al PR #12).
// Requiere una base de evaluación recién sembrada y el servidor con varios workers (PHP_CLI_SERVER_WORKERS).
// Uso: node tests/integration/races.mjs   (variables opcionales: E2E_BASE_URL, RACE_ROUNDS; por defecto 20 rondas)
import assert from 'node:assert/strict';

const base = process.env.E2E_BASE_URL || 'http://127.0.0.1:8000';
const ROUNDS = Number(process.env.RACE_ROUNDS || 20);
const PASSWORD = 'DemoNexo360!';

function client() {
  const jar = new Map();
  return async (path, method = 'GET', body, headers = {}) => {
    const csrf = decodeURIComponent(jar.get('XSRF-TOKEN') || '');
    const response = await fetch(base + '/api' + path, {
      method,
      headers: { Accept: 'application/json', 'Content-Type': 'application/json', Cookie: [...jar].map(([k, v]) => k + '=' + v).join('; '), 'X-XSRF-TOKEN': csrf, ...headers },
      body: body === undefined ? undefined : JSON.stringify(body),
      signal: AbortSignal.timeout(15000),
    });
    for (const cookie of response.headers.getSetCookie()) {
      const pair = cookie.split(';')[0];
      const eq = pair.indexOf('=');
      jar.set(pair.slice(0, eq), pair.slice(eq + 1));
    }
    return { status: response.status, body: response.status === 204 ? null : await response.json() };
  };
}

async function login(org, role) {
  const c = client();
  await c('/auth/csrf');
  assert.equal((await c('/auth/login', 'POST', { login: `org-${org}-${role}@example.test`, password: PASSWORD })).status, 200, `login ${org}-${role}`);
  await c('/auth/csrf');
  return c;
}

async function stock(c, sku) {
  const item = (await c('/inventory')).body.items.find((x) => x.sku === sku);
  return [item.fisico, item.reservado, item.disponible];
}

async function sentRequest(requester, sku, cantidad) {
  const created = await requester('/requests', 'POST', {
    zona: 'NORTE', prioridad: 'NORMAL', fechaRequerida: new Date(Date.now() + 86400000).toISOString(), lineas: [{ sku, cantidad }],
  });
  assert.equal(created.status, 201);
  assert.equal((await requester(`/requests/${created.body.id}/send`, 'POST', {})).status, 200);
  return created.body.id;
}

async function count(c, id, action) {
  return (await c(`/requests/${id}/audit`)).body.items.filter((e) => e.action === action).length;
}

const key = () => crypto.randomUUID();

// ---------- R-E05 / AC-03-03: dos reservas que compiten por existencias insuficientes, repetido ROUNDS veces ----------
// ORG-B: KIT-ALIMENTO físico 4; cada solicitud pide 3. Tras cada ronda se cancelan ambas para volver a 4/0/4.
const reqB = await login('b', 'solicitante');
const coordB = await login('b', 'coordinador');
assert.deepEqual(await stock(coordB, 'KIT-ALIMENTO'), [4, 0, 4], 'Usar una base recién sembrada (ORG-B KIT-ALIMENTO 4/0/4)');
const reserveWins = [0, 0];
for (let round = 1; round <= ROUNDS; round++) {
  const ids = [await sentRequest(reqB, 'KIT-ALIMENTO', 3), await sentRequest(reqB, 'KIT-ALIMENTO', 3)];
  const responses = await Promise.all(ids.map((id) => coordB(`/requests/${id}/reserve`, 'POST', {}, { 'Idempotency-Key': key() })));
  assert.deepEqual(responses.map((r) => r.status).sort(), [200, 409], `ronda ${round}: una reserva 200 y otra 409`);
  assert.deepEqual(await stock(coordB, 'KIT-ALIMENTO'), [4, 3, 1], `ronda ${round}: stock 4/3/1`);
  const winner = responses.findIndex((r) => r.status === 200);
  reserveWins[winner]++;
  assert.equal((await count(coordB, ids[0], 'RESERVE')) + (await count(coordB, ids[1], 'RESERVE')), 1, `ronda ${round}: un solo evento RESERVE`);
  for (const id of ids) assert.equal((await reqB(`/requests/${id}/cancel`, 'POST', {})).status, 200);
  assert.deepEqual(await stock(coordB, 'KIT-ALIMENTO'), [4, 0, 4], `ronda ${round}: cancelar restaura 4/0/4`);
}

// ---------- AC-03-17 (H-E02): despacho de Bodega y cancelación del Solicitante dueño a la vez ----------
const reqA = await login('a', 'solicitante');
const coordA = await login('a', 'coordinador');
const bodegaA = await login('a', 'bodega');

async function dispatchVsCancel(sku, cantidad) {
  const id = await sentRequest(reqA, sku, cantidad);
  assert.equal((await coordA(`/requests/${id}/reserve`, 'POST', {}, { 'Idempotency-Key': key() })).status, 200);
  const before = await stock(coordA, sku);
  const [dispatch, cancel] = await Promise.all([bodegaA(`/requests/${id}/dispatch`, 'POST', {}), reqA(`/requests/${id}/cancel`, 'POST', {})]);
  assert.deepEqual([dispatch.status, cancel.status].sort(), [200, 409], 'un 200 y un 409');
  const dispatchWon = dispatch.status === 200;
  const estado = (await coordA(`/requests/${id}`)).body.estado;
  assert.equal(estado, dispatchWon ? 'DESPACHADA' : 'CANCELADA');
  const after = await stock(coordA, sku);
  const expected = dispatchWon ? [before[0] - cantidad, before[1] - cantidad, before[2]] : [before[0], before[1] - cantidad, before[2] + cantidad];
  assert.deepEqual(after, expected, 'stock coherente con la transición ganadora');
  assert.equal((await count(coordA, id, 'DISPATCH')) + (await count(coordA, id, 'CANCEL')), 1, 'exactamente un evento exitoso nuevo');
  return { dispatchWon, after };
}

// Ronda del oráculo de AC-03-17: KIT-ALIMENTO 10/7/3 → 3/0/3 si gana el despacho, 10/0/10 si gana la cancelación.
assert.deepEqual(await stock(coordA, 'KIT-ALIMENTO'), [10, 0, 10], 'Usar una base recién sembrada (ORG-A KIT-ALIMENTO 10/0/10)');
const oracle = await dispatchVsCancel('KIT-ALIMENTO', 7);
assert.deepEqual(oracle.after, oracle.dispatchWon ? [3, 0, 3] : [10, 0, 10]);

// Repeticiones: KIT-AGUA de ORG-A (físico 20) con 1 unidad por ronda, para no agotar existencias.
const agua = await stock(coordA, 'KIT-AGUA');
assert.ok(agua[0] >= ROUNDS - 1 && agua[1] === 0, 'KIT-AGUA de ORG-A debe tener existencias suficientes y nada reservado');
let dispatchWins = oracle.dispatchWon ? 1 : 0;
for (let round = 2; round <= ROUNDS; round++) {
  if ((await dispatchVsCancel('KIT-AGUA', 1)).dispatchWon) dispatchWins++;
}

console.log(JSON.stringify({
  result: 'passed', database: 'PostgreSQL', rounds: ROUNDS,
  reserveRace: { ok: ROUNDS, winnerFirst: reserveWins[0], winnerSecond: reserveWins[1], stockEachRound: [4, 3, 1] },
  dispatchVsCancel: { ok: ROUNDS, dispatchWon: dispatchWins, cancelWon: ROUNDS - dispatchWins, oracleRound: oracle },
}, null, 2));
