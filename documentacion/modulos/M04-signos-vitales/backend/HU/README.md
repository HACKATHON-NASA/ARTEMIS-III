# M04 · Signos vitales · Historias de backend

> [← Ficha del módulo](../../README.md) · [Ficha de backend](../README.md) · [Historias de base de datos](../../database/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **Laravel** de cada historia de M04. Lo hace **Julieth**. Las tablas están en las [historias de base de datos](../../database/HU/README.md); las pantallas, en las [historias de frontend](../../frontend/HU/README.md).

## Reglas comunes

- **Valores configurables por misión** ([M16 §3.6](../../../M16-configuracion/backend/README.md#36-configuración-por-misión)): `vital_reciente_min` (10), `pa_reciente_h` (24) y `flecha_tendencia_pct` (5), además de las filas de umbral de M16 (`PA_ORTO_*`, `ECG_*`). Se leen con `ParametrosMision::get()`; los números de estas historias son el valor por defecto. El retraso de 120 s del recálculo diario es técnico y queda en el código.
- **M04 no evalúa umbrales.** Publica métricas (`ObservacionRegistrada`) y M12 las evalúa con las reglas de M16. M04 tampoco calcula el semáforo: lo pide a M13 (`EstadoSemaforo`).
- **Contratos que aún no existen.** El semáforo de M13 llega en el Sprint 3 (HU-13.01). Hasta entonces el controlador envía `semaforo: null` (con `app()->bound(EstadoSemaforo::class)`), y la página no lo muestra.
- **Auditoría:** cada página que muestra los vitales de **otro** tripulante (o de toda la tripulación) llama a `RegistroAuditoria::registrar(ver, …)` de M01. Las recargas por `usePoll` no cuentan (lo filtra M01).
- **Primero las pruebas** ([plan §10](../../../../scrum/plan-de-sprints.md#10-cómo-trabajar-con-ia-sin-introducir-errores)). Los umbrales clínicos salen de M16, **nunca** de la IA ni de constantes en M04.
- **Escritura en la API solo con token** (`TokenDeTipo` de M01): `dispositivo` para el ECG y `pwa` para la prueba ortostática.


## Historias

| Historia | Archivo |
|---|---|
| HU-04.01 · Ver mis signos vitales actuales y su tendencia | [HU-04.01-ver-mis-signos-vitales-actuales-y-su-tendencia.md](HU-04.01-ver-mis-signos-vitales-actuales-y-su-tendencia.md) |
| HU-04.03 · Registrar una prueba ortostática | [HU-04.03-registrar-una-prueba-ortostatica.md](HU-04.03-registrar-una-prueba-ortostatica.md) |
| HU-04.02 · Signos vitales de toda la tripulación con semáforo | [HU-04.02-signos-vitales-de-toda-la-tripulacion-con-semaforo.md](HU-04.02-signos-vitales-de-toda-la-tripulacion-con-semaforo.md) |
| HU-04.04 · Ver el ECG de una ventana de tiempo | [HU-04.04-ver-el-ecg-de-una-ventana-de-tiempo.md](HU-04.04-ver-el-ecg-de-una-ventana-de-tiempo.md) |
| HU-04.05 · Detectar irregularidades del ritmo | [HU-04.05-detectar-irregularidades-del-ritmo.md](HU-04.05-detectar-irregularidades-del-ritmo.md) |
