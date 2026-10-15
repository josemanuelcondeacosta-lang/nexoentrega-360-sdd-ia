'use strict';
const $ = id => document.getElementById(id);
const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
let identity = null, catalog = [], requests = [], page = 1, total = 0;
const reserveKeys = new Map();
const stateNames = {BORRADOR:'Borrador',ENVIADA:'Enviada',RESERVADA:'Reservada',DESPACHADA:'Despachada',ENTREGADA:'Entregada',CANCELADA:'Cancelada'};
const eventNames = {SEND:'Solicitud enviada',RESERVE:'Reserva confirmada',DISPATCH:'Despacho realizado',DELIVER:'Entrega registrada',CANCEL:'Solicitud cancelada',APPROVE_AI:'Resumen aprobado'};
let historyGeneration=0;
async function api(path, method='GET', body, extraHeaders={}) {
    const token = decodeURIComponent(document.cookie.split('; ').find(v => v.startsWith('XSRF-TOKEN='))?.slice(11) || '');
    const response = await fetch('/api'+path,{method,credentials:'same-origin',headers:{Accept:'application/json','Content-Type':'application/json','X-XSRF-TOKEN':token,...extraHeaders},body:body===undefined?undefined:JSON.stringify(body)});
    const data = response.status===204?null:await response.json();
    if (!response.ok) { const error = new Error(data.message || 'No se pudo completar la operación.'); error.status=response.status;error.field=data.field;throw error; }
    return data;
}
function notice(message, bad=false) { $('notice').textContent=message;$('notice').hidden=false;$('notice').className=bad?'notice error':'notice success'; }
function renderIdentity() {
    $('login-view').hidden=true;$('app-view').hidden=false;
    $('org-label').textContent=identity.organizationId;$('role-label').textContent=identity.role;
    $('new-request').hidden=identity.role!=='Solicitante';
    document.querySelector('[data-tab="inventory"]').hidden=identity.role==='Solicitante';
    document.querySelector('[data-tab="audit"]').hidden=identity.role==='Bodega';
    document.querySelector('[data-tab="ai"]').hidden=identity.role!=='Coordinador';
}
async function loadRequests() {
    const params = new URLSearchParams({page:String(page),pageSize:'20'});
    for(const [key,id] of [['estado','filter-state'],['prioridad','filter-priority'],['zona','filter-zone']]) if($(id).value)params.set(key,$(id).value);
    const [list, dashboard] = await Promise.all([api('/requests?'+params),api('/dashboard?'+params)]);
    requests=list.items;total=list.total;
    $('stats').innerHTML=['ENVIADA','RESERVADA','DESPACHADA','ENTREGADA'].map((s,i)=>`<div class="stat"><span class="stat-icon">${['↗','▦','→','✓'][i]}</span><span>${stateNames[s]}</span><strong>${dashboard.cantidadesPorEstado[s]}</strong><small>${['Por revisar','Kits comprometidos','En camino','Proceso completado'][i]}</small></div>`).join('');
    $('result-count').textContent=total+' solicitudes';$('page-label').textContent='Página '+page;
    $('prev').disabled=page<=1;$('next').disabled=page*20>=total;
    $('requests-list').innerHTML=requests.length?requests.map(row=>{
        const buttons=[];
        if(identity.role!=='Bodega')buttons.push(['history','Ver recorrido']);
        if(identity.role==='Solicitante' && row.estado==='BORRADOR'){buttons.push(['edit','Editar'],['send','Enviar']);}
        if(identity.role==='Solicitante' && ['ENVIADA','RESERVADA'].includes(row.estado))buttons.push(['cancel','Cancelar']);
        if(identity.role==='Coordinador' && row.estado==='ENVIADA')buttons.push(['reserve','Reservar']);
        if(identity.role==='Coordinador' && row.estado==='DESPACHADA')buttons.push(['deliver','Registrar entrega']);
        if(identity.role==='Bodega' && row.estado==='RESERVADA')buttons.push(['dispatch','Despachar']);
        return `<article class="request-card" data-request="${escapeHtml(row.id)}"><div class="request-symbol">${row.zona==='NORTE'?'N':row.zona==='SUR'?'S':'C'}</div><div class="request-description"><h3>${escapeHtml(row.codigo)} <span class="badge ${row.estado.toLowerCase()}">${stateNames[row.estado]}</span></h3><p>${escapeHtml(row.zona)} · ${row.lineas.reduce((s,l)=>s+l.cantidad,0)} kits · ${escapeHtml(new Date(row.fechaRequerida).toLocaleString('es-CO',{dateStyle:'medium',timeStyle:'short'}))}</p><small>${row.lineas.map(l=>escapeHtml(l.sku)+' × '+l.cantidad).join(' · ')}</small></div><span class="priority ${row.prioridad==='ALTA'?'high':''}">${escapeHtml(row.prioridad)}</span><div class="request-actions">${buttons.map(([action,label])=>`<button class="${action==='cancel'?'quiet':'secondary'}" data-action="${action}" data-id="${row.id}">${label}</button>`).join('')}</div></article>`;
    }).join(''):'<div class="empty"><span>↗</span><h3>Aún no hay solicitudes aquí</h3><p>Crea una solicitud o ajusta los filtros para comenzar.</p></div>';
}
async function selectTab(tab) {
    document.querySelectorAll('.tab').forEach(el=>el.hidden=el.id!==tab+'-tab');
    document.querySelectorAll('.nav-item').forEach(el=>el.classList.toggle('active',el.dataset.tab===tab));
    if(tab==='requests')await loadRequests();
    if(tab==='inventory') {
        const data=await api('/inventory?pageSize=100');
        $('inventory-list').innerHTML=data.items.map(item=>`<article class="panel inventory-card"><span class="eyebrow">KIT COMUNITARIO</span><h2>${escapeHtml(item.sku.replace('KIT-',''))}</h2><strong class="inventory-available">${item.disponible}<small> disponibles</small></strong><dl><div><dt>Existencia física</dt><dd>${item.fisico}</dd></div><div><dt>Reservados</dt><dd>${item.reservado}</dd></div></dl></article>`).join('');
    }
    if(tab==='audit') {
        const data=await api('/audit?pageSize=100');
        $('audit-list').innerHTML=data.items.length?data.items.map(event=>`<article class="audit-event"><span class="audit-dot"></span><div><h3>${escapeHtml(eventNames[event.action]||event.action)}</h3><p>${escapeHtml(requests.find(row=>row.id===event.resourceId)?.codigo||event.resourceId)}</p><small>Actor ${escapeHtml(event.actorId)} · ${escapeHtml(new Date(event.occurredAt).toLocaleString('es-CO'))}</small><small class="correlation">${escapeHtml(event.action)} · ${escapeHtml(event.correlationId)}</small>${event.action!=='APPROVE_AI'?`<button class="secondary" data-history="${escapeHtml(event.resourceId)}">Ver recorrido</button>`:''}</div></article>`).join(''):'<div class="empty"><h3>No hay eventos registrados</h3><p>Las transiciones confirmadas aparecerán aquí.</p></div>';
    }
    if(tab==='ai') {
        const data=await api('/requests?estado=ENVIADA&pageSize=100');
        $('ai-selection').innerHTML=data.items.length?data.items.map(row=>`<label class="ai-option"><input type="checkbox" name="ai-request" value="${escapeHtml(row.id)}" data-code="${escapeHtml(row.codigo)}"> <span>${escapeHtml(row.codigo)} · ${escapeHtml(row.zona)} <small>${escapeHtml(row.prioridad)}</small></span></label>`).join(''):'<p>No hay solicitudes enviadas para revisar.</p>';
        $('generate-ai').disabled=!data.items.length;$('ai-result').hidden=true;
    }
}
function run(task){ return task.catch(error=>notice(`${error.message}${error.status?' (HTTP '+error.status+')':''}`,true)); }
$('login-form').addEventListener('submit',async event=>{
    event.preventDefault();$('login-error').hidden=true;const button=event.submitter;button.disabled=true;
    try { await api('/auth/csrf');identity=await api('/auth/login','POST',{login:$('login-email').value,password:$('login-password').value});await api('/auth/csrf');catalog=await api('/catalog');renderIdentity();await selectTab('requests');$('main').focus(); }
    catch(error){$('login-error').textContent=error.message;$('login-error').hidden=false;$('login-email').setAttribute('aria-invalid','true');$('login-email').setAttribute('aria-describedby','login-error');}
    finally{button.disabled=false;}
});
$('logout').addEventListener('click',()=>run((async()=>{await api('/auth/logout','POST',{});identity=null;$('app-view').hidden=true;$('login-view').hidden=false;$('login-password').value='';$('notice').hidden=true;$('login-email').focus();})()));
document.querySelectorAll('[data-tab]').forEach(el=>el.addEventListener('click',()=>run(selectTab(el.dataset.tab))));
$('filters').addEventListener('submit',event=>{event.preventDefault();page=1;run(loadRequests());});
$('prev').addEventListener('click',()=>{page--;run(loadRequests());});$('next').addEventListener('click',()=>{page++;run(loadRequests());});
$('requests-list').addEventListener('click',event=>{
    const button=event.target.closest('[data-action]');if(!button)return;
    if(button.dataset.action==='edit'){openForm(requests.find(row=>row.id===button.dataset.id));return;}
    if(button.dataset.action==='history'){run(openHistory(button.dataset.id));return;}
    run((async()=>{button.disabled=true;const action=button.dataset.action,id=button.dataset.id;
        if(action==='reserve'&&!reserveKeys.has(id))reserveKeys.set(id,crypto.randomUUID());
        try{await api('/requests/'+id+'/'+action,'POST',{},action==='reserve'?{'Idempotency-Key':reserveKeys.get(id)}:{});reserveKeys.delete(id);notice('Operación confirmada. Estado e inventario actualizados.');await loadRequests();}
        catch(error){if(error.status&&error.status<500)reserveKeys.delete(id);throw error;}
        finally{button.disabled=false;}
    })());
});
async function openHistory(id) {
    const generation=++historyGeneration;
    const dialog=$('history-dialog');
    $('history-title').textContent='Recorrido de la solicitud';
    $('history-summary').textContent='Cargando historial…';
    $('history-events').replaceChildren();
    if(!dialog.open)dialog.showModal();
    $('close-history').focus();
    try {
        const [request,data]=await Promise.all([api('/requests/'+encodeURIComponent(id)),api('/requests/'+encodeURIComponent(id)+'/audit?pageSize=100')]);
        if(!dialog.open||generation!==historyGeneration)return;
        $('history-title').textContent='Recorrido de '+request.codigo;
        $('history-summary').textContent='Estado actual: '+stateNames[request.estado]+'. Del primer paso al más reciente.';
        $('history-events').innerHTML=data.items.length?data.items.map((event,index)=>`<li class="history-step"><span class="history-number">${index+1}</span><div><h3>${escapeHtml(eventNames[event.action]||event.action)}</h3><p>${escapeHtml(new Date(event.occurredAt).toLocaleString('es-CO'))}</p><small>Actor ${escapeHtml(event.actorId)}</small><details><summary>Referencia del evento</summary><small class="correlation">${escapeHtml(event.correlationId)}</small></details></div></li>`).join(''):'<li>El borrador aún no tiene transiciones registradas.</li>';
    } catch(error) {
        if(dialog.open&&generation===historyGeneration)$('history-summary').textContent=error.message+(error.status?' (HTTP '+error.status+')':'');
    }
}
$('audit-list').addEventListener('click',event=>{const button=event.target.closest('[data-history]');if(button)run(openHistory(button.dataset.history));});
$('close-history').addEventListener('click',()=>$('history-dialog').close());
$('history-dialog').addEventListener('close',()=>{historyGeneration++;});
function addLine(line={sku:catalog[0],cantidad:1}) {
    const index=crypto.randomUUID();const div=document.createElement('div');div.className='request-line';
    div.innerHTML=`<div><label for="sku-${index}">Tipo de kit</label><select id="sku-${index}" class="line-sku">${catalog.map(s=>`<option ${s===line.sku?'selected':''}>${escapeHtml(s)}</option>`).join('')}</select></div><div><label for="qty-${index}">Cantidad</label><input id="qty-${index}" class="line-qty" type="number" min="1" max="500" step="1" required value="${line.cantidad ?? 1}"></div><button class="quiet remove-line" type="button" aria-label="Eliminar línea">✕</button>`;
    div.querySelector('button').addEventListener('click',()=>{if($('request-lines').children.length>1)div.remove();});$('request-lines').append(div);
}
function openForm(row=null) {
    $('request-id').value=row?.id||'';$('dialog-title').textContent=row?'Editar borrador':'Nueva solicitud';$('request-zone').value=row?.zona||'NORTE';$('request-priority').value=row?.prioridad||'NORMAL';
    const date=row?new Date(row.fechaRequerida):new Date(Date.now()+86400000);date.setMinutes(date.getMinutes()-date.getTimezoneOffset());$('request-date').value=date.toISOString().slice(0,16);
    $('request-notes').value=row?.observaciones||'';$('request-lines').replaceChildren();(row?.lineas||[{}]).forEach(addLine);$('form-error').hidden=true;
    document.querySelectorAll('#request-form [aria-invalid]').forEach(el=>{el.removeAttribute('aria-invalid');el.removeAttribute('aria-describedby');});$('request-date').setAttribute('aria-describedby','date-help');
    $('request-dialog').showModal();$('request-zone').focus();
}
$('new-request').addEventListener('click',()=>openForm());$('add-line').addEventListener('click',()=>{if($('request-lines').children.length<10)addLine();});
for(const id of ['close-dialog','cancel-dialog'])$(id).addEventListener('click',()=>$('request-dialog').close());
$('request-form').addEventListener('submit',async event=>{
    event.preventDefault();const button=event.submitter;button.disabled=true;$('form-error').hidden=true;
    const data={zona:$('request-zone').value,prioridad:$('request-priority').value,fechaRequerida:new Date($('request-date').value).toISOString(),observaciones:$('request-notes').value,
        lineas:Array.from($('request-lines').children).map(line=>({sku:line.querySelector('select').value,cantidad:Number(line.querySelector('input').value)}))};
    try{const id=$('request-id').value;await api('/requests'+(id?'/'+id:''),id?'PATCH':'POST',data);await loadRequests();$('request-dialog').close();notice('Borrador guardado. Revísalo y envíalo cuando esté listo.');}
    catch(error){$('form-error').textContent=error.message+(error.field?' Campo: '+error.field:'');$('form-error').hidden=false;const field=error.field==='fechaRequerida'?$('request-date'):error.field?.startsWith('lineas')?$('request-lines').querySelector('input'):$('request-zone');field.setAttribute('aria-invalid','true');field.setAttribute('aria-describedby','form-error');field.focus();}
    finally{button.disabled=false;}
});
$('generate-ai').addEventListener('click',()=>run((async()=>{
    const ids=Array.from(document.querySelectorAll('[name="ai-request"]:checked')).map(el=>el.value);if(ids.length<1||ids.length>20){notice('Selecciona entre 1 y 20 solicitudes.',true);return;}
    const result=$('ai-result');result.hidden=false;result.setAttribute('aria-busy','true');result.innerHTML='<p role="status">Preparando resumen…</p>';
    let data;try{data=await api('/ai/summaries','POST',{requestIds:ids});}catch(error){result.innerHTML=`<p class="error" role="alert">${escapeHtml(error.message)}</p>`;result.removeAttribute('aria-busy');return;}
    // Todo texto del modelo se escapa (AC-05-17): summary y reason nunca se insertan como HTML.
    const codes=Object.fromEntries(Array.from(document.querySelectorAll('[name="ai-request"]')).map(el=>[el.value,el.dataset.code||el.value]));
    const originNames={provider:'modelo en vivo',replay:'reproducción declarada de una respuesta real',fixture:'fixture de prueba',fallback:'resumen automático'};
    const origin=[originNames[data.origin.type]||data.origin.type,data.origin.provider,data.origin.model,data.origin.type==='replay'||data.origin.type==='fixture'?data.origin.fixtureId:null].filter(Boolean).join(' · ');
    const recommendations=data.recommendations.length?`<h4>Recomendaciones (no se aplican solas)</h4><ul class="ai-recommendations">${data.recommendations.map(rec=>`<li><strong>${escapeHtml(codes[rec.requestId]||rec.requestId)}</strong>: ${escapeHtml(rec.reason)} <small>Evidencia: ${rec.evidenceFields.map(escapeHtml).join(', ')}</small></li>`).join('')}</ul>`:'';
    result.removeAttribute('aria-busy');
    result.innerHTML=`<span class="eyebrow">ASISTENCIA DE IA · BORRADOR</span><h3>Resumen para tu revisión</h3><p class="muted">Origen: ${escapeHtml(origin)}</p>${data.message?`<p role="status">${escapeHtml(data.message)}</p>`:''}<p class="ai-summary">${escapeHtml(data.summary)}</p>${recommendations}<button class="secondary" id="approve-ai">Aprobar resumen</button>`;
    $('approve-ai').addEventListener('click',()=>run((async()=>{await api('/ai/summaries/'+data.id+'/approve','POST',{});$('approve-ai').disabled=true;$('approve-ai').textContent='Resumen aprobado';notice('Aprobación registrada; no cambia solicitudes ni inventario.');})()));
})()));
(async()=>{try{await api('/auth/csrf');identity=await api('/auth/me');catalog=await api('/catalog');renderIdentity();await selectTab('requests');}catch(error){if(error.status!==401)$('login-error').textContent='No se pudo conectar con la API.';}})();
