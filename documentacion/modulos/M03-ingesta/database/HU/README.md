# M03 · Ingesta de datos · Historias de base de datos

> [← Ficha del módulo](../../README.md) · [Ficha de base de datos](../README.md) · [Historias de backend](../../backend/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **MySQL** de cada historia de M03. Lo hace **Julieth** (responsable del backend). Los puntos son los del [plan de sprints](../../../../scrum/plan-de-sprints.md) y cubren todas las capas.

## Reglas comunes

- Se siguen las [convenciones de base de datos](../../../README.md#7-convenciones-de-base-de-datos). Las migraciones viven en `app/Modules/Ingesta/Database/Migrations/` y **una migración fusionada no se edita**.
- `observaciones` es **la tabla grande** del sistema (~1 millón de filas en 30 días). Cada índice extra encarece la inserción: **no se añade ningún índice** que no esté en estas historias.
- `observaciones` y `lotes_ingesta` son de **solo inserción**: ninguna fila se actualiza ni se borra. Una corrección es una lectura nueva con `anula_id`.
- Las filas se escriben con el *query builder* en bloques (`DB::table('observaciones')->insertOrIgnore($bloque)`, 1.000 filas por sentencia), no con Eloquent fila a fila. Por eso `created_at` se rellena a mano.
- Toda columna `datetime(3)` exige en su modelo `protected $dateFormat = 'Y-m-d H:i:s.v';`.


## Historias

| Historia | Archivo |
|---|---|
| HU-03.01 · Envío de lecturas por lotes con token | [HU-03.01-envio-de-lecturas-por-lotes-con-token.md](HU-03.01-envio-de-lecturas-por-lotes-con-token.md) |
| HU-03.02 · Rechazar lecturas imposibles y marcar las de baja calidad | [HU-03.02-rechazar-lecturas-imposibles-y-marcar-las-de-baja-calidad.md](HU-03.02-rechazar-lecturas-imposibles-y-marcar-las-de-baja-calidad.md) |
| HU-03.03 · Aviso cuando un sensor está mal colocado | [HU-03.03-aviso-cuando-un-sensor-esta-mal-colocado.md](HU-03.03-aviso-cuando-un-sensor-esta-mal-colocado.md) |
