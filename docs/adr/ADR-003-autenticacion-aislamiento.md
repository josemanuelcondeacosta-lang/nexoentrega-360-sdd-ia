# ADR-003: Autenticación, Sesiones y Aislamiento Multitenant (ORG-A / ORG-B)

* **Estado:** Aprobado
* **Fecha:** 2026-10-14
* **Autora:** Carolina Cantillo
* **Revisión Humana:** Completada por Carolina Cantillo
* **Requisitos:** RF01, RF02, RNF01, SPEC-01 (AC-01-01 a AC-01-13)
* **Atención a Hallazgos:**
  - **H-V05:** Manejo estricto de HTTP 404 para evitar enumeración cross-tenant.
  - **H-V06:** Protección CSRF obligatoria en POST, PUT, PATCH, DELETE.

## Contexto y Problema
El sistema requiere aislar de forma completa y segura los datos entre ORG-A y ORG-B bajo cuatro roles (Solicitante, Coordinador, Bodega y Auditor), garantizando sesiones seguras y protección contra acceso no autorizado.

## Decisión Adoptada
Se elige **Laravel Sessions (Cookies HTTP-Only) + Scope Tenant en Eloquent**:
- **Algoritmo de Hash:** Bcrypt con costo 12.
- **Sesión y Cookie:** lifetime = 120 minutos, expire_on_close = true, http_only = true, same_site = lax. secure = true en producción (configurable a false en demo local HTTP).
- **Logout:** Invalidación explícita mediante Auth::logout() y regeneración de token de sesión.
- **Protección CSRF:** Activada en todas las mutaciones (POST, PUT, PATCH, DELETE).
- **Aislamiento:** Filtro por organization_id en todas las consultas Eloquent; retorna HTTP 404 ante intentos de acceso a recursos de otra entidad.