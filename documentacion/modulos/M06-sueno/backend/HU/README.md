# M06 · Sueño · Historias de backend

> [← Ficha del módulo](../../README.md) · [Ficha de backend](../README.md) · [Historias de base de datos](../../database/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **Laravel** de cada historia de M06. Lo hace **Julieth**. Las tablas están en las [historias de base de datos](../../database/HU/README.md); las pantallas, en las [historias de frontend](../../frontend/HU/README.md).

## Reglas comunes

- **Valores configurables por misión** ([M16 §3.6](../../../M16-configuracion/backend/README.md#36-configuración-por-misión)): `sueno_programado_h` (8,5), `sueno_regularidad_noches` (7), `sueno_regularidad_min_noches` (3), `sueno_despertar_utc` ("07:00", hora de despertar que prellena el formulario) y, de la fila `SUENO_MAL`, las horas (6), la eficiencia (85 %) y el número de noches seguidas (`persistencia_valor`, 3). Se leen con `ParametrosMision::get()` (o de la fila de umbral indicada) de la **misión del dato**; los números que aparecen en estas historias son el **valor por defecto** y **ninguno se escribe en el código**.
- **La noche, la eficiencia y el origen los calcula el servidor.** Si el cuerpo trae `noche`, `eficiencia_pct` u `origen`, se ignoran.
- **Autenticación de `POST /api/v1/sueno/registros`** (`auth:sanctum` + `TokenDeTipo:pwa,dispositivo`):

| Token | Tripulante | `origen` | Historia |
|---|---|---|---|
| De un dispositivo de tipo `actigrafo` | El asignado al dispositivo (el del cuerpo se ignora) | `actigrafia` | HU-06.01 |
| De un dispositivo de tipo `simulador` | `tripulante_id` (código) **obligatorio** en el cuerpo | `simulador` | HU-06.01 |
| De otro tipo de dispositivo | — → `403` | — | HU-06.01 |
| PWA (`registros:escribir`) de un `astronauta` o `cmo` | El del usuario (el del cuerpo se ignora) | `manual` | HU-06.03 |

- `actigrafia` y `simulador` son registros **medidos** (tienen prioridad en los horarios y la duración); `manual` es **declarado** (tiene prioridad en la medicación, la luz y el ruido).
- **Primero las pruebas** ([plan §10](../../../../scrum/plan-de-sprints.md#10-cómo-trabajar-con-ia-sin-introducir-errores)). Los umbrales de mal sueño salen de M16 (`SUENO_MAL`), no del código.


## Historias

| Historia | Archivo |
|---|---|
| HU-06.01 · Ver cuánto y qué tan bien dormí | [HU-06.01-ver-cuanto-y-que-tan-bien-dormi.md](HU-06.01-ver-cuanto-y-que-tan-bien-dormi.md) |
| HU-06.03 · Registrar la medicación para dormir | [HU-06.03-registrar-la-medicacion-para-dormir.md](HU-06.03-registrar-la-medicacion-para-dormir.md) |
| HU-06.02 · Aviso por varias noches de mal sueño | [HU-06.02-aviso-por-varias-noches-de-mal-sueno.md](HU-06.02-aviso-por-varias-noches-de-mal-sueno.md) |
