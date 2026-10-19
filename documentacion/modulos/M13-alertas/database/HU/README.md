# M13 · Alertas y casos clínicos · Historias de base de datos

> [← Ficha del módulo](../../README.md) · [Ficha de base de datos](../README.md) · [Historias de backend](../../backend/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **MySQL** de cada historia de M13. Lo hace **Julieth** (responsable del backend). Los puntos son los del [plan de sprints](../../../../scrum/plan-de-sprints.md) y cubren todas las capas.

## Reglas comunes

- Se siguen las [convenciones de base de datos](../../../README.md#7-convenciones-de-base-de-datos). Migraciones en `app/Modules/Alertas/Database/Migrations/`; **una migración fusionada no se edita**.
- **Orden de las migraciones.** `alertas` nace en HU-13.01 y `casos_clinicos` en HU-13.03. Por eso la clave foránea `alertas.caso_clinico_id` se añade **en HU-13.03, con una migración nueva**. Si `alertas` la tuviera desde el principio, su migración fallaría (la tabla de casos aún no existe).
- Alertas y casos **se actualizan** (cambian de estado): llevan `actualizada_en` para que la Tierra aplique solo la copia más reciente. `acciones` y `alerta_hallazgos` son de solo inserción.
- Toda columna `datetime(3)` exige `protected $dateFormat = 'Y-m-d H:i:s.v';` en su modelo.


## Historias

| Historia | Archivo |
|---|---|
| HU-13.01 · Bandeja de alertas ordenada por gravedad | [HU-13.01-bandeja-de-alertas-ordenada-por-gravedad.md](HU-13.01-bandeja-de-alertas-ordenada-por-gravedad.md) |
| HU-13.02 · Confirmar o descartar una alerta | [HU-13.02-confirmar-o-descartar-una-alerta.md](HU-13.02-confirmar-o-descartar-una-alerta.md) |
| HU-13.03 · Caso clínico con protocolo guiado | [HU-13.03-caso-clinico-con-protocolo-guiado.md](HU-13.03-caso-clinico-con-protocolo-guiado.md) |
| HU-13.04 · Protocolos guiados con fuente y estado de validación clínica | [HU-13.04-protocolos-guiados-con-fuente-y-estado-de-validacion-clinica.md](HU-13.04-protocolos-guiados-con-fuente-y-estado-de-validacion-clinica.md) |
