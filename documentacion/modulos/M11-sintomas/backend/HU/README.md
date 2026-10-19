# M11 · Síntomas · Historias de backend

> [← Ficha del módulo](../../README.md) · [Ficha de backend](../README.md) · [Historias de base de datos](../../database/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **Laravel** de cada historia de M11. Lo hace **Julieth**. Las tablas están en las [historias de base de datos](../../database/HU/README.md); las pantallas, en las [historias de frontend](../../frontend/HU/README.md).

## Reglas comunes

- **Valores configurables por misión** ([M16 §3.6](../../../M16-configuracion/backend/README.md#36-configuración-por-misión)): `mareo_dias` (3: el cuestionario aparece los días 1 a `mareo_dias`). Se leen con `ParametrosMision::get()` (o de la fila de umbral indicada) de la **misión del dato**; los números que aparecen en estas historias son el **valor por defecto** y **ninguno se escribe en el código**.
- **Autenticación de las rutas de escritura** (`auth:sanctum` + `TokenDeTipo:pwa,dispositivo`): PWA de un `astronauta` o `cmo` (el tripulante es el del usuario y se ignora el del cuerpo; `origen = manual`) o dispositivo de tipo `simulador` (`tripulante_id` obligatorio; `origen = simulador`; lo usa E3). Otro dispositivo → `403`. Las **fotos** solo con el token de la PWA.
- **Idempotencia** por `id`: `201` nuevo, `200` ya existía.
- **M11 no evalúa umbrales ni crea alertas.** Publica eventos; las cefaleas alimentan `ReglaCo2Cefalea` (M08).
- **Fechas:** ninguna puede estar más de 5 minutos en el futuro.
- **Primero las pruebas** ([plan §10](../../../../scrum/plan-de-sprints.md#10-cómo-trabajar-con-ia-sin-introducir-errores)).


## Historias

| Historia | Archivo |
|---|---|
| HU-11.01 · Registrar un síntoma con su intensidad | [HU-11.01-registrar-un-sintoma-con-su-intensidad.md](HU-11.01-registrar-un-sintoma-con-su-intensidad.md) |
| HU-11.02 · Cuestionario de mareo los primeros días | [HU-11.02-cuestionario-de-mareo-los-primeros-dias.md](HU-11.02-cuestionario-de-mareo-los-primeros-dias.md) |
| HU-11.03 · Adjuntar una fotografía a un síntoma | [HU-11.03-adjuntar-una-fotografia-a-un-sintoma.md](HU-11.03-adjuntar-una-fotografia-a-un-sintoma.md) |
