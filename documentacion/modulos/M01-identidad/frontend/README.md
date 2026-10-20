# M01 · Identidad y acceso · Frontend

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · **Frontend** · [Base de datos](../database/README.md)

Capa **Inertia + Vue 3 + Tailwind CSS v4** del módulo. Los datos llegan como *props* desde los controladores web del [backend](../backend/README.md); las capturas sin conexión usan la cola offline de [M15](../../M15-sincronizacion/frontend/README.md).

---

## 1. Páginas Inertia

| App | Ruta web | Componente | Roles |
|---|---|---|---|
| Todas | `/login` | `Auth/Login` (del *starter kit*) | Público |
| F4 | `/admin/usuarios` | `Admin/Usuarios/Index` | `admin` |
| F4 | `/admin/tokens` | `Admin/Tokens/Index` | `ingeniero_biomedico`, `admin` |
| F4 | `/admin/auditoria` | `Admin/Auditoria/Index` | `admin`, `medico_vuelo` |

Tras iniciar sesión, cada rol va a su inicio: `astronauta` y `cmo` → `/astronauta`, `medico_vuelo` → `/tierra`, `ingeniero_biomedico` y `admin` → `/admin`, `investigador` → `/admin/exportaciones`. Cada rol solo puede iniciar sesión en su nodo ([backend §3.2](../backend/README.md#32-inicio-de-sesión-por-nodo)), así que su inicio siempre existe.

> **Sprint 0:** los inicios `/astronauta`, `/cmo`, `/tierra`, `/admin` y `/admin/exportaciones` se crean como páginas temporales (título del rol y menú) para poder cumplir el criterio de cierre ("cada usuario inicia sesión y ve el menú de su rol"). Cada módulo las reemplaza en su sprint. En el Sprint 0 las *props* compartidas `tripulante` y `mision` llegan como `null` (M02 aún no existe).

Cómo debe verse cada pantalla (guía visual provisional, bocetos y textos exactos): [historias de frontend](HU/README.md).

## 2. Archivos

```
resources/js/modules/Identidad/Pages/Auth/Login.vue              # Del starter kit (pasa aquí desde pages/auth; se traduce y se quita "Recordarme")
resources/js/modules/Identidad/Pages/Auth/{ForgotPassword,ResetPassword}.vue   # Del kit; solo con correo configurado
resources/js/modules/Identidad/Pages/Settings/{Security,Appearance}.vue        # Del kit: cambio de contraseña y apariencia
resources/js/modules/Identidad/Pages/Admin/Usuarios/Index.vue
resources/js/modules/Identidad/Pages/Admin/Tokens/Index.vue
resources/js/modules/Identidad/Pages/Admin/Auditoria/Index.vue
resources/js/modules/Identidad/Components/{UsuariosFiltros,UsuariosTabla,UsuarioPanel,DialogoDesactivarUsuario,TokenUnaVez,AuditoriaFiltros,AuditoriaTabla}.vue
resources/js/modules/Identidad/Types/{usuarios,tokens,auditoria}.ts
resources/js/modules/Identidad/Utils/accionesAuditoria.ts
resources/js/layouts/{AstronautaLayout,CmoLayout,TierraLayout,AdminLayout}.vue   # Globales: menú según los permisos
resources/js/composables/usePermisos.ts         # puede('ver-auditoria') a partir de auth.permisos
```

## 3. Props de cada página

```ts
// Prop compartida (HandleInertiaRequests): se amplía la de las convenciones del frontend
auth: { user: { id: number, name: string, email: string, rol: Rol }, permisos: string[] /* Gates que cumple, backend §5 */ }
name: string                       // nombre de la app (del kit)
sidebarOpen: boolean               // estado del menú lateral (del kit)
flash: { exito: string | null, error: string | null,
         tokenPwa: { token: string, expira_en: string } | null /* solo tras iniciar sesión (HU-15.04) */,
         descargarUrl: string | null /* M14: reporte recién generado */ }

// Auth/Login  (del starter kit)
{ status?: string, canResetPassword: boolean /* false si el nodo no tiene correo configurado */ }

// Admin/Usuarios/Index
{
  usuarios: Paginado<{ id: number, name: string, email: string, rol: Rol, activo: boolean,
                       tripulante: { codigo: string } | null }>,
  roles: Rol[],                      // los 6 roles de usuario del contrato §9.1 del plan (sin `sistema`)
  filtros: { buscar?: string, rol?: Rol, activo?: '1' | '0' }
}

// Admin/Tokens/Index
{
  tokens: { id: number, nombre: string, tipo: 'dispositivo' | 'pwa' | 'nodo',
            dueno: string /* identificador del dispositivo, del usuario o del nodo */,
            habilidades: string[], expira_en: string | null, ultimo_uso_en: string | null, creado_en: string }[],
  dispositivos: { id: string, identificador: string, tipo: string, tieneToken: boolean }[],   // sin las tabletas
  tokenNuevo?: string                // texto plano, solo en la respuesta que lo crea (flash)
}

// Admin/Auditoria/Index
{
  entradas: Paginado<{ ocurrido_en: string, actor: string, accion: string, recurso_tipo: string,
                       recurso_id: string | null, tripulante: string | null, ip: string | null,
                       nodo_origen: 'nave' | 'tierra', detalle: Record<string, unknown> | null }>,
  tripulantes: string[],             // códigos para el filtro
  filtros: { tripulante?: string, actor?: string, accion?: string, nodo?: 'nave' | 'tierra', desde?: string, hasta?: string }
}
```

`Paginado<T>` es el formato de `paginate()` de Laravel (`data`, `links`, `meta`).

| Página | Acciones |
|---|---|
| `Admin/Usuarios/Index` | Crear usuario con contraseña temporal, cambiar rol, activar/desactivar (`useForm` → `PUT /admin/usuarios/{id}`), poner otra contraseña temporal (`PUT /admin/usuarios/{id}/contrasena`); desactivar pide confirmación porque revoca sus tokens y cierra su sesión |
| `Admin/Tokens/Index` | Emitir token para un dispositivo (`POST /admin/tokens`; si ya tiene uno, pide confirmar el reemplazo); revocar (`DELETE /admin/tokens/{id}`; los de nodo, solo el admin). El token nuevo se muestra **una sola vez** con botón "Copiar" y el aviso "No se volverá a mostrar" |
| `Admin/Auditoria/Index` | Filtrar, también por nodo de origen (los filtros viajan en la URL con `router.get(..., { preserveState: true })`); solo lectura |

## 4. Componentes propios

| Componente | Uso |
|---|---|
| `Components/TokenUnaVez.vue` | Caja con el token en texto plano, botón copiar y aviso |
| `Components/UsuariosFiltros.vue` · `UsuariosTabla.vue` · `UsuarioPanel.vue` · `DialogoDesactivarUsuario.vue` | Piezas de `Admin/Usuarios/Index`: la página solo las coordina |
| `Components/AuditoriaFiltros.vue` · `AuditoriaTabla.vue` | Piezas de `Admin/Auditoria/Index` |
| Menú de cada *layout* | Muestra solo las entradas cuyo *Gate* está en `auth.permisos` (el frontend **oculta**, el backend **prohíbe**; la matriz no se repite en el frontend) |

## 5. Comportamiento

- **Inicio de sesión:** al entrar, `astronauta` y `cmo` van a `/astronauta` (F1); `cmo` tiene además F2 en el menú; `medico_vuelo` va a `/tierra`; `ingeniero_biomedico`, `investigador` y `admin` van a F4. Si el rol no trabaja en ese nodo, el formulario muestra el mensaje del backend ("Este usuario trabaja en el nodo Tierra").
- **Sin "Recordarme" ni "Registrarse"** en el formulario.
- **PWA (HU-15.04):** tras iniciar sesión, F1 recibe el token `registros:escribir` una sola vez en `flash.tokenPwa` y lo guarda en IndexedDB (`credenciales`, M15) **por usuario**, junto con su `user_id`. Nunca en `localStorage`.
- **Tableta compartida:** cada elemento de la cola offline guarda el `user_id` de quien lo registró y **solo se envía con el token de ese usuario**. Si otra persona inicia sesión en la misma tableta, los pendientes del anterior se conservan y se envían cuando él vuelva a entrar ([M15 frontend §4](../../M15-sincronizacion/frontend/README.md#4-cola-offline-de-la-pwa-resourcesjsoffline)).
- **Sesión caducada:** un `419` o `401` lleva al inicio de sesión **sin borrar** la cola offline.
- **Cerrar sesión:** borra de `credenciales` el token del usuario que sale, pero no sus pendientes.

## 6. Accesibilidad

- El formulario de inicio de sesión funciona solo con teclado y anuncia los errores con `aria-live`.
- Las tablas de F4 tienen encabezados `<th scope="col">` y paginación accesible.

## 7. Pruebas mínimas (Vitest)

- [ ] `usePermisos` devuelve `true` solo para los *Gates* de `auth.permisos`.
- [ ] Con dos usuarios en la misma tableta, un pendiente de T2 nunca se envía con el token de T3.
