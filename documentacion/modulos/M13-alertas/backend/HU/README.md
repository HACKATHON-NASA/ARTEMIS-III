# M13 · Alertas y casos clínicos · Historias de backend

> [← Ficha del módulo](../../README.md) · [Ficha de backend](../README.md) · [Historias de base de datos](../../database/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **Laravel** de cada historia de M13. Lo hace **Julieth**. Las tablas están en las [historias de base de datos](../../database/HU/README.md); las pantallas, en las [historias de frontend](../../frontend/HU/README.md).

## Reglas comunes

- **Valores configurables por misión** ([M16 §3.6](../../../M16-configuracion/backend/README.md#36-configuración-por-misión)): `alerta_confirmacion_min` (30) y `falsos_positivos_dias` (30). Se leen con `ParametrosMision::get()` (o de la fila de umbral indicada) de la **misión del dato**; los números que aparecen en estas historias son el **valor por defecto** y **ninguno se escribe en el código**.
- **M13 es el único que crea alertas.** Las crea a partir de `HallazgoDetectado` (M12), que trae `familia`, `sistema`, `modulo_nave_id` y `ocurrencia` ([M12 HU-12.02](../../../M12-analitica/backend/HU/HU-12.02-evaluar-umbrales-segun-el-contexto-de-actividad.md)).
- **Las alertas se gestionan en la nave.** En la Tierra son de solo lectura: el médico de vuelo responde con recomendaciones (M15). Todas las rutas de gestión llevan `SoloEnNodo:nave` y el Gate `gestionar-alertas` (`cmo`). Los protocolos van al revés: se editan en la Tierra (`SoloEnNodo:tierra`, `gestionar-protocolos`) y llegan a la nave en el paquete `configuracion`.
- **Auditoría** (M01): confirmar, descartar, abrir y cerrar casos y alertas, y editar o validar protocolos.
- **Cada cambio** de una alerta o de su caso actualiza `actualizada_en` y, desde la parte B (Sprint 3, con HU-15.01), encola un paquete `alerta` con el estado completo: prioridad 1 si es crítica y 2 si es aviso.
- **Primero las pruebas** ([plan §10](../../../../scrum/plan-de-sprints.md#10-cómo-trabajar-con-ia-sin-introducir-errores)). **La IA no redacta pasos clínicos** (regla 6 del plan).


## Historias

| Historia | Archivo |
|---|---|
| HU-13.01 · Bandeja de alertas ordenada por gravedad | [HU-13.01-bandeja-de-alertas-ordenada-por-gravedad.md](HU-13.01-bandeja-de-alertas-ordenada-por-gravedad.md) |
| HU-13.02 · Confirmar o descartar una alerta | [HU-13.02-confirmar-o-descartar-una-alerta.md](HU-13.02-confirmar-o-descartar-una-alerta.md) |
| HU-13.03 · Caso clínico con protocolo guiado | [HU-13.03-caso-clinico-con-protocolo-guiado.md](HU-13.03-caso-clinico-con-protocolo-guiado.md) |
| HU-13.04 · Protocolos guiados con fuente y estado de validación clínica | [HU-13.04-protocolos-guiados-con-fuente-y-estado-de-validacion-clinica.md](HU-13.04-protocolos-guiados-con-fuente-y-estado-de-validacion-clinica.md) |
