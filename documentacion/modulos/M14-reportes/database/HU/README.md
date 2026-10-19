# M14 · Reportes · Historias de base de datos

> [← Ficha del módulo](../../README.md) · [Ficha de base de datos](../README.md) · [Historias de backend](../../backend/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **MySQL** de M14. Lo hace **Julieth** (responsable del backend). Los puntos son los del [plan de sprints](../../../../scrum/plan-de-sprints.md) y cubren todas las capas.

## Reglas comunes

- Se siguen las [convenciones de base de datos](../../../README.md#7-convenciones-de-base-de-datos). Migraciones en `app/Modules/Reportes/Database/Migrations/`; **una migración fusionada no se edita**.
- Los archivos (PDF y CSV) van en el disco privado `local` (en Laravel 11+ su raíz es `storage/app/private`), en `reportes/<tipo>/<id>.<pdf|csv>`. Nunca en `public/`.
- Toda columna `datetime(3)` exige `protected $dateFormat = 'Y-m-d H:i:s.v';` en su modelo.


## Historias

| Historia | Archivo |
|---|---|
| HU-14.02 · Exportar datos anonimizados en CSV | [HU-14.02-exportar-datos-anonimizados-en-csv.md](HU-14.02-exportar-datos-anonimizados-en-csv.md) |
| HU-14.01 · Reporte diario por tripulante | [HU-14.01-reporte-diario-por-tripulante.md](HU-14.01-reporte-diario-por-tripulante.md) |
| HU-14.03 · Reporte de misión por tripulante | [HU-14.03-reporte-de-mision-por-tripulante.md](HU-14.03-reporte-de-mision-por-tripulante.md) |
