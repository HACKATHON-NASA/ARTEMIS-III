# M07 · Exámenes médicos · Historias de base de datos

> [← Ficha del módulo](../../README.md) · [Ficha de base de datos](../README.md) · [Historias de backend](../../backend/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **MySQL** de cada historia de M07. Lo hace **Julieth** (responsable del backend). Los puntos son los del [plan de sprints](../../../../scrum/plan-de-sprints.md) y cubren todas las capas.

## Reglas comunes

- Se siguen las [convenciones de base de datos](../../../README.md#7-convenciones-de-base-de-datos). Migraciones en `app/Modules/Examenes/Database/Migrations/`; **una migración fusionada no se edita**.
- **Resultados y documentos son inmutables:** una corrección o un reemplazo es una fila **nueva** con `anula_id` apuntando a la anterior. Nunca se hace `UPDATE` sobre la fila vieja. La ficha anterior marcaba el PDF viejo con `anulado_por`, que obligaba a modificarlo.
- **Vigente** = la fila que ninguna otra anula (`NOT EXISTS (… WHERE x.anula_id = r.id)`).
- Los PDF van en el disco privado `local` (en Laravel 11+ su raíz es `storage/app/private`), nunca en `public/`.
- Toda columna `datetime(3)` exige `protected $dateFormat = 'Y-m-d H:i:s.v';` en su modelo.


## Historias

| Historia | Archivo |
|---|---|
| HU-07.01 · Cargar los resultados de mis exámenes | [HU-07.01-cargar-los-resultados-de-mis-examenes.md](HU-07.01-cargar-los-resultados-de-mis-examenes.md) |
| HU-07.03 · Evolución de cada prueba en una gráfica con su rango | [HU-07.03-evolucion-de-cada-prueba-en-una-grafica-con-su-rango.md](HU-07.03-evolucion-de-cada-prueba-en-una-grafica-con-su-rango.md) |
| HU-07.04 · Recordatorios de exámenes programados | [HU-07.04-recordatorios-de-examenes-programados.md](HU-07.04-recordatorios-de-examenes-programados.md) |
| HU-07.05 · Adjuntar el PDF del laboratorio | [HU-07.05-adjuntar-el-pdf-del-laboratorio.md](HU-07.05-adjuntar-el-pdf-del-laboratorio.md) |
| HU-07.02 · Marcar valores fuera de rango | [HU-07.02-marcar-valores-fuera-de-rango.md](HU-07.02-marcar-valores-fuera-de-rango.md) |
