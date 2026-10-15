# ADR-003: Autenticación, Sesiones y Aislamiento Multitenant (ORG-A / ORG-B)

* **Estado:** Propuesto
* **Fecha:** 2026-10-14
* **Autora:** Carolina Cantillo
* **Revisión Humana:** Pendiente
* **Requisitos:** RF01, RF02, RNF01, SPEC-01 (AC-01-01 a AC-01-13)
* **Atención a Hallazgos:** 
  - **H-V05:** Precisión de respuestas HTTP 403 vs 404 para evitar enumeración y fuga de datos cross-tenant.
  - **H-V06:** Protección estricta contra ataques CSRF mediante verificación de tokens y cookies SameSite/SameOrigin.

## Contexto y Problema
NexoEntrega 360 requiere aislar completamente los recursos entre ORG-A y ORG-B bajo cuatro roles (Solicitante, Coordinador, Bodega, Auditor). Se deben precisar los parámetros de sesión, logout, algoritmo de hashing definitivo y protección contra vulnerabilidades CSRF.

## Opciones Evaluadas
1. **Laravel Sessions (Cookie HTTP-Only, SameSite=Lax/Strict) + Scope Tenant en Eloquent.**
2. **Tokens Sanctum SPA / API Bearer Tokens.**
3. **Multi-tenancy con esquemas de BD separados.**

## Decisión Seleccionada
Adoptamos la **Opción 1: Sesiones basadas en cookies HTTP-Only con Middleware de Organización**, con las siguientes precisiones técnicas definitivas:

### Especificación Técnica
- **Algoritmo de Hashing Definitivo:** **Bcrypt** con factor de costo 12 para todas las contraseñas almacenadas.
- **Sesión y Cookie:** Duración de sesión configurable (config/session.php: lifetime = 120 minutos), expire_on_close = true, http_only = true, same_site = lax. El flag secure = true estará habilitado por entorno en producción HTTPS, manteniendo secure = false únicamente cuando la demo local corra sobre HTTP estándar.
- **Logout:** Invalidación explícita de sesión (Auth::logout(), session()->invalidate(), session()->regenerateToken()).
- **Protección CSRF (H-V06):** Verificación activa de token CSRF mediante ValidateCsrfToken en todas las peticiones de mutación HTTP (**POST, PUT, PATCH, DELETE**).
- **Manejo de Errores 403 vs 404 (H-V05):** Si un recurso existe pero pertenece a otra organización, el servidor retornará HTTP 404 Not Found en lugar de 403 Forbidden para evitar la enumeración maliciosa de identificadores.

## Consecuencias y Validación
- Aislamiento multitenant robusto a nivel de backend. Evaluado contra criterios AC-01-01 a AC-01-13.

## Complemento técnico propuesto para revisión

Preparado con apoyo de Codex a petición del coordinador; conserva la autoría del documento original. No constituye una aprobación de Carolina ni acredita una revisión humana nueva.

### Criterios y justificación

Se priorizan el aislamiento por organización, la revocación inmediata al cerrar sesión y una interfaz servida por el mismo monolito. Las sesiones evitan distribuir y almacenar tokens de larga duración en el navegador. Sanctum con tokens Bearer añadiría gestión de expiración y revocación sin una aplicación cliente independiente que lo justifique. Separar esquemas de PostgreSQL aumentaría el coste de migraciones y pruebas para dos organizaciones; se propone un esquema compartido con filtros de organización aplicados antes de consultar o contar recursos.

La comparación de organizaciones siempre utiliza la identidad autenticada; nunca se acepta la organización enviada por el cliente como autorización. Se responde 401 sin autenticación, 403 ante una acción prohibida dentro del ámbito visible y 404 ante un identificador inválido, inexistente o perteneciente a otra organización. Estas reglas también se aplican al historial y al asistente de IA.

### Consecuencias y riesgos

La sesión requiere protección CSRF en las rutas de escritura y almacenamiento compartido de sesiones si se despliega más de una instancia. SameSite=Lax complementa el token CSRF; no lo sustituye. HTTP local solo sirve para la demostración: un despliegue HTTPS debe activar la cookie Secure. El filtrado por organización en la aplicación exige pruebas negativas en cada consulta nueva; un esquema compartido no impone por sí solo ese aislamiento.

### Verificación y revisión pendiente

Contrastar `config/session.php`, `config/hashing.php`, `routes/web.php`, los módulos Identity/Queries/AI y las pruebas de autenticación y aislamiento. Ejecutar las pruebas con PostgreSQL real y comprobar login, regeneración e invalidación de sesión, 401/403/404, solicitudes sin CSRF, cruce de organizaciones y ausencia de cambios tras un rechazo. Registrar comando, resultado real, SHA y enlace de CI antes de aceptar este ADR. La configuración de pruebas puede reducir el coste bcrypt para acelerar la suite; no demuestra por sí sola el coste 12 de la configuración de ejecución.

Un revisor distinto de quien preparó cada cambio debe publicar su decisión sobre el SHA actualizado. Estado del complemento: **propuesto, revisión humana pendiente**.
