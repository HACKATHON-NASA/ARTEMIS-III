# M03 · Ingesta de datos · Historias de backend

> [← Ficha del módulo](../../README.md) · [Ficha de backend](../README.md) · [Historias de base de datos](../../database/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **Laravel** de cada historia de M03. Lo hace **Julieth**. Las tablas están en las [historias de base de datos](../../database/HU/README.md); el aviso de F1, en las [historias de frontend](../../frontend/HU/README.md).

## Reglas comunes

- **Valores configurables por misión** ([M16 §3.6](../../../M16-configuracion/backend/README.md#36-configuración-por-misión)): `sensor_sin_datos_min` (10), `colocacion_artefactos_min` (3) y `colocacion_ventana_min` (5). Se leen con `ParametrosMision::get()`; los números de estas historias son el valor por defecto. La tolerancia de 5 minutos en el futuro y el máximo de 5.000 lecturas por lote son límites técnicos y quedan en el código.
- **M03 es la única puerta de entrada de las lecturas de sensores.** Los registros de formularios (tests, síntomas, exámenes…) no pasan por aquí.
- **Escritura solo con token de dispositivo**: `auth:sanctum` + `TokenDeTipo:dispositivo` de [M01](../../../M01-identidad/backend/HU/HU-01.03-tokens-de-dispositivo.md). Una sesión de navegador recibe `401`.
- **Primero las pruebas** ([plan §10](../../../../scrum/plan-de-sprints.md#10-cómo-trabajar-con-ia-sin-introducir-errores)). Los rangos, las unidades y los códigos salen del catálogo de M16, **nunca** de la IA.
- **Errores `422` legibles por máquina.** Los errores de negocio usan el [sobre de error común](../../../00-arquitectura/ARCHITECTURE.md#12-estándar-json) con `errors: { "<campo>": ["<codigo>"] }` y estos códigos exactos, que el simulador muestra tal cual: `tripulante_desconocido`, `ubicacion_desconocida`, `dispositivo_desconocido`, `dispositivo_no_admitido`, `lote_mixto`, `contexto_requerido`, `contexto_no_permitido`, `ids_repetidos`.
- **Motivos de rechazo de una lectura** (*enum* `MotivoRechazo`): `codigo_desconocido`, `codigo_no_admitido`, `ambito_invalido`, `unidad_invalida`, `fuera_de_rango`, `marca_de_tiempo`.


## Historias

| Historia | Archivo |
|---|---|
| HU-03.01 · Envío de lecturas por lotes con token | [HU-03.01-envio-de-lecturas-por-lotes-con-token.md](HU-03.01-envio-de-lecturas-por-lotes-con-token.md) |
| HU-03.02 · Rechazar lecturas imposibles y marcar las de baja calidad | [HU-03.02-rechazar-lecturas-imposibles-y-marcar-las-de-baja-calidad.md](HU-03.02-rechazar-lecturas-imposibles-y-marcar-las-de-baja-calidad.md) |
| HU-03.03 · Aviso cuando un sensor está mal colocado | [HU-03.03-aviso-cuando-un-sensor-esta-mal-colocado.md](HU-03.03-aviso-cuando-un-sensor-esta-mal-colocado.md) |
