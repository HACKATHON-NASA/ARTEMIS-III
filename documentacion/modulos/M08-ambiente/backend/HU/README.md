# M08 · Ambiente · Historias de backend

> [← Ficha del módulo](../../README.md) · [Ficha de backend](../README.md) · [Historias de base de datos](../../database/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **Laravel** de cada historia de M08. Lo hace **Julieth**. Las consultas están en las [historias de base de datos](../../database/HU/README.md); las pantallas, en las [historias de frontend](../../frontend/HU/README.md).

## Reglas comunes

- **Valores configurables por misión** ([M16 §3.6](../../../M16-configuracion/backend/README.md#36-configuración-por-misión)): `co2_cefalea_ventana_h` (24), `co2_cefalea_intensidad_min` (1) y `co2_objetivo_7d_mmhg` (2,5), además de las filas `CO2_ALTO` y `CO2_CEFALEA` (3,0 mmHg). Se leen con `ParametrosMision::get()` (o de la fila de umbral indicada) de la **misión del dato**; los números que aparecen en estas historias son el **valor por defecto** y **ninguno se escribe en el código**.
- **M08 no tiene API.** Las lecturas entran por `POST /api/v1/ingesta/lotes` (M03) con `ubicacion` = código del módulo de la nave.
- **Ningún límite clínico en el código.** Los valores salen de M16: `CO2_CEFALEA` y `CO2_ALTO` (3,0 mmHg), `AMB_*` (sin valor hasta que el médico de vuelo los cargue con su fuente) y `co2_cefalea_ventana_h` (24 h).
- **Promedio horario** = media de las lecturas válidas de una hora de reloj UTC de un módulo ([BD](../../database/HU/HU-08.01-ver-el-pco2-por-modulo-de-la-nave.md)).
- **Los hallazgos ambientales de módulo son globales** (`tripulante_id = null`, sistema `ambiente`) y la explicación nombra el módulo. El de correlación con cefalea es **por tripulante**.
- **Primero las pruebas** ([plan §10](../../../../scrum/plan-de-sprints.md#10-cómo-trabajar-con-ia-sin-introducir-errores)).


## Historias

| Historia | Archivo |
|---|---|
| HU-08.01 · Ver el pCO₂ por módulo de la nave | [HU-08.01-ver-el-pco2-por-modulo-de-la-nave.md](HU-08.01-ver-el-pco2-por-modulo-de-la-nave.md) |
| HU-08.02 · Relacionar el CO₂ alto con las cefaleas | [HU-08.02-relacionar-el-co2-alto-con-las-cefaleas.md](HU-08.02-relacionar-el-co2-alto-con-las-cefaleas.md) |
| HU-08.03 · Alertas por VOC, partículas y ruido con límites con fuente | [HU-08.03-alertas-por-voc-particulas-y-ruido-con-limites-con-fuente.md](HU-08.03-alertas-por-voc-particulas-y-ruido-con-limites-con-fuente.md) |
