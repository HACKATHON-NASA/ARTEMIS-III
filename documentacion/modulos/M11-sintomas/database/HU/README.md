# M11 · Síntomas · Historias de base de datos

> [← Ficha del módulo](../../README.md) · [Ficha de base de datos](../README.md) · [Historias de backend](../../backend/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **MySQL** de cada historia de M11. Lo hace **Julieth** (responsable del backend). Los puntos son los del [plan de sprints](../../../../scrum/plan-de-sprints.md) y cubren todas las capas.

## Reglas comunes

- Se siguen las [convenciones de base de datos](../../../README.md#7-convenciones-de-base-de-datos). Migraciones en `app/Modules/Sintomas/Database/Migrations/`; **una migración fusionada no se edita**.
- Las tres tablas son **de solo inserción** (sin `updated_at`).
- Las fotos van en el disco privado `local` (en Laravel 11+ su raíz es `storage/app/private`), nunca en `public/`.
- **Los códigos `AH-SINTOMA-<TIPO>` no se guardan en `catalogo_variables`.** Solo viajan en el evento `ObservacionRegistrada`. Algunos superan los 20 caracteres de `catalogo_variables.codigo` (`AH-SINTOMA-CONGESTION_NASAL` tiene 27), y sembrarlos ahí rompería la migración en MySQL. Si alguna tabla futura guarda estos códigos, su columna debe ser `varchar(30)` o más.
- Toda columna `datetime(3)` exige `protected $dateFormat = 'Y-m-d H:i:s.v';` en su modelo.


## Historias

| Historia | Archivo |
|---|---|
| HU-11.01 · Registrar un síntoma con su intensidad | [HU-11.01-registrar-un-sintoma-con-su-intensidad.md](HU-11.01-registrar-un-sintoma-con-su-intensidad.md) |
| HU-11.02 · Cuestionario de mareo los primeros días | [HU-11.02-cuestionario-de-mareo-los-primeros-dias.md](HU-11.02-cuestionario-de-mareo-los-primeros-dias.md) |
| HU-11.03 · Adjuntar una fotografía a un síntoma | [HU-11.03-adjuntar-una-fotografia-a-un-sintoma.md](HU-11.03-adjuntar-una-fotografia-a-un-sintoma.md) |
