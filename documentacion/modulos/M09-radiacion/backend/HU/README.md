# M09 · Radiación · Historias de backend

> [← Ficha del módulo](../../README.md) · [Ficha de backend](../README.md) · [Historias de base de datos](../../database/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **Laravel** de cada historia de M09. Lo hace **Julieth**. Las tablas están en las [historias de base de datos](../../database/HU/README.md); las pantallas, en las [historias de frontend](../../frontend/HU/README.md).

## Reglas comunes

- **Ningún límite en la pantalla está fijo:** `limite_carrera_msv` (600), `limite_evento_msv` (250, a verificar) y el `valor` de `DOSIS_PROYECCION` (80 %) llegan a las páginas como *props*.
- **Valores configurables por misión** ([M16 §3.6](../../../M16-configuracion/backend/README.md#36-configuración-por-misión)): `dosis_hueco_max_min` (5), `dosis_proyeccion_dias` (7), `spe_ventana_h` (24), `spe_min_lecturas` (60), `spe_lecturas_consecutivas` (3) y `dosis_corto_plazo_dias` (30), además de `limite_carrera_msv`, `limite_evento_msv`, `spe_factor` y la fila `DOSIS_PROYECCION` (80 %). Se leen con `ParametrosMision::get()` (o de la fila de umbral indicada) de la **misión del dato**; los números que aparecen en estas historias son el **valor por defecto** y **ninguno se escribe en el código**.
- **M09 no tiene API.** La tasa de dosis entra por `POST /api/v1/ingesta/lotes` (M03) desde el dosímetro personal (`dosimetro-T<n>`) con el código `AH-DOSIS-TASA` en mSv/d.
- **Valores de M16:** `limite_carrera_msv` (600), el 80 % de `DOSIS_PROYECCION` y `spe_factor` (10, parámetro de diseño †). Ninguno se escribe en el código.
- **Artefactos excluidos** en todos los cálculos (`ConsultaObservaciones::serie`, sin `incluirArtefactos`).
- **Primero las pruebas** ([plan §10](../../../../scrum/plan-de-sprints.md#10-cómo-trabajar-con-ia-sin-introducir-errores)).


## Historias

| Historia | Archivo |
|---|---|
| HU-09.01 · Ver mi dosis acumulada frente al límite de carrera | [HU-09.01-ver-mi-dosis-acumulada-frente-al-limite-de-carrera.md](HU-09.01-ver-mi-dosis-acumulada-frente-al-limite-de-carrera.md) |
| HU-09.02 · Alarma inmediata ante un evento solar | [HU-09.02-alarma-inmediata-ante-un-evento-solar.md](HU-09.02-alarma-inmediata-ante-un-evento-solar.md) |
