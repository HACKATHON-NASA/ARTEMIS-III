# M06 · Sueño · Historias de base de datos

> [← Ficha del módulo](../../README.md) · [Ficha de base de datos](../README.md) · [Historias de backend](../../backend/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **MySQL** de cada historia de M06. Lo hace **Julieth** (responsable del backend). Los puntos son los del [plan de sprints](../../../../scrum/plan-de-sprints.md) y cubren todas las capas.

## Reglas comunes

- Se siguen las [convenciones de base de datos](../../../README.md#7-convenciones-de-base-de-datos). Migraciones en `app/Modules/Sueno/Database/Migrations/`; **una migración fusionada no se edita**.
- `registros_sueno` es **inmutable**: una corrección o una fusión es un registro nuevo con `anula_id` apuntando al anterior. Nunca se hace `UPDATE` ni `DELETE`.
- **Registro vigente** de una noche = el que **ningún otro anula** (no existe otra fila con `anula_id` = su `id`).
- Toda columna `datetime(3)` exige `protected $dateFormat = 'Y-m-d H:i:s.v';` en su modelo.


## Historias

| Historia | Archivo |
|---|---|
| HU-06.01 · Ver cuánto y qué tan bien dormí | [HU-06.01-ver-cuanto-y-que-tan-bien-dormi.md](HU-06.01-ver-cuanto-y-que-tan-bien-dormi.md) |
| HU-06.03 · Registrar la medicación para dormir | [HU-06.03-registrar-la-medicacion-para-dormir.md](HU-06.03-registrar-la-medicacion-para-dormir.md) |
| HU-06.02 · Aviso por varias noches de mal sueño | [HU-06.02-aviso-por-varias-noches-de-mal-sueno.md](HU-06.02-aviso-por-varias-noches-de-mal-sueno.md) |
