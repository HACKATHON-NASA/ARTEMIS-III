# 🔐 M0 · Identidad y acceso · Frontend

> [← Frontend](../README.md) · Código: `astrohealth/resources/js/modules/Identidad/` · Contrato: [Sesión con JWT](../../../../documentacion/arquitectura/03-frontend-vue.md#sesión-con-jwt) · [Endpoints de M0](../../backend/M0-identidad/README.md#6-endpoints)

| Estado | Fase | Responsable |
|---|:---:|---|
| ⬜ Sin empezar | **1** | Frontend |

Este documento cuenta **cómo quedó hecho**. **Qué** hay que construir está en la guía del frontend y en los endpoints de M0.

## Avance

**Página:** `pages/Login.vue`

**Componentes**
- [ ] LoginForm (correo, contraseña, errores AUTH_INVALID_CREDENTIALS y TOO_MANY_REQUESTS)
- [ ] RoleBadge (admin · médico de vuelo · astronauta)
- [ ] UserMenu (nombre, rol, cerrar sesión)

**Store y cliente**
- [ ] `useAuthStore` (access token en memoria, user, permissions, can())
- [ ] `lib/api.ts`: Bearer + un reintento con /auth/refresh ante AUTH_TOKEN_EXPIRED
- [ ] Restaurar la sesión al recargar (/auth/refresh al arrancar)
- [ ] Guardas de navegación: sin sesión → Login; sin permiso → ocultar o redirigir

## Componentes reales

| Componente | Props | Emite | Notas |
|---|---|---|---|
| | | | |

## Decisiones técnicas

_Pendiente._

## Diferencias con la guía

_Pendiente._

## Capturas

_Pendiente: login, error de credenciales, menú de usuario por rol._

> 📝 **Documentar siempre:** este documento se actualiza en el mismo *pull request* que cambia el código.
