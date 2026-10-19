# M08 · Ambiente · Historias de base de datos

> [← Ficha del módulo](../../README.md) · [Ficha de base de datos](../README.md) · [Historias de backend](../../backend/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

**M08 no tiene tablas propias.** Sus series son observaciones ambientales de M03 (`observaciones` con `modulo_nave_id` no nulo), los módulos de la nave están en M02 y las reglas en M16. Estas historias solo fijan **qué consultas** hace el módulo y **qué índices** usa, para que nadie añada tablas ni índices de más. Lo hace **Julieth** (responsable del backend).

## Reglas comunes

- **Ningún índice nuevo en `observaciones`** (la tabla grande; [M03](../../../M03-ingesta/database/HU/HU-03.01-envio-de-lecturas-por-lotes-con-token.md)). Todas las consultas de M08 usan el índice existente `(modulo_nave_id, codigo, medido_en)`.
- **Las lecturas se leen con el contrato de M03** (`ConsultaObservaciones::serieUbicacion`) o con consultas agregadas dentro de un repositorio de M08 que solo **lee** `observaciones`. Los artefactos se excluyen siempre.
- **Promedio horario** = media de las lecturas válidas de una **hora de reloj UTC** (`HH:00:00.000` a `HH:59:59.999`) de un módulo y un código.


## Historias

| Historia | Archivo |
|---|---|
| HU-08.01 · Ver el pCO₂ por módulo de la nave | [HU-08.01-ver-el-pco2-por-modulo-de-la-nave.md](HU-08.01-ver-el-pco2-por-modulo-de-la-nave.md) |
| HU-08.02 · Relacionar el CO₂ alto con las cefaleas | [HU-08.02-relacionar-el-co2-alto-con-las-cefaleas.md](HU-08.02-relacionar-el-co2-alto-con-las-cefaleas.md) |
| HU-08.03 · Alertas por VOC, partículas y ruido con límites con fuente | [HU-08.03-alertas-por-voc-particulas-y-ruido-con-limites-con-fuente.md](HU-08.03-alertas-por-voc-particulas-y-ruido-con-limites-con-fuente.md) |
