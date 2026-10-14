"""Validación documental OpenAPI, referencias, ejemplos y preservación IA."""
import copy
import warnings
from pathlib import Path

import yaml
from openapi_spec_validator import OpenAPIV31SpecValidator
with warnings.catch_warnings():
    warnings.simplefilter('ignore', DeprecationWarning)
    from jsonschema import Draft202012Validator, RefResolver

ROOT = Path(__file__).resolve().parents[1]


def validate_csrf(doc):
    for path, methods in doc['paths'].items():
        for method, operation in methods.items():
            if method not in ('post','put','patch','delete'):
                continue
            parameters = [doc['components']['parameters'][p['$ref'].split('/')[-1]] if '$ref' in p else p for p in operation.get('parameters',[])]
            if not any(p.get('in')=='header' and p.get('name')=='X-XSRF-TOKEN' and p.get('required') is True for p in parameters) or '419' not in operation['responses']:
                raise ValueError('Escritura sin contrato CSRF completo: ' + method + ' ' + path)


def main():
    doc = yaml.safe_load((ROOT/'contracts/openapi.yaml').read_text(encoding='utf-8'))
    ia = yaml.safe_load((ROOT/'contracts/openapi-ia.yaml').read_text(encoding='utf-8'))
    OpenAPIV31SpecValidator(doc).validate()
    assert doc['openapi'] == '3.1.1'
    validate_csrf(doc)
    # Regresión R-V01: OpenAPI puede ser válido y aun omitir protección CSRF.
    unsafe = copy.deepcopy(doc)
    unsafe['paths']['/api/ai/summaries']['post']['parameters'] = [p for p in unsafe['paths']['/api/ai/summaries']['post']['parameters'] if p.get('$ref') != '#/components/parameters/CsrfToken']
    OpenAPIV31SpecValidator(unsafe).validate()
    try:
        validate_csrf(unsafe)
    except ValueError:
        pass
    else:
        raise AssertionError('Regresión R-V01 no detectada')
    for path, value in ia['paths'].items():
        assert doc['paths'][path] == value, 'Ruta IA alterada: ' + path
    for category, values in ia['components'].items():
        for key, value in values.items():
            assert doc['components'][category][key] == value, 'Componente IA alterado: ' + key
    operation_ids = []
    examples = 0
    for path, methods in doc['paths'].items():
        for method, operation in methods.items():
            operation_ids.append(operation['operationId'])
            assert operation.get('security',doc['security']) or path in ['/health','/ready','/api/auth/login','/api/auth/csrf'], 'Ruta privada sin sesión'
            for result in operation['responses'].values():
                for media in result.get('content',{}).values():
                    validator = Draft202012Validator(media['schema'], resolver=RefResolver.from_schema(doc))
                    for example in media.get('examples',{}).values():
                        validator.validate(example['value'])
                        examples += 1
    assert len(operation_ids) == len(set(operation_ids)), 'operationId repetido'
    for action in ['send','reserve','dispatch','deliver','cancel']:
        assert 'post' in doc['paths']['/api/requests/{requestId}/'+action]
    assert not any(method in methods for methods in [doc['paths']['/api/audit'],doc['paths']['/api/requests/{requestId}/audit']] for method in ['post','patch','put','delete'])
    for path in ['/api/audit','/api/requests/{requestId}/audit']:
        assert doc['paths'][path]['get']['x-error-policy'] == {'unauthenticated':401,'missingOrOtherOrganization':404,'sameOrganizationForbiddenRoleOrOwner':403}, 'Política 403/404 divergente'
    validator = Draft202012Validator(doc['components']['schemas']['BorradorSolicitud'], resolver=RefResolver.from_schema(doc))
    valid = {'zona':'NORTE','prioridad':'NORMAL','fechaRequerida':'2030-01-10T15:00:00Z','lineas':[{'sku':'KIT-1','cantidad':1}]}
    validator.validate(valid)
    for field,value in [('prioridad','MEDIA'),('organizationId','ORG-B'),('lineas',[{'sku':'KIT-1','cantidad':0}])]:
        invalid = copy.deepcopy(valid)
        invalid[field] = value
        assert list(validator.iter_errors(invalid)), 'Dato inválido aceptado por esquema: ' + field
    print(f'OpenAPI 3.1.1 válido: {len(doc["paths"])} rutas, {len(operation_ids)} operaciones; IA preservada; {examples} ejemplos válidos y 3 casos negativos rechazados')
    print('Validación documental; no pruebas de API, base ni aplicación ejecutadas')
    print('CSRF en todas las escrituras y regresión por omisión detectada; política de auditoría 401/403/404 coherente')


if __name__ == '__main__':
    main()
