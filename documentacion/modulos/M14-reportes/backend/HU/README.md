# M14 · Reportes · Historias de backend

> [← Ficha del módulo](../../README.md) · [Ficha de backend](../README.md) · [Historias de base de datos](../../database/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **Laravel** de M14. Lo hace **Julieth**. La tabla está en las [historias de base de datos](../../database/HU/README.md); las pantallas, en las [historias de frontend](../../frontend/HU/README.md).

## Reglas comunes

- **Valores configurables por misión** ([M16 §3.6](../../../M16-configuracion/backend/README.md#36-configuración-por-misión)): `tendencia_dias_larga` (30) y `sueno_programado_h`, **de la misión del reporte** (`misionId`), para que un reporte de una misión pasada use su propia configuración. Se leen con `ParametrosMision::get()` (o de la fila de umbral indicada) de la **misión del dato**; los números que aparecen en estas historias son el **valor por defecto** y **ninguno se escribe en el código**.
- **M14 solo lee** a través de los contratos de los demás módulos ([ficha §4](../README.md#4-contratos)). No escribe datos clínicos.
- **Confidencialidad** ([M05](../../../M05-psicologia/backend/README.md#33-confidencialidad)): hay dos versiones de cada reporte. `medico` es completa; `cmo` no incluye escalas, cohesión ni métricas cognitivas (de cognición, solo las alertas `COG_DETERIORO`). El CMO **solo** obtiene la versión `cmo`.
- **PDF con `barryvdh/laravel-dompdf`** y plantillas Blade de impresión en `app/Modules/Reportes/Resources/views/`, con la fuente **DejaVu Sans** (la que trae dompdf, con tildes, ñ y "₂"). Sin JavaScript: los datos van en **tablas**.
- **Fechas:** día de misión y UTC ("D12 · 3 nov 2026").
- **Auditoría** (M01): generar o descargar un reporte de otro tripulante → `ver`; exportar → `exportar`.
- **Primero las pruebas** ([plan §10](../../../../scrum/plan-de-sprints.md#10-cómo-trabajar-con-ia-sin-introducir-errores)).


## Historias

| Historia | Archivo |
|---|---|
| HU-14.02 · Exportar datos anonimizados en CSV | [HU-14.02-exportar-datos-anonimizados-en-csv.md](HU-14.02-exportar-datos-anonimizados-en-csv.md) |
| HU-14.01 · Reporte diario por tripulante | [HU-14.01-reporte-diario-por-tripulante.md](HU-14.01-reporte-diario-por-tripulante.md) |
| HU-14.03 · Reporte de misión por tripulante | [HU-14.03-reporte-de-mision-por-tripulante.md](HU-14.03-reporte-de-mision-por-tripulante.md) |
