# M15 · Sincronización · Historias de base de datos

> [← Ficha del módulo](../../README.md) · [Ficha de base de datos](../README.md) · [Historias de backend](../../backend/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **MySQL** de M15. Lo hace **Julieth** (responsable del backend). Los puntos son los del [plan de sprints](../../../../scrum/plan-de-sprints.md) y cubren todas las capas. La cola de la PWA (HU-15.04) vive en el navegador (IndexedDB): no tiene tablas.

## Reglas comunes

- Se siguen las [convenciones de base de datos](../../../README.md#7-convenciones-de-base-de-datos). Migraciones en `app/Modules/Sincronizacion/Database/Migrations/`; **una migración fusionada no se edita**.
- **El `id` de un paquete es la clave de idempotencia de punta a punta:** es el mismo en la cola de salida de un nodo y en la de recepción del otro.
- Los `payload` grandes (adjuntos en base64, `detalle` comprimido) van en `longText`.
- Toda columna `datetime(3)` exige `protected $dateFormat = 'Y-m-d H:i:s.v';` en su modelo.


## Historias

| Historia | Archivo |
|---|---|
| HU-15.04 · La app funciona sin conexión | [HU-15.04-la-app-funciona-sin-conexion.md](HU-15.04-la-app-funciona-sin-conexion.md) |
| HU-15.01 · Encolar paquetes hacia la Tierra por prioridad | [HU-15.01-encolar-paquetes-hacia-la-tierra-por-prioridad.md](HU-15.01-encolar-paquetes-hacia-la-tierra-por-prioridad.md) |
| HU-15.02 · Sincronizar sin pérdidas tras un corte de enlace | [HU-15.02-sincronizar-sin-perdidas-tras-un-corte-de-enlace.md](HU-15.02-sincronizar-sin-perdidas-tras-un-corte-de-enlace.md) |
| HU-15.03 · Enviar una recomendación a la nave | [HU-15.03-enviar-una-recomendacion-a-la-nave.md](HU-15.03-enviar-una-recomendacion-a-la-nave.md) |
