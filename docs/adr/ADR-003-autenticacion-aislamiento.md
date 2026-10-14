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