import assert from 'node:assert/strict';
const base=process.env.E2E_BASE_URL||'http://127.0.0.1:8000';
function client(){
 const jar=new Map();
 return async(path,method='GET',body,headers={})=>{
  const csrf=decodeURIComponent(jar.get('XSRF-TOKEN')||'');
  const response=await fetch(base+'/api'+path,{method,headers:{Accept:'application/json','Content-Type':'application/json',Cookie:[...jar].map(([k,v])=>k+'='+v).join('; '),'X-XSRF-TOKEN':csrf,...headers},body:body===undefined?undefined:JSON.stringify(body),signal:AbortSignal.timeout(15000)});
  for(const cookie of response.headers.getSetCookie()){const pair=cookie.split(';')[0],eq=pair.indexOf('=');jar.set(pair.slice(0,eq),pair.slice(eq+1));}
  return {status:response.status,body:response.status===204?null:await response.json()};
 };
}
async function login(role){const c=client();await c('/auth/csrf');assert.equal((await c('/auth/login','POST',{login:`org-b-${role}@example.test`,password:'DemoNexo360!'})).status,200);await c('/auth/csrf');return c;}
const requester=await login('solicitante'),coordinator=await login('coordinador');
const before=(await coordinator('/inventory')).body.items.find(x=>x.sku==='KIT-ALIMENTO');
assert.equal(before.fisico,4,'Usar demo limpia ORG-B');assert.equal(before.reservado,0,'No competir con una demo ajena');
const ids=[];
for(let i=0;i<2;i++){
 const result=await requester('/requests','POST',{zona:'NORTE',prioridad:'NORMAL',fechaRequerida:new Date(Date.now()+86400000).toISOString(),lineas:[{sku:'KIT-ALIMENTO',cantidad:3}]});
 assert.equal(result.status,201);ids.push(result.body.id);assert.equal((await requester(`/requests/${result.body.id}/send`,'POST',{})).status,200);
}
const responses=await Promise.all(ids.map(id=>coordinator(`/requests/${id}/reserve`,'POST',{}, {'Idempotency-Key':crypto.randomUUID()})));
assert.deepEqual(responses.map(x=>x.status).sort(),[200,409]);
const after=(await coordinator('/inventory')).body.items.find(x=>x.sku==='KIT-ALIMENTO');assert.deepEqual([after.fisico,after.reservado,after.disponible],[4,3,1]);
const states=await Promise.all(ids.map(id=>coordinator(`/requests/${id}`)));assert.deepEqual(states.map(x=>x.body.estado).sort(),['ENVIADA','RESERVADA']);
let reserves=0;
for(const id of ids){const events=await coordinator(`/requests/${id}/audit`);reserves+=events.body.items.filter(e=>e.action==='RESERVE').length;}
assert.equal(reserves,1);
const winner=states.findIndex(x=>x.body.estado==='RESERVADA');assert.equal((await requester(`/requests/${ids[winner]}/cancel`,'POST',{})).status,200);
const restored=(await coordinator('/inventory')).body.items.find(x=>x.sku==='KIT-ALIMENTO');assert.deepEqual([restored.fisico,restored.reservado,restored.disponible],[4,0,4]);
console.log(JSON.stringify({result:'passed',database:'PostgreSQL',responses:responses.map(x=>x.status),stockAfter:[4,3,1],reserveEvents:1,stockAfterCancel:[4,0,4]},null,2));
