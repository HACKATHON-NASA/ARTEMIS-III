# M12 · Analítica y línea base · Historias de base de datos

> [← Ficha del módulo](../../README.md) · [Ficha de base de datos](../README.md) · [Historias de backend](../../backend/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **MySQL** de cada historia de M12. Lo hace **Julieth** (responsable del backend). Los puntos son los del [plan de sprints](../../../../scrum/plan-de-sprints.md) y cubren todas las capas.

## Reglas comunes

- Se siguen las [convenciones de base de datos](../../../README.md#7-convenciones-de-base-de-datos). Migraciones en `app/Modules/Analitica/Database/Migrations/`; **una migración fusionada no se edita**.
- `lineas_base` **ya existe**: la creó HU-02.03 en el Sprint 1 ([M02](../../../M02-mision/database/HU/HU-02.03-registrar-la-linea-base-previa-al-vuelo.md)). Cualquier columna nueva va en una migración nueva.
- Toda columna `datetime(3)` exige `protected $dateFormat = 'Y-m-d H:i:s.v';` en su modelo.


## Historias

| Historia | Archivo |
|---|---|
| HU-12.01 · Línea base de referencia por tripulante y variable | [HU-12.01-linea-base-de-referencia-por-tripulante-y-variable.md](HU-12.01-linea-base-de-referencia-por-tripulante-y-variable.md) |
| HU-12.02 · Evaluar umbrales según el contexto de actividad | [HU-12.02-evaluar-umbrales-segun-el-contexto-de-actividad.md](HU-12.02-evaluar-umbrales-segun-el-contexto-de-actividad.md) |
| HU-12.03 · Explicación de cada hallazgo | [HU-12.03-explicacion-de-cada-hallazgo.md](HU-12.03-explicacion-de-cada-hallazgo.md) |
| HU-12.04 · Tendencias de 7 y 30 días | [HU-12.04-tendencias-de-7-y-30-dias.md](HU-12.04-tendencias-de-7-y-30-dias.md) |
