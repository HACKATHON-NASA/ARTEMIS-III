# M04 · Signos vitales · Historias de base de datos

> [← Ficha del módulo](../../README.md) · [Ficha de base de datos](../README.md) · [Historias de backend](../../backend/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **MySQL** de cada historia de M04. Lo hace **Julieth** (responsable del backend). Los puntos son los del [plan de sprints](../../../../scrum/plan-de-sprints.md) y cubren todas las capas.

## Reglas comunes

- Se siguen las [convenciones de base de datos](../../../README.md#7-convenciones-de-base-de-datos). Migraciones en `app/Modules/SignosVitales/Database/Migrations/`; **una migración fusionada no se edita**.
- Las referencias a tripulantes (M02) y usuarios (M01) son **lógicas**: columna indexada, sin clave foránea.
- Toda columna `datetime(3)` exige `protected $dateFormat = 'Y-m-d H:i:s.v';` en su modelo.
- Los archivos (señal de ECG) van en el disco privado `local` de Laravel, **nunca** en `public/`. En Laravel 11+ su raíz es `storage/app/private`, así que la ruta guardada es relativa al disco: `ecg/<tripulante_id>/<id>.json`.


## Historias

| Historia | Archivo |
|---|---|
| HU-04.01 · Ver mis signos vitales actuales y su tendencia | [HU-04.01-ver-mis-signos-vitales-actuales-y-su-tendencia.md](HU-04.01-ver-mis-signos-vitales-actuales-y-su-tendencia.md) |
| HU-04.03 · Registrar una prueba ortostática | [HU-04.03-registrar-una-prueba-ortostatica.md](HU-04.03-registrar-una-prueba-ortostatica.md) |
| HU-04.02 · Signos vitales de toda la tripulación con semáforo | [HU-04.02-signos-vitales-de-toda-la-tripulacion-con-semaforo.md](HU-04.02-signos-vitales-de-toda-la-tripulacion-con-semaforo.md) |
| HU-04.04 · Ver el ECG de una ventana de tiempo | [HU-04.04-ver-el-ecg-de-una-ventana-de-tiempo.md](HU-04.04-ver-el-ecg-de-una-ventana-de-tiempo.md) |
| HU-04.05 · Detectar irregularidades del ritmo | [HU-04.05-detectar-irregularidades-del-ritmo.md](HU-04.05-detectar-irregularidades-del-ritmo.md) |
