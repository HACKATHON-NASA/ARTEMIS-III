# M01 · Identidad y acceso · Base de datos

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · [Frontend](../frontend/README.md) · **Base de datos**

Tablas **MySQL** que este módulo posee: es el único que las migra y las escribe ([reglas de comunicación](../../README.md#4-reglas-de-comunicación-entre-módulos)). Siguen las [convenciones de base de datos](../../README.md#7-convenciones-de-base-de-datos).

---

## 1. Tablas

### `users` (del *starter kit*, ampliada)

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `bigint` | Del *starter kit* |
| `name`, `email`, `password` | — | Del *starter kit* |
| `rol` | `varchar(30)` | `astronauta`, `cmo`, `medico_vuelo`, `ingeniero_biomedico`, `investigador`, `admin` y `sistema` (solo usuarios técnicos de los nodos; no inicia sesión) |
| `activo` | `boolean` | Un usuario inactivo no puede iniciar sesión ni usar sus tokens |

> El vínculo con un tripulante está en `tripulantes.user_id` (M02). Los roles `astronauta` y `cmo` deben tener un tripulante asociado.

### `auditoria` (solo inserción)

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | |
| `user_id` | `bigint` nulo | `null` si la acción la hizo un token de dispositivo o de nodo, la consola, o un inicio fallido con un correo inexistente |
| `actor` | `varchar(150)` | Copia legible de quién actuó: correo, `dispositivo:<identificador>`, `nodo:<nombre>` o `consola` |
| `token_id` | `bigint` nulo | Token de Sanctum usado, si aplica |
| `accion` | `varchar(20)` | `login`, `login_fallido`, `logout`, `ver`, `crear`, `anular`, `modificar`, `exportar` |
| `recurso_tipo` | `varchar(60)` | Ej.: `observaciones`, `alertas`, `umbrales` |
| `recurso_id` | `varchar(36)` nulo | |
| `tripulante_id` | `uuid` nulo | Tripulante cuyos datos se tocaron |
| `detalle` | `json` nulo | Motivo y antes/después de un cambio de configuración (M16); nunca datos de salud ni secretos |
| `ip` | `varchar(45)` nulo | `null` sin petición HTTP (consola o cola) |
| `nodo_origen` | `varchar(6)` | `nave` o `tierra` (las entradas de la nave llegan a la Tierra por M15) |
| `ocurrido_en` | `datetime(3)` | |

Índices: `(tripulante_id, ocurrido_en)`, `(user_id, ocurrido_en)` y `(ocurrido_en)` (filtro solo por fechas en F4).

**Solo inserción:** dos *triggers* (`auditoria_sin_update` y `auditoria_sin_delete`) rechazan cualquier `UPDATE` o `DELETE`. Variantes para MySQL y SQLite en las [historias](HU/HU-01.04-auditoria-de-accesos.md).

### `auditoria_envios` (solo en la nave, Sprint 3)

| Columna | Tipo | Notas |
|---|---|---|
| `hora` | `datetime` PK | Hora completa (UTC) cuyas entradas ya se encolaron hacia la Tierra |
| `n_entradas` | `int unsigned` | Puede ser 0 |
| `encolado_en` | `datetime(3)` | |

### Tokens (`personal_access_tokens`, de Sanctum)

| Tipo de token | Dueño (`tokenable`) | Habilidades | Expiración (`expires_at`) | Lo emite |
|---|---|---|---|---|
| Dispositivo | `Mision\Dispositivo` (M02) | `lecturas:escribir` | `null` (sin expiración); revocable | Ingeniero biomédico o admin (HU-01.03) |
| Cola offline de la PWA | `User` | `registros:escribir` | 7 días; un solo token vigente por usuario (el anterior se revoca al emitir uno nuevo) | Automático al iniciar sesión con red (rol `astronauta` o `cmo`) |
| Nodo remoto | Usuario con rol `sistema` (`nodo-nave` o `nodo-tierra`) | `sync:escribir` | `null`; revocable | Admin, con `php artisan identidad:token-nodo` en el nodo que recibe |

`sanctum.expiration` queda en `null`: si se fijara un valor global, también caducarían los tokens de dispositivo y de nodo.

**`tokenable_id` es `varchar(36)`**, no el `bigint` que crea `morphs()` en la migración de Sanctum, porque los dispositivos tienen UUID. `tokenable_type` guarda `user` o `dispositivo` (`Relation::morphMap`).

## 2. Migraciones y seeders

```
├── Database/
│   ├── Migrations/{add_rol_activo_to_users,create_auditoria,create_auditoria_envios}.php
│   └── Seeders/{UsuariosDePruebaSeeder,UsuariosSistemaSeeder,TokenSimuladorSeeder}.php
```

Más la migración de Sanctum (`personal_access_tokens`, con `tokenable_id` de 36 caracteres) en HU-01.01. Detalle en las [historias de base de datos](HU/README.md).

## 3. Relaciones

| Desde | Hacia | Tipo |
|---|---|---|
| `auditoria.user_id` | `users.id` | Lógica (las entradas importadas de la nave traen usuarios de la nave; no hay borrado de usuarios, se desactivan) |
| `auditoria.token_id` | `personal_access_tokens.id` | Lógica (los tokens revocados se borran de Sanctum, la auditoría se conserva) |
| `auditoria.tripulante_id` | `tripulantes.id` (M02) | Lógica |
| `tripulantes.user_id` (M02) | `users.id` | Lógica, vista desde M02 |

> `auditoria.user_id` pasa a ser referencia **lógica**: una entrada importada desde la nave puede apuntar a un usuario que no existe con el mismo `id` en el nodo Tierra. Los seeders crean los mismos usuarios con los mismos `id` en los dos nodos para que la referencia se pueda leer.

## 4. Consultas principales

| Consulta | Índice |
|---|---|
| Accesos a los datos de un tripulante en un periodo (F4) | `(tripulante_id, ocurrido_en)` |
| Actividad de un usuario | `(user_id, ocurrido_en)` |
| Accesos de un periodo, sin más filtros (F4) | `(ocurrido_en)` |
| ¿Ya hay una entrada `ver` de este usuario, recurso y tripulante en los últimos 10 minutos? | `(user_id, ocurrido_en)` |
| Inicio de sesión por correo | `users.email` único (del *starter kit*) |

## 5. Volumen

`auditoria` crece con cada página de datos ajenos que se abre: una fila por página y, como máximo, una cada 10 minutos por usuario, recurso y tripulante. Las recargas por sondeo no se auditan. Del orden de cientos de filas al día en la demo. No se purga.

## 6. Seeders

- `UsuariosDePruebaSeeder`: `id` 1 a 8 ([tabla exacta](HU/HU-01.01-inicio-de-sesion.md)); un usuario por rol (`astronauta@astrohealth.test`, `cmo@…`, `medico@…`, `ingeniero@…`, `investigador@…`, `admin@…`) y dos astronautas más (`astronauta3@…` y `astronauta4@…`) para que cada tripulante de la misión de demostración tenga su usuario ([M02 base de datos §6](../../M02-mision/database/README.md#6-seeders)), con `id` fijos e iguales en los dos nodos. En local usa la contraseña de desarrollo de `.env.example`; en los nodos desplegados (públicos en cPanel) cada contraseña sale de `SEED_PASSWORD_<ROL>` del `.env` del servidor y el *seeder* **falla** si falta alguna. Al final fija `AUTO_INCREMENT` en 1000 (nave) o 2000 (Tierra), para que los usuarios creados después en cada nodo no compartan `id`.
- `UsuariosSistemaSeeder`: `nodo-nave` (`id` 9) y `nodo-tierra` (`id` 10) con rol `sistema`, `activo = true` y una contraseña aleatoria que nadie conoce (no inician sesión).
