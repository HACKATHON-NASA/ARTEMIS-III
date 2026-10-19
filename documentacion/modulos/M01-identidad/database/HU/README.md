# M01 · Identidad y acceso · Historias de base de datos

> [← Ficha del módulo](../../README.md) · [Ficha de base de datos](../README.md) · [Historias de backend](../../backend/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **MySQL** (migraciones, *seeders*, *factories* y protección de tablas) de cada historia de M01. Lo hace **Julieth** (responsable del backend). Los puntos de cada historia son los del [plan de sprints](../../../../scrum/plan-de-sprints.md) y cubren las tres capas; aquí no se reparten.

## Reglas comunes

- Se siguen las [convenciones de base de datos](../../../README.md#7-convenciones-de-base-de-datos): `varchar` validado con *enums* de PHP (nunca `ENUM` de MySQL), `datetime(3)` en UTC, InnoDB y `utf8mb4_unicode_ci`.
- Las migraciones de M01 viven en `app/Modules/Identidad/Database/Migrations/`. **Una migración fusionada no se edita**: se crea otra.
- Las pruebas corren con la base que defina `phpunit.xml` (el *starter kit* trae SQLite en memoria). Toda sentencia SQL escrita a mano debe tener su variante para `mysql` y para `sqlite`.
- Toda columna con milisegundos (`datetime(3)`) obliga a que su modelo declare `protected $dateFormat = 'Y-m-d H:i:s.v';`; sin eso, Eloquent guarda la fecha sin milisegundos.


## Historias

| Historia | Archivo |
|---|---|
| HU-01.01 · Inicio de sesión | [HU-01.01-inicio-de-sesion.md](HU-01.01-inicio-de-sesion.md) |
| HU-01.02 · Asignación de roles | [HU-01.02-asignacion-de-roles.md](HU-01.02-asignacion-de-roles.md) |
| HU-01.03 · Tokens de dispositivo | [HU-01.03-tokens-de-dispositivo.md](HU-01.03-tokens-de-dispositivo.md) |
| HU-01.04 · Auditoría de accesos | [HU-01.04-auditoria-de-accesos.md](HU-01.04-auditoria-de-accesos.md) |
| HU-15.04 · Token de la PWA (trabajo de M01 dentro de esa historia) | [HU-15.04-token-de-la-pwa.md](HU-15.04-token-de-la-pwa.md) |
