# M01 · Identidad y acceso · Historias de backend

> [← Ficha del módulo](../../README.md) · [Ficha de backend](../README.md) · [Historias de base de datos](../../database/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **Laravel** (rutas, controladores, middleware, *Gates*, eventos y pruebas Pest) de cada historia de M01. Lo hace **Julieth**. Las tablas y los *seeders* están en las [historias de base de datos](../../database/HU/README.md); las pantallas, en las [historias de frontend](../../frontend/HU/README.md).

## Reglas comunes

- **M01 no tiene API REST propia.** Todas sus pantallas usan rutas **web** de Inertia (sesión y CSRF). Las rutas `/api/v1/...` de escritura son de otros módulos y M01 solo aporta el middleware `TokenDeTipo` que las protege.
- **Primero las pruebas:** cada criterio *Dado / Cuando / Entonces* se convierte en una prueba Pest antes de pedir la implementación ([plan §10](../../../../scrum/plan-de-sprints.md#10-cómo-trabajar-con-ia-sin-introducir-errores)).
- **Mensajes en español:** `APP_LOCALE=es` y `php artisan lang:publish`, con los textos de `auth.php`, `passwords.php` y `validation.php` traducidos en `lang/es/`.
- **Antes de empezar HU-01.01, comprobar cómo trae el *starter kit* la autenticación.** Hay dos posibilidades y cada tarea indica qué hacer en ambas:

| Si el *starter kit* trae… | Registro | Comprobación extra al iniciar sesión |
|---|---|---|
| Controladores propios (`routes/auth.php` y `app/Http/Requests/Auth/LoginRequest.php`) | Borrar las rutas `register` de `routes/auth.php` y `RegisteredUserController` | Dentro de `LoginRequest::authenticate()`, después de `Auth::attempt` |
| Laravel Fortify (`config/fortify.php`) | Quitar `Features::registration()` de `config/fortify.php` | `Fortify::authenticateUsing(...)` en `FortifyServiceProvider` |


## Historias

| Historia | Archivo |
|---|---|
| HU-01.01 · Inicio de sesión | [HU-01.01-inicio-de-sesion.md](HU-01.01-inicio-de-sesion.md) |
| HU-01.02 · Asignación de roles | [HU-01.02-asignacion-de-roles.md](HU-01.02-asignacion-de-roles.md) |
| HU-01.03 · Tokens de dispositivo | [HU-01.03-tokens-de-dispositivo.md](HU-01.03-tokens-de-dispositivo.md) |
| HU-01.04 · Auditoría de accesos | [HU-01.04-auditoria-de-accesos.md](HU-01.04-auditoria-de-accesos.md) |
| HU-15.04 · Token de la PWA (trabajo de M01 dentro de esa historia) | [HU-15.04-token-de-la-pwa.md](HU-15.04-token-de-la-pwa.md) |
