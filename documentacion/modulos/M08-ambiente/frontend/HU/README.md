# M08 · Ambiente · Historias de frontend

> [← Ficha del módulo](../../README.md) · [Ficha de frontend](../README.md) · [Historias de base de datos](../../database/HU/README.md) · [Historias de backend](../../backend/HU/README.md)

Trabajo de **Inertia + Vue 3 + Tailwind CSS v4** de M08: qué páginas hay, **cómo deben verse**, qué textos llevan y cómo se comportan. Lo hace **Gabriela**. HU-08.02 no tiene pantalla propia (los avisos los muestra M13). Las *props* vienen de las [historias de backend](../../backend/HU/README.md).

## Reglas comunes

- Se siguen la [guía visual provisional](../../../M01-identidad/frontend/HU/README.md#guía-visual-provisional) y la [estructura común de las aplicaciones](../../../M01-identidad/frontend/HU/README.md#estructura-común-de-las-cuatro-aplicaciones) de M01.
- **Nombres y unidades** (`resources/js/contratos/ambiente.ts`):

| Código | Nombre | Unidad en pantalla | Decimales |
|---|---|---|:---:|
| `AH-PCO2` | pCO₂ | mmHg | 1 |
| `AH-VOC` | Compuestos orgánicos volátiles (VOC) | mg/m³ | 2 |
| `AH-PM` | Partículas | µg/m³ | 0 |
| `AH-RUIDO` | Ruido | dB | 0 |

- **Ningún límite inventado:** las líneas de límite de VOC, partículas y ruido solo se dibujan si llegan en `limites` (regla `AMB_*` activa), siempre con su fuente.


## Historias

| Historia | Archivo |
|---|---|
| HU-08.01 · Ver el pCO₂ por módulo de la nave | [HU-08.01-ver-el-pco2-por-modulo-de-la-nave.md](HU-08.01-ver-el-pco2-por-modulo-de-la-nave.md) |
| HU-08.03 · Alertas por VOC, partículas y ruido con límites con fuente | [HU-08.03-alertas-por-voc-particulas-y-ruido-con-limites-con-fuente.md](HU-08.03-alertas-por-voc-particulas-y-ruido-con-limites-con-fuente.md) |
