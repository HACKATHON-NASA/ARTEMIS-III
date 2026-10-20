# M01 · Identidad y acceso · Backend

> [← Ficha del módulo](../README.md) · **Backend** · [Frontend](../frontend/README.md) · [Base de datos](../database/README.md)

Capa **Laravel** del módulo: API, eventos, reglas de negocio, contratos, permisos, archivos y pruebas. Las tablas están en [Base de datos](../database/README.md) y las páginas Vue en [Frontend](../frontend/README.md).

---

## 1. API REST

**M01 no tiene API REST propia.** Emitir y revocar tokens se hace desde pantallas con sesión, así que usa rutas **web** de Inertia (con CSRF). Si fueran rutas `/api/v1/...` con sesión, contradirían la regla de que las escrituras en la API exigen token (§3.3).

| Método | Ruta web | Autorización | Descripción |
|---|---|---|---|
| `GET`, `POST` | `/admin/usuarios` | Gate `gestionar-usuarios` | Lista y alta de usuarios |
| `PUT` | `/admin/usuarios/{user}` y `/admin/usuarios/{user}/contrasena` | Gate `gestionar-usuarios` | Cambio de datos, rol o estado; contraseña temporal nueva |
| `GET`, `POST` | `/admin/tokens` | Gate `emitir-tokens` (`POST` solo en la nave) | Lista de tokens; emite un token de dispositivo (texto plano **una sola vez**, en el *flash*) |
| `DELETE` | `/admin/tokens/{id}` | Gate `emitir-tokens` (los de nodo, solo `admin`) | Revoca un token |
| `GET` | `/admin/auditoria` | Gate `ver-auditoria` | Consulta de la auditoría |

El inicio de sesión también es web (Inertia). Detalle de cada ruta en las [historias de backend](HU/README.md). El inicio de sesión lo atiende **Laravel Fortify** (del *starter kit*), que llama a `Application/AutenticarUsuario`. El registro público se **elimina en el backend** con `php artisan install:features` (quita `Features::registration()`, sus rutas, su página y sus pruebas), no solo se oculta en la pantalla.

## 2. Eventos

| Publica | Consume |
|---|---|
| — | `UmbralModificado` (M16) → registra la entrada de auditoría (se ignora si el evento llega con `importado = true`) · `Illuminate\Auth\Events\Login`, `Logout` y `Failed` → entradas `login`, `logout` y `login_fallido` |

## 3. Reglas de negocio

### 3.1 Usuarios y roles

1. Un usuario tiene **exactamente un rol**. El CMO tiene, además de sus permisos de CMO, los del astronauta sobre **sus propios datos**.
2. El rol `sistema` solo lo tienen los usuarios técnicos `nodo-nave` y `nodo-tierra`, dueños del token de nodo. **No puede iniciar sesión** ni asignarse desde `Admin/Usuarios`.
3. **Usuario inactivo:** no puede iniciar sesión (la comprobación de `activo` se añade al *Form Request* de inicio de sesión del *starter kit*), sus tokens se revocan al desactivarlo y el middleware `UsuarioActivo` cierra en la siguiente petición cualquier sesión que siga abierta.
4. **Contraseñas:** el admin crea cada usuario con una contraseña temporal y puede ponerle otra. "Olvidé mi contraseña" solo se activa si el nodo tiene correo configurado (`canResetPassword = false` en otro caso). No hay "Recordarme", ni registro, ni verificación de correo, ni doble factor, ni *passkeys*: se quitan del *starter kit* con `install:features` ([HU-01.01](HU/HU-01.01-inicio-de-sesion.md)).
5. **Cambio de rol:** revoca todos los tokens del usuario (el de la PWA lleva los permisos del rol anterior).
6. **Protecciones del admin:** nadie puede cambiarse su propio rol ni desactivarse, y debe quedar al menos un admin activo.
7. **Usuario vinculado a un tripulante:** solo puede tener rol `astronauta` o `cmo`; para cambiarlo, primero se desvincula en M02.

### 3.2 Inicio de sesión por nodo

El mismo código corre en los dos nodos (`APP_NODO`), pero no todos los roles tienen trabajo en ambos. Al iniciar sesión se comprueba con `Rol::puedeIniciarSesionEn()` y, si el rol no trabaja en ese nodo, se rechaza con "Este usuario trabaja en el nodo Tierra." (o "Este usuario trabaja en la nave."). Si el rol cambia con la sesión abierta, el middleware `RolPermitidoEnNodo` la cierra ([HU-01.01](HU/HU-01.01-inicio-de-sesion.md)):

| Nodo | Roles que pueden iniciar sesión |
|---|---|
| `nave` | `astronauta`, `cmo`, `ingeniero_biomedico`, `admin` |
| `tierra` | `medico_vuelo`, `investigador`, `ingeniero_biomedico`, `admin` |

Así ningún rol llega a un inicio que no existe en ese nodo (por ejemplo, `medico_vuelo` → `/tierra` en la nave).

### 3.3 Tokens

1. **Escribir en la API exige un token.** Las rutas `POST` de `/api/v1/...` usan solo `auth:sanctum` con token *Bearer* y el middleware `TokenDeTipo` (`dispositivo`, `pwa` o `nodo`). Ese middleware comprueba la clase del dueño del token y su habilidad, y **rechaza las peticiones autenticadas por sesión**: con la sesión del navegador, Sanctum responde `true` a cualquier `tokenCan()`, y sin esta regla un astronauta con sesión podría enviar lotes como si fuera un sensor. Las rutas `GET` de consulta pueden aceptar sesión.
2. Un token de dispositivo solo puede escribir lecturas del tripulante y de la ubicación asignados a ese dispositivo en M02 (la comprobación la hace M03 con el contrato de M02).
3. **Token de la PWA:** se construye dentro de **HU-15.04** (Sprint 2). Se emite al iniciar sesión (roles `astronauta` y `cmo`, solo si tienen tripulante asociado) y se entrega al frontend una sola vez en `flash.tokenPwa`. Antes de emitirlo se **revoca el token PWA anterior** del mismo usuario; al cerrar sesión también se revoca.
4. **Expiración:** `sanctum.expiration` queda en `null` (global) y cada token fija su propio `expires_at`: 7 días para la PWA; `null` para los de dispositivo y de nodo.
5. **Token de nodo:** el admin lo emite en el nodo que **recibe** (`php artisan identidad:token-nodo nave` en la Tierra, o `tierra` en la nave), lo que revoca el anterior, y se copia en el `.env` del otro nodo como `NODO_REMOTO_TOKEN`, junto con `NODO_REMOTO_URL`. Solo el `admin` puede revocar un token de nodo.
6. **Un token vigente por dispositivo:** emitir otro exige confirmar el reemplazo. Las tabletas no reciben token de dispositivo.
7. **Dueños con UUID:** `personal_access_tokens.tokenable_id` es `varchar(36)` (los dispositivos usan UUID) y `tokenable_type` guarda `user` o `dispositivo` (`Relation::morphMap`). Ver [base de datos](../database/HU/HU-01.03-tokens-de-dispositivo.md).

### 3.4 Auditoría

1. Toda consulta de datos de salud de un tripulante distinto al propio genera una entrada `ver` en `auditoria` (una por página o por exportación, no por fila).
2. **El sondeo no se audita:** las recargas parciales de Inertia (`usePoll`, encabezado `X-Inertia-Partial-Data`) no crean entradas, y una misma persona que vuelve a abrir el mismo recurso del mismo tripulante genera como máximo **una entrada cada 10 minutos**. Sin esta regla, un panel de F2 abierto todo el día crearía ~8.640 entradas.
3. La auditoría no se puede modificar ni borrar: el modelo bloquea `update` y `delete`, y dos *triggers* de la base rechazan cualquier `UPDATE` o `DELETE`. En cPanel no se pueden quitar permisos a una sola tabla, por eso se usan *triggers*. Si el hosting no permite crearlos, queda la protección del modelo y se anota (`identidad:verificar-auditoria`).
4. Cada entrada guarda en `actor` una copia legible de quién actuó (correo, `dispositivo:…`, `nodo:…` o `consola`). Los usuarios no se sincronizan entre nodos, y el `user_id` por sí solo no basta en la Tierra.
5. Las entradas de la nave viajan a la Tierra en un paquete `auditoria` de M15 por cada hora completa (prioridad 3), con el comando `identidad:encolar-auditoria`. Como `auditoria` no admite `UPDATE`, la tabla `auditoria_envios` guarda qué horas ya se encolaron. Esta parte se construye dentro de HU-15.01 (Sprint 3). En el nodo Tierra, cada entrada conserva `nodo_origen`.

## 4. Contratos

| Ofrece (`Identidad\Contratos\…`) | Uso |
|---|---|
| `RegistroAuditoria::registrar(accion, recurso_tipo, recurso_id = null, tripulante_id = null, user_id = null, detalle = null)` (`user_id` solo sin petición HTTP; `detalle`, motivo y antes/después de un cambio de configuración) | Todos los módulos registran accesos a datos de salud |
| `Rol` (*enum*) | Valores de rol compartidos |
| `Importador` de `auditoria` (implementa `Sincronizacion\Contratos\Importador`) | M15 |

| Usa | De |
|---|---|
| `Mision\Contratos\ConsultaTripulantes` | M02 — saber qué tripulante corresponde al usuario |
| `Mision\Contratos\ConsultaDispositivos` (`porId()`, `todos()`) | M02 — emitir tokens de dispositivo |
| `Sincronizacion\Contratos\ColaSincronizacion` | M15 — enviar la auditoría a la Tierra |
| Campo `importado` de los eventos (M15) | No auditar dos veces un `UmbralModificado` importado |

## 5. Permisos

M01 define **la lista completa** de *Gates* del sistema. Recoge la matriz de [02 §7](../../../docs/propuesta/02-modulos-del-sistema.md#7-roles-y-permisos) y los permisos que piden las fichas de los demás módulos; cada módulo usa estos nombres y no define otros.

| Gate | Roles | Lo usa |
|---|---|---|
| `ver-datos-propios` | `astronauta`, `cmo` | Módulos clínicos |
| `ver-datos-tripulacion` | `cmo`, `medico_vuelo` | Módulos clínicos |
| `ver-psicologia-detallada` | `medico_vuelo` (y cada tripulante la suya); el CMO **no**, porque es un compañero de tripulación | M05 |
| `ver-alertas` | `cmo`, `medico_vuelo` | M05, M13 |
| `gestionar-alertas` | `cmo` (solo en la nave; en la Tierra el médico de vuelo responde con recomendaciones) | M13 |
| `gestionar-protocolos` | `admin` | M13 |
| `enviar-recomendaciones` | `medico_vuelo` | M15 |
| `editar-umbrales` | `medico_vuelo` | M16 |
| `ajustar-umbral-temporal` | `cmo` | M16 |
| `editar-catalogos` | `admin` | M16 |
| `ver-configuracion` | `cmo`, `medico_vuelo`, `ingeniero_biomedico`, `admin` | M16 |
| `ver-ambiente` | `cmo`, `medico_vuelo`, `ingeniero_biomedico`, `admin` | M08 |
| `ver-estado-dispositivos` | `cmo`, `medico_vuelo`, `ingeniero_biomedico`, `admin` (y `astronauta` solo los propios) | M02, M03 |
| `registrar-dispositivos` | `ingeniero_biomedico`, `admin` | M02 |
| `emitir-tokens` | `ingeniero_biomedico`, `admin` (revocar un token de nodo: solo `admin`, con `TokenPolicy`) | M01 |
| `ver-enlace` | `cmo`, `medico_vuelo`, `ingeniero_biomedico`, `admin` | M15 |
| `generar-reportes` | `cmo`, `medico_vuelo` | M14 |
| `exportar-anonimizados` | `investigador` | M14 |
| `ver-auditoria` | `admin`, `medico_vuelo` | M01 |
| `gestionar-usuarios` | `admin` | M01 |
| `gestionar-mision` | `admin` | M02 |
| `registrar-linea-base` | `medico_vuelo` | M02 |
| `registrar-ortostatica` | `cmo` | M04 |

Los nombres van en minúsculas con guion medio. `HandleInertiaRequests` comparte `auth.permisos` (la lista de *Gates* que cumple el usuario) para que el frontend no repita la matriz.

## 6. Archivos

```
astrohealth/app/Modules/Identidad/
├── IdentidadServiceProvider.php
├── routes.php                      # Grupo web: /inicio, /admin/usuarios, /admin/tokens, /admin/auditoria
├── Contratos/
│   └── RegistroAuditoria.php
├── Domain/
│   ├── Enums/{Rol,TipoToken,AccionAuditoria,CodigoIdentidad}.php
│   ├── Reglas/ReglasAdministrador.php
│   └── Excepciones/{CredencialesNoAdmitidas,UsuarioDesactivado,RolFueraDeNodo,NoPuedeCambiarSuPropioRol,UltimoAdministrador,UsuarioVinculadoATripulante,DispositivoConToken,TabletaSinToken}.php
├── Application/
│   ├── AutenticarUsuario.php       # Comprobaciones del inicio de sesión (lo llama Fortify::authenticateUsing)
│   ├── GuardarUsuario.php          # Reglas del admin y del tripulante vinculado
│   ├── DesactivarUsuario.php
│   ├── EmitirTokenDispositivo.php
│   ├── EmitirTokenPwa.php          # Revoca el anterior y emite el nuevo (HU-15.04)
│   ├── RevocarToken.php
│   ├── RegistrarAuditoria.php      # Implementa RegistroAuditoria: regla de 10 minutos, sin recargas parciales
│   └── DTOs/{DatosUsuario,ResultadoUsuario}.php
├── Infrastructure/
│   ├── Repositorios/{UsuarioLectura,UsuarioEscritura,TokenLectura,TokenEscritura,AuditoriaLectura,AuditoriaEscritura}Repositorio.php
│   ├── Models/{Auditoria,AuditoriaEnvio}.php       # User sigue en app/Models (lo exigen Fortify y Sanctum)
│   ├── Listeners/{AuditarLogin,AuditarLogout,AuditarLoginFallido,AuditarUmbralModificado}.php
│   └── Sincronizacion/ImportadorAuditoria.php
├── Http/
│   ├── Controllers/Web/{InicioController,UsuarioController,TokenController,AuditoriaController}.php
│   ├── Requests/{LoginRequest,GuardarUsuarioRequest,EmitirTokenRequest}.php
│   ├── Resources/{UsuarioResource,TokenResource,AuditoriaResource}.php
│   └── Middleware/{RedirigirSegunRol,RolPermitidoEnNodo,UsuarioActivo,TokenDeTipo}.php
├── Policies/{GatesIdentidad,TokenPolicy}.php       # Los 23 Gates (queCumple) y la revocación de tokens de nodo
├── Console/{TokenNodoCommand,VerificarAuditoriaCommand,EncolarAuditoriaCommand}.php
├── Database/
│   ├── Migrations/{add_rol_activo_to_users,create_auditoria,create_auditoria_envios}.php
│   ├── Seeders/{UsuariosDePruebaSeeder,UsuariosSistemaSeeder,TokenSimuladorSeeder}.php
│   └── Factories/AuditoriaFactory.php
└── Tests/{Unit,Feature}/
```

Del *starter kit* siguen fuera del módulo `app/Models/User.php`, `app/Providers/FortifyServiceProvider.php` (configura Fortify y llama a `AutenticarUsuario`) y `app/Http/Middleware/HandleInertiaRequests.php` (*props* compartidas). M01 es su dueño.

## 7. Pruebas mínimas (Pest)

- [ ] Cada rol solo accede a las rutas que le permite la sección 5 (prueba de *Feature* por rol y ruta).
- [ ] Un usuario inactivo no puede iniciar sesión ni usar un token; si ya tenía sesión abierta, la siguiente petición lo saca.
- [ ] En el nodo `nave`, `medico_vuelo` no puede iniciar sesión; en el nodo `tierra`, `astronauta` tampoco. Un usuario `sistema` no inicia sesión en ningún nodo.
- [ ] Un astronauta **con sesión** que hace `POST /api/v1/ingesta/lotes` recibe `401` (las escrituras exigen token).
- [ ] Un token de dispositivo recibe `403` en una ruta con `TokenDeTipo:nodo`.
- [ ] Cambiar el rol de un usuario revoca sus tokens; un admin no puede desactivarse a sí mismo.
- [ ] El ingeniero biomédico no puede revocar un token de nodo (`403`).
- [ ] Iniciar sesión dos veces deja **un solo** token PWA vigente para ese usuario.
- [ ] Un token PWA caduca a los 7 días; uno de dispositivo no caduca.
- [ ] Diez recargas parciales de `Cmo/Tripulacion/Index` no crean entradas; abrir dos veces la misma página en 10 minutos crea una sola.
- [ ] Intentar modificar una fila de `auditoria` lanza una excepción, también con `DB::table('auditoria')->update(...)` (*trigger*).
- [ ] `POST /register` devuelve `404`.
- [ ] Los códigos de la sección 8 llegan con su campo (pantallas) o en el sobre JSON (API).

## 8. Códigos de respuesta

M01 no tiene API propia, pero su middleware `TokenDeTipo` responde en todas las rutas `/api/v1` con el [sobre de error](../../00-arquitectura/ARCHITECTURE.md#12-estándar-json). En las pantallas, sus errores de negocio llegan como error de validación del campo indicado.

| Código | HTTP | Mensaje | Dónde |
|---|:---:|---|---|
| `IDENTIDAD_TOKEN_REQUERIDO` | 401 | "Las escrituras en la API requieren un token." | `TokenDeTipo` (petición con sesión) |
| `IDENTIDAD_TOKEN_NO_PERMITIDO` | 403 | "Este token no permite esta operación." | `TokenDeTipo` (tipo o habilidad incorrectos) |
| `IDENTIDAD_USUARIO_DESACTIVADO` | 401 / campo `email` | "Usuario desactivado." (API) · "Este usuario está desactivado. Contacte con la administración." (inicio de sesión) | `TokenDeTipo` · `AutenticarUsuario` |
| `IDENTIDAD_ROL_FUERA_DE_NODO` | campo `email` | "Este usuario trabaja en el nodo Tierra." / "Este usuario trabaja en la nave." | `AutenticarUsuario` |
| `IDENTIDAD_PROPIO_ROL` | campo `rol` o `activo` | "No puede cambiar su propio rol ni desactivarse." | `GuardarUsuario` |
| `IDENTIDAD_ULTIMO_ADMIN` | campo `rol` o `activo` | "Debe quedar al menos un administrador activo." | `GuardarUsuario` |
| `IDENTIDAD_USUARIO_VINCULADO` | campo `rol` | "Este usuario está vinculado al tripulante T2. Desvincúlelo primero en Tripulantes." | `GuardarUsuario` |
| `IDENTIDAD_TABLETA_SIN_TOKEN` | campo `dispositivo_id` | "Las tabletas no usan token de dispositivo: el astronauta usa el de la PWA." | `EmitirTokenDispositivo` |
| `IDENTIDAD_DISPOSITIVO_CON_TOKEN` | campo `dispositivo_id` | "Este dispositivo ya tiene un token vigente. Confirme si quiere reemplazarlo." | `EmitirTokenDispositivo` |
