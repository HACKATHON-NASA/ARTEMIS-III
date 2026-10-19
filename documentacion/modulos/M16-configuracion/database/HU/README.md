# M16 · Configuración · Historias de base de datos

> [← Ficha del módulo](../../README.md) · [Ficha de base de datos](../README.md) · [Historias de backend](../../backend/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **MySQL** de cada historia de M16. Lo hace **Julieth** (responsable del backend). Los puntos son los del [plan de sprints](../../../../scrum/plan-de-sprints.md) y cubren todas las capas.

## Reglas comunes

- Se siguen las [convenciones de base de datos](../../../README.md#7-convenciones-de-base-de-datos). Migraciones en `app/Modules/Configuracion/Database/Migrations/`; **una migración fusionada no se edita** (los cambios de valores sembrados van en una migración de datos nueva).
- **Cada valor lleva su fuente** o se marca como parámetro de diseño (†). Los valores salen de la [ficha de backend §3.2 a §3.5](../README.md#6-datos-sembrados), **nunca** de la IA.
- **Claves iguales en los dos nodos:** lo que se siembra usa UUID v5 (espacio de nombres de M02); lo que se crea después viaja con su `id`.
- Toda columna `datetime(3)` exige `protected $dateFormat = 'Y-m-d H:i:s.v';` en su modelo.


## Historias

| Historia | Archivo |
|---|---|
| HU-16.01 · Editar los umbrales de alerta | [HU-16.01-editar-los-umbrales-de-alerta.md](HU-16.01-editar-los-umbrales-de-alerta.md) |
| HU-16.02 · Gestionar el catálogo de exámenes y sus rangos | [HU-16.02-gestionar-el-catalogo-de-examenes-y-sus-rangos.md](HU-16.02-gestionar-el-catalogo-de-examenes-y-sus-rangos.md) |
| HU-16.03 · Ajuste temporal de un umbral durante un apagón | [HU-16.03-ajuste-temporal-de-un-umbral-durante-un-apagon.md](HU-16.03-ajuste-temporal-de-un-umbral-durante-un-apagon.md) |
