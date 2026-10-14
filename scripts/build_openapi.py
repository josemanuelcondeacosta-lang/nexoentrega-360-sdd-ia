"""Genera contrato base y une el fragmento IA sin cambiar sus rutas/componentes."""
import argparse
import copy
from pathlib import Path

import yaml

ROOT = Path(__file__).resolve().parents[1]


def ref(name):
    return {'$ref': '#/components/schemas/' + name}


def obj(properties, required=None):
    return {'type': 'object', 'additionalProperties': False,
            'required': required if required is not None else list(properties), 'properties': properties}


def response(description, schema=None):
    result = {'description': description, 'headers': {'X-Correlation-Id': {'$ref': '#/components/headers/CorrelationId'}}}
    if schema:
        result['content'] = {'application/json': {'schema': schema}}
    return result


def build():
    ia = yaml.safe_load((ROOT / 'contracts/openapi-ia.yaml').read_text(encoding='utf-8'))
    doc = copy.deepcopy(ia)
    doc['info'] = {'title': 'NexoEntrega 360 — contrato integrado', 'version': '0.1.0-draft',
        'description': 'Diseño G2 propuesto, no API implementada. SPEC 0.3-draft; G1/G2 pendientes. '
        'Laravel + PostgreSQL. Sesión/CSRF pendientes ADR-003. CL-16/17/18: respuesta afirmativa reportada por coordinador; evidencia original pendiente. '
        'Rutas y componentes IA copiados sin cambios desde openapi-ia.yaml.'}
    doc['x-status'] = 'pending-human-review'
    doc['x-source-ia'] = 'contracts/openapi-ia.yaml; rama docs/g2-vega-ia; a13aa17'
    comp = doc['components']
    text = {'type': 'string', 'minLength': 1}
    instant = {'type': 'string', 'format': 'date-time'}
    number = {'type': 'integer', 'minimum': 0}
    state = {'type': 'string', 'enum': ['BORRADOR','ENVIADA','RESERVADA','DESPACHADA','ENTREGADA','CANCELADA']}
    priority = {'type': 'string', 'enum': ['NORMAL','ALTA']}
    line = obj({'sku': text, 'cantidad': {'type':'integer','minimum':1,'maximum':500}})
    draft = obj({'zona': text, 'prioridad': priority, 'fechaRequerida': instant,
                 'observaciones': {'type':'string','maxLength':1000},
                 'lineas': {'type':'array','minItems':1,'maxItems':10,'items':ref('LineaSolicitud')}},
                ['zona','prioridad','fechaRequerida','lineas'])
    schemas = {
        'LineaSolicitud': line,
        'Credenciales': obj({'login': {'type':'string','format':'email'}, 'password': {'type':'string','minLength':1,'writeOnly':True}}),
        'Identidad': obj({'id':text,'organizationId':text,'role':{'type':'string','enum':['Solicitante','Coordinador','Bodega','Auditor']}}),
        'BorradorSolicitud': draft,
        'Solicitud': obj({'id':text,'codigo':text,'solicitanteId':text,'organizationId':text,'estado':state,
            'zona':text,'prioridad':priority,'fechaRequerida':instant,'observaciones':{'type':['string','null'],'maxLength':1000},
            'lineas': {'type':'array','minItems':1,'maxItems':10,'items':ref('LineaSolicitud')},'createdAt':instant,'updatedAt':instant}),
        'InventarioItem': obj({'sku':text,'activo':{'type':'boolean'},'fisico':number,'reservado':number,'disponible':number}),
        'EventoAuditoria': obj({'id':text,'actorId':text,'organizationId':text,'resourceId':text,'action':text,'occurredAt':instant,'correlationId':text}),
        'Tablero': obj({'cantidadesPorEstado':obj({s: number for s in state['enum']})}),
        'Health': obj({'status':{'const':'ok'}}),
        'Readiness': obj({'status':{'enum':['ready','not_ready']},'database':{'enum':['up','down']}}),
    }
    for title, item in [('PaginaSolicitudes','Solicitud'),('PaginaInventario','InventarioItem'),('PaginaAuditoria','EventoAuditoria')]:
        schemas[title] = obj({'items':{'type':'array','maxItems':100,'items':ref(item)},
            'page':{'type':'integer','minimum':1},'pageSize':{'type':'integer','minimum':1,'maximum':100},'total':number})
    for title, value in schemas.items():
        if title in comp['schemas']:
            raise ValueError('Colisión de schema IA: ' + title)
        comp['schemas'][title] = value
    for title, description in {
        'Base401':'Sin identidad válida; no revela existencia de cuenta.',
        'Base403':'Acción prohibida por rol o dueño sobre recurso de la organización.',
        'Base404':'Recurso inexistente o de otra organización; sin revelar existencia.',
        'Base409':'Estado incompatible, stock insuficiente o contenido idempotente conflictivo; sin efectos de negocio.',
        'Base422':'Formato o datos inválidos; code/message y field cuando corresponda, sin escrituras parciales.',
        'Base500':'Fallo interno sanitizado; rollback antes de commit.'}.items():
        comp['responses'][title] = response(description, ref('Error'))
    comp['parameters']['RequestId'] = {'name':'requestId','in':'path','required':True,'schema':text}
    comp['parameters']['IdempotencyKey'] = {'name':'Idempotency-Key','in':'header','required':True,
        'description':'Reserva. Autenticar y autorizar antes de replay. Ámbito usuario/org/operación; hash canónico de requestId+cuerpo. '
        'Guardar 200 y 409 de negocio al menos 24h; no guardar 401/403/404/422/500. TTL exacto/retención pendiente ADR-002 y acuerdo CL-04/11.',
        'schema':{'type':'string','minLength':1,'maxLength':128}}
    comp['parameters']['CsrfToken'] = {'name':'X-XSRF-TOKEN','in':'header','required':True,
        'description':'Propuesta CSRF para sesión cookie; obtener cookie XSRF-TOKEN desde bootstrap y enviar token correspondiente. Pendiente ADR-003.',
        'schema':text}
    pagination = [ {'name':name,'in':'query','schema':schema} for name,schema in [
        ('page',{'type':'integer','minimum':1,'default':1}),
        ('pageSize',{'type':'integer','minimum':1,'maximum':100,'default':20})]]
    filters = [{'name':name,'in':'query','schema':schema} for name,schema in [('estado',state),('prioridad',priority),('zona',text)]]

    def add(path, method, operation_id, summary, schema=None, body=None, status='200', errors=('401','403','404','422'),
            roles=None, description='', parameters=None, public=False):
        if path in ia['paths']:
            raise ValueError('Colisión de ruta IA: ' + path)
        operation = {'operationId':operation_id,'summary':summary,'tags':['Base'],
            'description':description,'parameters':[{'$ref':'#/components/parameters/CorrelationId'}] + (parameters or []),
            'responses':{status:response(summary,schema)}}
        for code in errors:
            operation['responses'][code] = {'$ref':'#/components/responses/Base' + code}
        if body:
            operation['requestBody'] = {'required':True,'content':{'application/json':{'schema':body}}}
        if public:
            operation['security'] = []
        if roles:
            operation['x-roles'] = roles
        if method in ('post','patch'):
            operation['parameters'].append({'$ref':'#/components/parameters/CsrfToken'})
            operation['responses']['419'] = response('Token CSRF ausente o inválido; sesión cookie propuesta.', ref('Error'))
        doc['paths'].setdefault(path,{})[method] = operation

    all_roles = ['Solicitante','Coordinador','Bodega','Auditor']
    add('/api/auth/csrf','get','bootstrapCsrf','Inicializar protección CSRF',status='204',errors=(),public=True,
        description='Emite cookie XSRF-TOKEN, sin login ni conceder rol. Ruta de diseño pendiente ADR-003.')
    add('/api/auth/login','post','login','Iniciar sesión',ref('Identidad'),ref('Credenciales'),errors=('401','422'),public=True,
        description='Verificar hash, regenerar sesión y emitir nexo_session HttpOnly; Secure bajo HTTPS y SameSite. No retorna token/hash. Parámetros pendientes ADR-003.')
    add('/api/auth/logout','post','logout','Revocar sesión',status='204',errors=('401',),
        description='Revocar sesión y eliminar cookie; reutilización obtiene 401. No cambia datos de negocio.')
    add('/api/auth/me','get','identidadActual','Consultar identidad del servidor',ref('Identidad'),errors=('401',),roles=all_roles)
    add('/api/requests','post','crearSolicitud','Crear borrador propio',ref('Solicitud'),ref('BorradorSolicitud'),status='201',
        errors=('401','403','422','500'),roles=['Solicitante'],description='Organización, dueño y código generados por servidor. SKU activo y distinto; rechazar duplicados por validación semántica. Cabecera+líneas atómicas; stock inalterado.')
    add('/api/requests','get','listarSolicitudes','Listar solicitudes paginadas',ref('PaginaSolicitudes'),errors=('401','422'),roles=all_roles,
        parameters=pagination+filters,description='Solicitante solo propias; otros roles organización. Filtrar alcance antes de total/paginación; orden estable createdAt+id. Máximo 100. Vacío retorna items=[].')
    id_param = [{'$ref':'#/components/parameters/RequestId'}]
    add('/api/requests/{requestId}','get','consultarSolicitud','Consultar solicitud',ref('Solicitud'),roles=all_roles,
        parameters=id_param,description='Solicitante solo propia; búsqueda con alcance evita revelar recursos ajenos.')
    add('/api/requests/{requestId}','patch','editarSolicitud','Editar borrador propio',ref('Solicitud'),ref('BorradorSolicitud'),
        errors=('401','403','404','409','422','500'),roles=['Solicitante'],parameters=id_param,
        description='Reemplazo completo de campos editables, no merge parcial; solo BORRADOR del dueño. Nunca modificar código/dueño/org. No cambia stock.')
    transitions = [
        ('send','enviarSolicitud','Solicitante','BORRADOR → ENVIADA; validar fechaRequerida >= instante de envío. No cambia stock.'),
        ('reserve','reservarSolicitud','Coordinador','ENVIADA → RESERVADA; todas las líneas o ninguna. Disponible=físico−reservado. Aumenta reservado; stock/estado/auditoría/resultado idempotente atómicos.'),
        ('dispatch','despacharSolicitud','Bodega','RESERVADA → DESPACHADA; disminuyen físico y reservado por las cantidades de solicitud.'),
        ('deliver','entregarSolicitud','Coordinador','DESPACHADA → ENTREGADA; sin cambio de inventario.'),
        ('cancel','cancelarSolicitud','Solicitante','Dueño únicamente. ENVIADA o RESERVADA → CANCELADA. Liberar reserva solo desde RESERVADA; físico inalterado.')]
    for action, operation_id, role, desc in transitions:
        params = id_param + ([{'$ref':'#/components/parameters/IdempotencyKey'}] if action=='reserve' else [])
        add('/api/requests/{requestId}/'+action,'post',operation_id,action+' solicitud',ref('Solicitud'),obj({}),
            errors=('401','403','404','409','422','500'),roles=[role],parameters=params,
            description=desc+' Cuerpo requerido {} sin propiedades extra. Rechazo sin cambios de negocio ni evento exitoso. Audit atómico. Misma reserva con otra clave devuelve 409.')
    add('/api/inventory','get','listarInventario','Consultar inventario',ref('PaginaInventario'),errors=('401','403','422'),
        roles=['Coordinador','Bodega','Auditor'],parameters=pagination,
        description='Solo lectura de organización autenticada. físico/reservado/disponible no negativos. Coordinador incluido por CL-16 según respuesta docente reportada por coordinador.')
    add('/api/dashboard','get','consultarTablero','Consultar cantidades por estado',ref('Tablero'),errors=('401','403','422'),
        roles=all_roles,parameters=filters,
        description='Alcance propio para Solicitante por CL-18 reportada; organización para otros. Sin inventario ni datos ajenos.')
    for path in ['/api/audit','/api/requests/{requestId}/audit']:
        add(path,'get','listarAuditoria' if path=='/api/audit' else 'historialSolicitud','Consultar auditoría inmutable',
            ref('PaginaAuditoria'),roles=['Auditor','Coordinador','Solicitante'],parameters=pagination + ([] if path=='/api/audit' else id_param),
            description='Auditor y Coordinador: organización; Solicitante: solo eventos de sus solicitudes (CL-17 según reporte del coordinador). Bodega sin acceso. Por solicitud validar pertenencia y dueño; recursos fuera del alcance retornan 404. Orden UTC+id. Sin rutas de edición o borrado.')
    add('/health','get','health','Liveness del proceso',ref('Health'),errors=(),public=True,
        description='200 si proceso responde; no depende de DB o LLM. Sin detalles internos.')
    add('/ready','get','readiness','Readiness de persistencia',ref('Readiness'),errors=(),public=True,
        description='200 si PostgreSQL accesible y migraciones esperadas aplicadas; 503 en otro caso. No depende de IA; sin secretos.')
    doc['paths']['/ready']['get']['responses']['503'] = response('Base no disponible o migraciones pendientes.',ref('Readiness'))
    # Fragmento IA intacto: el contrato de sesión/CSRF común se concretará en ADR-003.
    assert all(doc['paths'][p] == item for p,item in ia['paths'].items())
    return doc


if __name__ == '__main__':
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--check', action='store_true')
    args = parser.parse_args()
    target = ROOT / 'contracts/openapi.yaml'
    data = yaml.safe_dump(build(), sort_keys=False, allow_unicode=True)
    if args.check:
        if not target.exists() or target.read_text(encoding='utf-8') != data:
            raise SystemExit('Contrato integrado desactualizado')
        print('Contrato integrado coherente con base y fragmento IA')
    else:
        target.write_text(data, encoding='utf-8')
        print('Contrato integrado generado')
