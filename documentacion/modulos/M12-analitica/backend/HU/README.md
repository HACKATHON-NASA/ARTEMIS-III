# M12 · Analítica y línea base · Historias de backend

> [← Ficha del módulo](../../README.md) · [Ficha de backend](../README.md) · [Historias de base de datos](../../database/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **Laravel** de cada historia de M12. Lo hace **Julieth**. Las tablas están en las [historias de base de datos](../../database/HU/README.md); las pantallas, en las [historias de frontend](../../frontend/HU/README.md).

## Reglas comunes

- **Valores configurables por misión** ([M16 §3.6](../../../M16-configuracion/backend/README.md#36-configuración-por-misión)): `tendencia_dias_corta` (7), `tendencia_dias_larga` (30), `ventana_linea_base_dias` (7: la línea base se calcula el día `ventana + 1`), `recuperacion_fc_min` (10: también la ventana tras el ejercicio que no cuenta como reposo), `ewma_lambda`, `tendencia_min_pct`, `linea_base_min_lecturas` (10), `linea_base_min_noches` (5), `tendencia_min_puntos` (4) y `tendencia_p_max` (0,05). Se leen con `ParametrosMision::get()` (o de la fila de umbral indicada) de la **misión del dato**; los números que aparecen en estas historias son el **valor por defecto** y **ninguno se escribe en el código**.
- **M12 no crea alertas** (eso es M13) **ni guarda valores de umbral** (eso es M16). Evalúa, guarda hallazgos y publica `HallazgoDetectado` (solo `aviso` y `critico`).
- **En el nodo Tierra M12 no evalúa reglas.** Solo guarda las líneas base que llegan y atiende las vistas de tendencias.
- **Eventos importados:** M12 ignora los eventos con `importado = true` (M15 lo rellena al construirlos durante una importación), salvo `UmbralModificado`, que siempre invalida la caché de umbrales.
- **Los listeners van en cola** con `viaQueue()` según el `modo` del lote (`historico` o `default`), como el resto de los listeners de `LoteIngerido` ([M03](../../../M03-ingesta/backend/HU/HU-03.01-envio-de-lecturas-por-lotes-con-token.md)).
- **Primero las pruebas** ([plan §10](../../../../scrum/plan-de-sprints.md#10-cómo-trabajar-con-ia-sin-introducir-errores)). Los valores clínicos salen de M16, nunca de la IA.


## Historias

| Historia | Archivo |
|---|---|
| HU-12.01 · Línea base de referencia por tripulante y variable | [HU-12.01-linea-base-de-referencia-por-tripulante-y-variable.md](HU-12.01-linea-base-de-referencia-por-tripulante-y-variable.md) |
| HU-12.02 · Evaluar umbrales según el contexto de actividad | [HU-12.02-evaluar-umbrales-segun-el-contexto-de-actividad.md](HU-12.02-evaluar-umbrales-segun-el-contexto-de-actividad.md) |
| HU-12.03 · Explicación de cada hallazgo | [HU-12.03-explicacion-de-cada-hallazgo.md](HU-12.03-explicacion-de-cada-hallazgo.md) |
| HU-12.04 · Tendencias de 7 y 30 días | [HU-12.04-tendencias-de-7-y-30-dias.md](HU-12.04-tendencias-de-7-y-30-dias.md) |
