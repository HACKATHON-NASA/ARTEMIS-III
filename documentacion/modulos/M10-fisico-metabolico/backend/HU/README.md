# M10 · Físico y metabólico · Historias de backend

> [← Ficha del módulo](../../README.md) · [Ficha de backend](../README.md) · [Historias de base de datos](../../database/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **Laravel** de cada historia de M10. Lo hace **Julieth**. Las tablas están en las [historias de base de datos](../../database/HU/README.md); las pantallas, en las [historias de frontend](../../frontend/HU/README.md).

## Reglas comunes

- **Autenticación de las rutas de escritura** (`auth:sanctum` + `TokenDeTipo:pwa,dispositivo`): PWA de un `astronauta` o `cmo` (el tripulante es el del usuario y se ignora el del cuerpo; `origen = manual`) o dispositivo de tipo `simulador` (`tripulante_id` obligatorio; `origen = simulador`). Cualquier otro dispositivo → `403`.
- **Idempotencia** por `id`: `201` nuevo, `200` ya existía. **Corrección**: un registro con `anula_id` apuntando a otro del mismo tripulante.
- **Sin metas ni alertas inventadas:** la nutrición y la fuerza solo se muestran. Las únicas reglas de M10 son `MASA_PERDIDA` y `GLUCOSA`, de M16, y las evalúa el motor genérico de M12.
- **Valores configurables por misión:** `ejercicio_meta_semanal_min` (840) y las filas `MASA_PERDIDA` (5 %) y `GLUCOSA` (70–180 mg/dL) de M16 llegan a las páginas como *props*; ninguna línea de referencia está fija en el frontend.
- **Fechas:** ninguna puede estar más de 5 minutos en el futuro.
- **Primero las pruebas** ([plan §10](../../../../scrum/plan-de-sprints.md#10-cómo-trabajar-con-ia-sin-introducir-errores)).


## Historias

| Historia | Archivo |
|---|---|
| HU-10.01 · Registrar mis sesiones de ejercicio | [HU-10.01-registrar-mis-sesiones-de-ejercicio.md](HU-10.01-registrar-mis-sesiones-de-ejercicio.md) |
| HU-10.03 · Registrar la nutrición y la hidratación | [HU-10.03-registrar-la-nutricion-y-la-hidratacion.md](HU-10.03-registrar-la-nutricion-y-la-hidratacion.md) |
| HU-10.04 · Registrar la fuerza de prensión | [HU-10.04-registrar-la-fuerza-de-prension.md](HU-10.04-registrar-la-fuerza-de-prension.md) |
| HU-10.02 · Tendencia de la masa corporal | [HU-10.02-tendencia-de-la-masa-corporal.md](HU-10.02-tendencia-de-la-masa-corporal.md) |
| HU-10.05 · Recibir la glucosa y evaluar su umbral | [HU-10.05-recibir-la-glucosa-y-evaluar-su-umbral.md](HU-10.05-recibir-la-glucosa-y-evaluar-su-umbral.md) |
