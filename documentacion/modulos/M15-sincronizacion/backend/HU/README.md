# M15 · Sincronización · Historias de backend

> [← Ficha del módulo](../../README.md) · [Ficha de backend](../README.md) · [Historias de base de datos](../../database/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **Laravel** de M15. Lo hace **Julieth**. Las tablas están en las [historias de base de datos](../../database/HU/README.md); las pantallas y la cola de la PWA, en las [historias de frontend](../../frontend/HU/README.md).

## Reglas comunes

- **Valores configurables por misión** ([M16 §3.6](../../../M16-configuracion/backend/README.md#36-configuración-por-misión)): `conjuncion_dias` (14, valor por defecto del comando de demo), además de `retardo_marte_min`, `sync_cupo_*` y `sync_max_bytes_peticion`. Se leen con `ParametrosMision::get()` (o de la fila de umbral indicada) de la **misión del dato**; los números que aparecen en estas historias son el **valor por defecto** y **ninguno se escribe en el código**.
- **Solo M15 habla con el otro nodo.** Los demás módulos encolan con `ColaSincronizacion::encolar()` o implementan `Importador`.
- **Corrección importante: la marca de importación viaja en el evento.** La ficha ponía una marca global (`ContextoImportacion`) mientras dura la importación, pero los eventos se publican **después de confirmar la transacción** y sus *listeners* van **en cola**: cuando se ejecutan, en otro proceso, la marca ya no existe. Los datos importados se habrían reenviado y habrían generado hallazgos dobles. Ahora:
  - todo evento de dominio lleva `public bool $importado`, que se rellena **al construirlo** con `ContextoImportacion::activo()`;
  - los *listeners* de M12, M15 y M01 comprueban `$evento->importado`, no el contexto;
  - `ContextoImportacion::activo()` solo se usa dentro del proceso que importa (al construir el evento).
- **Ningún paso hacia el otro nodo se hace dentro de una petición web larga.** Todo va por el *scheduler* (cron cada minuto) o por la cola.
- **Primero las pruebas** ([plan §10](../../../../scrum/plan-de-sprints.md#10-cómo-trabajar-con-ia-sin-introducir-errores)).


## Historias

| Historia | Archivo |
|---|---|
| HU-15.04 · La app funciona sin conexión | [HU-15.04-la-app-funciona-sin-conexion.md](HU-15.04-la-app-funciona-sin-conexion.md) |
| HU-15.01 · Encolar paquetes hacia la Tierra por prioridad | [HU-15.01-encolar-paquetes-hacia-la-tierra-por-prioridad.md](HU-15.01-encolar-paquetes-hacia-la-tierra-por-prioridad.md) |
| HU-15.02 · Sincronizar sin pérdidas tras un corte de enlace | [HU-15.02-sincronizar-sin-perdidas-tras-un-corte-de-enlace.md](HU-15.02-sincronizar-sin-perdidas-tras-un-corte-de-enlace.md) |
| HU-15.03 · Enviar una recomendación a la nave | [HU-15.03-enviar-una-recomendacion-a-la-nave.md](HU-15.03-enviar-una-recomendacion-a-la-nave.md) |
