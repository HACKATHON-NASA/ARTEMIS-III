# M05 · Psicología y cognición · Historias de backend

> [← Ficha del módulo](../../README.md) · [Ficha de backend](../README.md) · [Historias de base de datos](../../database/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **Laravel** de cada historia de M05. Lo hace **Julieth**. Las tablas están en las [historias de base de datos](../../database/HU/README.md); las pantallas, en las [historias de frontend](../../frontend/HU/README.md).

## Reglas comunes

- **Valores configurables por misión** ([M16 §3.6](../../../M16-configuracion/backend/README.md#36-configuración-por-misión)): `cog_z_aviso` (2), `cog_racha_aviso` (3), `cog_z_fuerte` (3), `cog_racha_fuerte` (2), `cog_lapsos_factor` (2), `cog_lapsos_min` (2), `cog_pendiente_dias` (7), `contexto_sueno_noches` (4), `contexto_co2_h` (24), `contexto_escalas_dias` (3), `contexto_rmssd_h` (24) y `test_atraso_min` (60), además de los que ya tenía (`pvt_*`, `aritmetica_items`, `memoria_*`, `sigma_min`, `sigma_min_rel`, `cog_sesiones_*`). Se leen con `ParametrosMision::get()` (o de la fila de umbral indicada) de la **misión del dato**; los números que aparecen en estas historias son el **valor por defecto** y **ninguno se escribe en el código**.
- **El servidor no confía en el cliente:** recalcula las métricas, `completa`, `es_correcta` (aritmética y memoria) y la falsa salida por tiempo (TR < 100 ms) a partir de los ítems.
- **Autenticación de las rutas de escritura** (`auth:sanctum` + `TokenDeTipo:pwa,dispositivo` de M01):

| Token | `tripulante_id` | `origen` |
|---|---|---|
| PWA (`registros:escribir`) de un `astronauta` o `cmo` | **Se toma del usuario** (`ConsultaTripulantes::porUsuario`); el del cuerpo se ignora | `app` |
| De dispositivo, **solo si el dueño es de tipo `simulador`** (otro dispositivo → `403`) | **Obligatorio** en el cuerpo (código `T1`…); si falta → `422` | `simulador` |

- **Confidencialidad** ([ficha §3.3](../README.md#33-confidencialidad)): las escalas y la evolución detallada solo las ven el propio tripulante y el médico de vuelo (`ver-psicologia-detallada`). El CMO solo ve las **alertas** cognitivas.
- **Idempotencia:** por `id` de la sesión y por `id` de cada escala. Respuestas `201` (nuevo), `200` (ya existía), `422`, `401`/`403`.
- **Parámetros** de M16 (`ParametrosMision::get()`): `pvt_duracion_s` (180), `pvt_intervalo_min_s` / `max_s` (2 y 10), `pvt_espera_max_s` (30, nuevo), `memoria_longitud_inicial` (3), `memoria_ms_por_elemento` (1000), `memoria_longitud_tope` (12, nuevo), `sigma_min` y `sigma_min_rel` (nuevo).
- **Primero las pruebas** ([plan §10](../../../../scrum/plan-de-sprints.md#10-cómo-trabajar-con-ia-sin-introducir-errores)). Las σ mínimas y los criterios de la regla salen de M16 y de este documento, **nunca** de la IA.


## Historias

| Historia | Archivo |
|---|---|
| HU-05.01 · Realizar un test de tiempo de reacción (PVT) | [HU-05.01-realizar-un-test-de-tiempo-de-reaccion-pvt.md](HU-05.01-realizar-un-test-de-tiempo-de-reaccion-pvt.md) |
| HU-05.02 · Realizar un test de aritmética | [HU-05.02-realizar-un-test-de-aritmetica.md](HU-05.02-realizar-un-test-de-aritmetica.md) |
| HU-05.07 · Realizar un test de memoria de secuencias | [HU-05.07-realizar-un-test-de-memoria-de-secuencias.md](HU-05.07-realizar-un-test-de-memoria-de-secuencias.md) |
| HU-05.03 · Registrar ánimo, estrés y fatiga | [HU-05.03-registrar-animo-estres-y-fatiga.md](HU-05.03-registrar-animo-estres-y-fatiga.md) |
| HU-05.05 · Recordatorios de los tests programados | [HU-05.05-recordatorios-de-los-tests-programados.md](HU-05.05-recordatorios-de-los-tests-programados.md) |
| HU-05.04 · Detectar un deterioro cognitivo sostenido | [HU-05.04-detectar-un-deterioro-cognitivo-sostenido.md](HU-05.04-detectar-un-deterioro-cognitivo-sostenido.md) |
| HU-05.06 · Ver la evolución psicológica de cada tripulante | [HU-05.06-ver-la-evolucion-psicologica-de-cada-tripulante.md](HU-05.06-ver-la-evolucion-psicologica-de-cada-tripulante.md) |
