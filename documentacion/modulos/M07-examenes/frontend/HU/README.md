# M07 · Exámenes médicos · Historias de frontend

> [← Ficha del módulo](../../README.md) · [Ficha de frontend](../README.md) · [Historias de base de datos](../../database/HU/README.md) · [Historias de backend](../../backend/HU/README.md)

Trabajo de **Inertia + Vue 3 + Tailwind CSS v4** de M07: qué páginas hay, **cómo deben verse**, qué textos llevan y cómo se comportan. Lo hace **Gabriela**. Las *props* vienen de las [historias de backend](../../backend/HU/README.md).

## Reglas comunes

- Se siguen la [guía visual provisional](../../../M01-identidad/frontend/HU/README.md#guía-visual-provisional) y la [estructura común de las aplicaciones](../../../M01-identidad/frontend/HU/README.md#estructura-común-de-las-cuatro-aplicaciones) de M01. Textos para el astronauta **de tú**.
- **`InsigniaInterpretacion.vue`** (la usan todas las vistas): icono, texto **y** color. Nunca solo color.

| Valor | Icono y texto | Forma del punto en la gráfica |
|---|---|---|
| `normal` | ● Normal | Círculo |
| `bajo` | ▼ Bajo | Triángulo hacia abajo |
| `alto` | ▲ Alto | Triángulo hacia arriba |
| `critico` | ✖ Crítico | Cruz |

- **Valores** con la coma decimal y los decimales del catálogo ("13,4 g/dL"). **Fechas de muestra** como "10 nov 2026 · D23".
- **El CSV y el PDF requieren conexión:** sin red, sus botones están deshabilitados con el texto "Requiere conexión". El formulario funciona sin red (cola offline).


## Historias

| Historia | Archivo |
|---|---|
| HU-07.01 · Cargar los resultados de mis exámenes | [HU-07.01-cargar-los-resultados-de-mis-examenes.md](HU-07.01-cargar-los-resultados-de-mis-examenes.md) |
| HU-07.03 · Evolución de cada prueba en una gráfica con su rango | [HU-07.03-evolucion-de-cada-prueba-en-una-grafica-con-su-rango.md](HU-07.03-evolucion-de-cada-prueba-en-una-grafica-con-su-rango.md) |
| HU-07.04 · Recordatorios de exámenes programados | [HU-07.04-recordatorios-de-examenes-programados.md](HU-07.04-recordatorios-de-examenes-programados.md) |
| HU-07.05 · Adjuntar el PDF del laboratorio | [HU-07.05-adjuntar-el-pdf-del-laboratorio.md](HU-07.05-adjuntar-el-pdf-del-laboratorio.md) |
| HU-07.02 · Marcar valores fuera de rango | [HU-07.02-marcar-valores-fuera-de-rango.md](HU-07.02-marcar-valores-fuera-de-rango.md) |
