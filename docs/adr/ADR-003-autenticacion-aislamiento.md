# ADR-003: Esquema de Autenticación, Permisos y Aislamiento Multitenant (ORG-A / ORG-B)

* **Estado:** Propuesto
* **Fecha:** 2026-10-14
* **Autora:** Carolina Cantillo
* **Revisión Humana:** Pendiente
* **Requisitos:** RF01, RF02, RNF01, SPEC-01 (AC-01-01 a AC-01-13)
* **Atención a Hallazgos:** H-V05 (mensajes genéricos en errores de credenciales) y H-V06 (aislamiento estricto multitenant en consultas).

## Contexto y Problema
El sistema NexoEntrega 360 da soporte a dos organizaciones independientes (ORG-A y ORG-B) bajo cuatro roles bien definidos (Solicitante, Coordinador, Bodega y Auditor). El reto principal es asegurar un aislamiento completo de la información entre ambas entidades, impidiendo que usuarios de una organización consulten o manipulen registros pertenecientes a la otra. Asimismo, la aplicación debe responder de forma limpia (HTTP 401, 403 y 404) sin filtrar pistas sobre la existencia de datos ajenos.

## Opciones Evaluadas
1. **Manejo de Sesiones nativo en Laravel (Cookies HTTP-Only) + Middleware de Tenant.**
2. **Tokens de Acceso con Laravel Sanctum (Bearer Tokens en Cabecera).**
3. **Bases de datos independientes o esquemas separados por organización.**

## Decisión Seleccionada
Adoptamos la **Opción 1: Sesiones basadas en cookies HTTP-Only con Middleware de Organización**, apoyadas por el ORM Eloquent con un alcance global (*Global Scope*) vinculado a Auth::user()->organization_id.

### Justificación
- **Determinación en Servidor:** La organización y el rol del usuario se resuelven de forma obligatoria en el backend a partir de la sesión autenticada. No se confía en ningún parámetro enviado desde el cliente (organization_id o ole_id en cuerpo o URL), cerrando cualquier intento de suplantación.
- **Respuesta 404 para Prevenir Enumeración:** Al buscar un recurso que no corresponde a la organización del usuario autenticado, el backend devolverá 404 Not Found en lugar de 403 Forbidden. Esto responde directamente al hallazgo H-V06 e impide que usuarios malintencionados deduzcan si un recurso existe en la otra organización.
- **Respuesta Segura en Credenciales (H-V05):** Ante cualquier fallo de autenticación, el mensaje de retorno será unívoco (*"Credenciales incorrectas"*), sin detallar si el correo o la contraseña fueron el factor de error.
- **Manejo de Contraseñas y CSRF:** Se utiliza hashing nativo con Bcrypt/Argon2id y protección activada contra CSRF mediante middleware de verificación de tokens.

## Consecuencias y Validación
- **Impacto:** Alta seguridad y separación lógica sólida sin la sobrecarga de mantener múltiples esquemas de base de datos.
- **Verificación:** Pruebas automatizadas sobre los criterios AC-01-01 a AC-01-13 y validación de respuestas en endpoints mediante middleware.
