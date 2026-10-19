# M11 · Síntomas · Historias de frontend

> [← Ficha del módulo](../../README.md) · [Ficha de frontend](../README.md) · [Historias de base de datos](../../database/HU/README.md) · [Historias de backend](../../backend/HU/README.md)

Trabajo de **Inertia + Vue 3 + Tailwind CSS v4** de M11: qué páginas hay, **cómo deben verse**, qué textos llevan y cómo se comportan. Lo hace **Jhon**. Las *props* vienen de las [historias de backend](../../backend/HU/README.md).

## Reglas comunes

- Se siguen la [guía visual provisional](../../../M01-identidad/frontend/HU/README.md#guía-visual-provisional) y la [estructura común de las aplicaciones](../../../M01-identidad/frontend/HU/README.md#estructura-común-de-las-cuatro-aplicaciones) de M01. Textos para el astronauta **de tú**.
- **Iconos de los síntomas** (`resources/js/contratos/sintomas.ts`): los pone el frontend, porque `catalogo_sintomas` (M16) no tiene columna de icono. Un código sin icono conocido usa uno genérico (●). Iconos del *starter kit* (Lucide):

| Código | Icono (Lucide) |
|---|---|
| `cefalea` | `brain` |
| `nausea`, `vomito` | `frown` |
| `mareo` | `rotate-cw` |
| `dolor_espalda`, `dolor_otro` | `activity` |
| `congestion_nasal` | `wind` |
| `lesion_cutanea`, `prurito` | `hand` |
| `fatiga` | `battery-low` |
| `insomnio` | `moon` |

- **Capturas sin conexión:** `id` con `crypto.randomUUID()` y `encolar()` de M15. El catálogo viaja en la instantánea offline.


## Historias

| Historia | Archivo |
|---|---|
| HU-11.01 · Registrar un síntoma con su intensidad | [HU-11.01-registrar-un-sintoma-con-su-intensidad.md](HU-11.01-registrar-un-sintoma-con-su-intensidad.md) |
| HU-11.02 · Cuestionario de mareo los primeros días | [HU-11.02-cuestionario-de-mareo-los-primeros-dias.md](HU-11.02-cuestionario-de-mareo-los-primeros-dias.md) |
| HU-11.03 · Adjuntar una fotografía a un síntoma | [HU-11.03-adjuntar-una-fotografia-a-un-sintoma.md](HU-11.03-adjuntar-una-fotografia-a-un-sintoma.md) |
