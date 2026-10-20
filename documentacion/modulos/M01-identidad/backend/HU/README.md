# M01 · Identidad y acceso · Historias de backend

> [← Ficha del módulo](../../README.md) · [Ficha de backend](../README.md) · [Historias de base de datos](../../database/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **Laravel** (rutas, controladores, middleware, *Gates*, eventos y pruebas Pest) de cada historia de M01. Lo hace **Julieth**. Las tablas y los *seeders* están en las [historias de base de datos](../../database/HU/README.md); las pantallas, en las [historias de frontend](../../frontend/HU/README.md).

## Reglas comunes

- **M01 no tiene API REST propia.** Todas sus pantallas usan rutas **web** de Inertia (sesión y CSRF). Las rutas `/api/v1/...` de escritura son de otros módulos y M01 solo aporta el middleware `TokenDeTipo` que las protege.
- **Primero las pruebas:** cada criterio *Dado / Cuando / Entonces* se convierte en una prueba Pest antes de pedir la implementación ([plan §10](../../../../scrum/plan-de-sprints.md#10-cómo-trabajar-con-ia-sin-introducir-errores)).
- **Mensajes en español:** `APP_LOCALE=es` y `php artisan lang:publish`, con los textos de `auth.php`, `passwords.php` y `validation.php` traducidos en `lang/es/`.
- **El *starter kit* instalado usa Laravel Fortify** (`config/fortify.php`, `app/Providers/FortifyServiceProvider.php`, `app/Actions/Fortify/`). Trae funciones que AstroHealth no usa (registro, verificación de correo, doble factor, *passkeys* y confirmación de contraseña). **Se quitan con la herramienta oficial del kit**, no a mano: borra rutas, páginas, pruebas, migraciones y paquetes de npm de cada función.

  ```powershell
  php artisan install:features
  ```

  En la pregunta *"Which authentication features would you like to enable?"* **se desmarcan las cinco** (barra espaciadora) y se pulsa Enter. Se ejecuta **una sola vez**, al empezar HU-01.01: al terminar, el comando se borra a sí mismo.
- **Errores de negocio:** las reglas lanzan excepciones del módulo (`Domain/Excepciones`) con su código `IDENTIDAD_…` y su campo; el manejador común las convierte en error de validación del campo en las pantallas y en el sobre JSON en la API ([ficha §8](../README.md#8-códigos-de-respuesta)).


## Historias

| Historia | Archivo |
|---|---|
| HU-01.01 · Inicio de sesión | [HU-01.01-inicio-de-sesion.md](HU-01.01-inicio-de-sesion.md) |
| HU-01.02 · Asignación de roles | [HU-01.02-asignacion-de-roles.md](HU-01.02-asignacion-de-roles.md) |
| HU-01.03 · Tokens de dispositivo | [HU-01.03-tokens-de-dispositivo.md](HU-01.03-tokens-de-dispositivo.md) |
| HU-01.04 · Auditoría de accesos | [HU-01.04-auditoria-de-accesos.md](HU-01.04-auditoria-de-accesos.md) |
| HU-15.04 · Token de la PWA (trabajo de M01 dentro de esa historia) | [HU-15.04-token-de-la-pwa.md](HU-15.04-token-de-la-pwa.md) |
