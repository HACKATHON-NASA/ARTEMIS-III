# M10 · Físico y metabólico · Historias de base de datos

> [← Ficha del módulo](../../README.md) · [Ficha de base de datos](../README.md) · [Historias de backend](../../backend/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **MySQL** de cada historia de M10. Lo hace **Julieth** (responsable del backend). Los puntos son los del [plan de sprints](../../../../scrum/plan-de-sprints.md) y cubren todas las capas. Toda la épica es *Could*: es la primera que se recorta si hay retraso ([plan §11](../../../../scrum/plan-de-sprints.md#11-plan-de-contingencia)).

## Reglas comunes

- Se siguen las [convenciones de base de datos](../../../README.md#7-convenciones-de-base-de-datos). Migraciones en `app/Modules/FisicoMetabolico/Database/Migrations/`; **una migración fusionada no se edita**.
- Las tres tablas son **de solo inserción** (sin `updated_at`). Un error se corrige con un registro nuevo con `anula_id` apuntando al anterior; el vigente es el que ninguna otra fila anula.
- La **masa corporal** (`29463-7`) y la **glucosa** (`AH-GLUCOSA`) no tienen tabla propia: son observaciones de M03, que llegan por lote desde un dispositivo `medidor_masa` (nuevo en M02) y desde el `cgm`.
- Toda columna `datetime(3)` exige `protected $dateFormat = 'Y-m-d H:i:s.v';` en su modelo.


## Historias

| Historia | Archivo |
|---|---|
| HU-10.01 · Registrar mis sesiones de ejercicio | [HU-10.01-registrar-mis-sesiones-de-ejercicio.md](HU-10.01-registrar-mis-sesiones-de-ejercicio.md) |
| HU-10.03 · Registrar la nutrición y la hidratación | [HU-10.03-registrar-la-nutricion-y-la-hidratacion.md](HU-10.03-registrar-la-nutricion-y-la-hidratacion.md) |
| HU-10.04 · Registrar la fuerza de prensión | [HU-10.04-registrar-la-fuerza-de-prension.md](HU-10.04-registrar-la-fuerza-de-prension.md) |
| HU-10.02 · Tendencia de la masa corporal | [HU-10.02-tendencia-de-la-masa-corporal.md](HU-10.02-tendencia-de-la-masa-corporal.md) |
| HU-10.05 · Recibir la glucosa y evaluar su umbral | [HU-10.05-recibir-la-glucosa-y-evaluar-su-umbral.md](HU-10.05-recibir-la-glucosa-y-evaluar-su-umbral.md) |
