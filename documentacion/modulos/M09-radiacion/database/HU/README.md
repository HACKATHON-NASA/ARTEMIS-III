# M09 · Radiación · Historias de base de datos

> [← Ficha del módulo](../../README.md) · [Ficha de base de datos](../README.md) · [Historias de backend](../../backend/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **MySQL** de cada historia de M09. Lo hace **Julieth** (responsable del backend). Los puntos son los del [plan de sprints](../../../../scrum/plan-de-sprints.md) y cubren todas las capas.

## Reglas comunes

- Se siguen las [convenciones de base de datos](../../../README.md#7-convenciones-de-base-de-datos). Migraciones en `app/Modules/Radiacion/Database/Migrations/`; **una migración fusionada no se edita**.
- La **tasa** de dosis (`AH-DOSIS-TASA`, mSv/d) vive en `observaciones` (M03) y se lee con su contrato. M09 guarda solo **resúmenes derivados** (`dosis_diarias`), que se recalculan, por eso admiten `upsert`.
- Toda columna `datetime(3)` exige `protected $dateFormat = 'Y-m-d H:i:s.v';` en su modelo.


## Historias

| Historia | Archivo |
|---|---|
| HU-09.01 · Ver mi dosis acumulada frente al límite de carrera | [HU-09.01-ver-mi-dosis-acumulada-frente-al-limite-de-carrera.md](HU-09.01-ver-mi-dosis-acumulada-frente-al-limite-de-carrera.md) |
| HU-09.02 · Alarma inmediata ante un evento solar | [HU-09.02-alarma-inmediata-ante-un-evento-solar.md](HU-09.02-alarma-inmediata-ante-un-evento-solar.md) |
