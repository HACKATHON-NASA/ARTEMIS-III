# M16 · Configuración · Historias de backend

> [← Ficha del módulo](../../README.md) · [Ficha de backend](../README.md) · [Historias de base de datos](../../database/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **Laravel** de cada historia de M16. Lo hace **Julieth**. Las tablas están en las [historias de base de datos](../../database/HU/README.md); las pantallas, en las [historias de frontend](../../frontend/HU/README.md).

## Reglas comunes

- **M16 es la única fuente de los valores configurables.** No evalúa nada (eso es M12).
- **Dónde se edita** ([ficha §3.1](../README.md#31-edición-y-ajustes-temporales)): umbrales, catálogos y parámetros **en la Tierra**; en la nave son de solo lectura, salvo el ajuste temporal del CMO. Las rutas de edición llevan `SoloEnNodo:tierra` (y la del ajuste, `SoloEnNodo:nave`).
- **Todo cambio exige `motivo`** (≤ 300) y se audita: los umbrales los audita M01 al recibir `UmbralModificado` y los catálogos y parámetros, M16 con `RegistroAuditoria`.
- **Parte B** (con HU-15.01 en el Sprint 3 y HU-15.02 en el Sprint 4): cada cambio hecho en la Tierra encola un paquete `configuracion` (prioridad 2) hacia la nave, y cada ajuste de la nave un paquete `ajuste_umbral` (prioridad 2) hacia la Tierra. El `Importador` de M16 aplica las filas con su mismo `id` y publica `UmbralModificado` (`importado = true`).
- **Primero las pruebas** ([plan §10](../../../../scrum/plan-de-sprints.md#10-cómo-trabajar-con-ia-sin-introducir-errores)). Ningún valor clínico sale de la IA.


## Historias

| Historia | Archivo |
|---|---|
| HU-16.01 · Editar los umbrales de alerta | [HU-16.01-editar-los-umbrales-de-alerta.md](HU-16.01-editar-los-umbrales-de-alerta.md) |
| HU-16.02 · Gestionar el catálogo de exámenes y sus rangos | [HU-16.02-gestionar-el-catalogo-de-examenes-y-sus-rangos.md](HU-16.02-gestionar-el-catalogo-de-examenes-y-sus-rangos.md) |
| HU-16.03 · Ajuste temporal de un umbral durante un apagón | [HU-16.03-ajuste-temporal-de-un-umbral-durante-un-apagon.md](HU-16.03-ajuste-temporal-de-un-umbral-durante-un-apagon.md) |
