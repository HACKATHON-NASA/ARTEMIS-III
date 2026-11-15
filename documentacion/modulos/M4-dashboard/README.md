# 📈 M4 · Dashboard con gráficas en el tiempo

> [← Módulos](../README.md) · [Plan §3 M4](../../PLAN_ASTROHEALTH.md#-m4--dashboard-con-gráficas-en-el-tiempo) · Anterior: [M3](../M3-cuerpo/README.md) · Siguiente: [M5 Alertas](../M5-alertas/README.md)

| Backend | Frontend | Fase | Responsable | Depende de |
|---|---|:---:|:---:|---|
| `app/Modules/Dashboard/` | `resources/js/modules/Dashboard/` | 3 | P4 | [M1](../M1-registro/README.md) `MeasurementQuery` · [M2](../M2-evaluacion/README.md) `TrendAnalyzer`, `Evaluator` · [M6](../M6-entorno/README.md) `MissionQuery` |

## 1. Para qué sirve

Muestra **la historia**: cómo han cambiado los indicadores en los últimos 7 o 30 días, con bandas de color de fondo y una flecha de tendencia. Avisa cuando algo viene empeorando, aunque todavía esté en verde. **No tiene tablas propias.**

## 2. En la demo

> *"La gráfica muestra que su sueño viene cayendo hace 3 días."* (20 s)

1. Una tarjeta por indicador: el valor actual, su color y una flecha ↑ ↓ →.
2. Un banner: *"Tu sueño ha bajado 3 días seguidos"*.
3. Clic en la tarjeta de sueño → una gráfica de línea con las bandas verde, amarilla y roja de fondo.
4. Selector de **7 días / 30 días**. Las gráficas se animan al cargar.

## 3. Datos

No tiene tablas. Lee las mediciones de M1 y pide las tendencias a M2. Las bandas de color salen de los umbrales que M2 devuelve junto con la serie.

## 4. API

| Método | Ruta | Qué hace |
|---|---|---|
| GET | `/api/v1/astronauts/{id}/measurements?indicator=hr&range=7d` | La serie de un indicador, con sus bandas de umbral. `range`: `7d` o `30d` |
| GET | `/api/v1/astronauts/{id}/trends` | La tendencia de los 10 indicadores |

**Serie**

```json
{
  "data": {
    "indicator": "sleep",
    "unit": "h",
    "range": "7d",
    "bands": { "green_min": 7, "green_max": 9, "yellow_min": 6, "yellow_max": 24 },
    "points": [
      { "measured_at": "2026-11-08T07:00:00Z", "value": 7.4, "status": "green", "source": "simulated_reference" },
      { "measured_at": "2026-11-09T07:00:00Z", "value": 6.6, "status": "yellow", "source": "simulated_reference" }
    ]
  },
  "message": "ok"
}
```

**Tendencias**

```json
{
  "data": [
    { "indicator": "sleep", "direction": "down", "trend_alert": true, "current": 6.2, "status": "yellow", "message": "Tu sueño ha bajado 3 días seguidos" },
    { "indicator": "hr", "direction": "stable", "trend_alert": false, "current": 71, "status": "green", "message": null }
  ],
  "message": "ok"
}
```

> Para no mandar miles de puntos a Vue: con 30 días y 2 mediciones diarias se agrupa **por día** (promedio) en el repositorio. Con 7 días se envían todos los puntos.

## 5. Backend por capas

| Capa | Archivo | Qué hace |
|---|---|---|
| Domain | `Domain/Enums/Range.php` | `7d` y `30d`, con su número de días |
| Domain | `Domain/Reglas/TrendMessage.php` | Arma el texto: *"Tu {indicador} ha {subido/bajado} {n} días seguidos"* |
| Application | `Application/GetIndicatorSeries.php` | `MeasurementQuery::series()` (M1) + bandas (`Evaluator`/umbrales de M2) |
| Application | `Application/GetTrends.php` | Para cada indicador: los últimos 7 valores (M1) → `TrendAnalyzer` (M2) → mensaje |
| Http | `Http/Requests/SeriesRequest.php` | `indicator` debe ser uno de los 10 y `range` debe ser `7d` o `30d` |
| Http | `Http/Controllers/Api/ShowSeriesController.php` · `ShowTrendsController.php` | Uno por acción |
| Http | `Http/Resources/SeriesResource.php` · `TrendResource.php` | |

> **Bandas:** M4 pide el entorno a M6 (`MissionQuery`) y el umbral a M2 (`Evaluator::thresholdFor`).

## 6. Frontend

| Archivo | Qué hace |
|---|---|
| `Components/IndicatorCard.vue` | Valor actual, `StatusBadge`, flecha ↑ ↓ → y unidad. Clic → selecciona el indicador |
| `Components/IndicatorGrid.vue` | Las 10 tarjetas, en una cuadrícula responsiva |
| `Components/TrendChart.vue` | `vue-chartjs` (`Line`). Bandas de fondo con un *plugin* propio de Chart.js que pinta rectángulos de `green_min`–`green_max` y de la banda amarilla, con los colores de `lib/status.ts` al 12 % de opacidad. Cada punto lleva el color de su estado |
| `Components/RangeToggle.vue` | El selector de 7 y 30 días |
| `Components/TrendBanner.vue` | El aviso amarillo de *"ha bajado 3 días seguidos"*. Si hay varios, se muestra el más grave |
| `Stores/useDashboardStore.ts` | `trends`, `series`, `selected`, `range`, `loadTrends()`, `loadSeries()` |
| Principal `modules/Dashboard/Dashboard.vue` | Banner → cuadrícula → gráfica del indicador seleccionado |

**Animación de las gráficas:** la de Chart.js (`animation.duration: 600`) y, con `prefers-reduced-motion`, `animation: false`.

## 7. Contratos y eventos

| Tipo | Nombre | Detalle |
|---|---|---|
| Usa | M1 `MeasurementQuery::series()` | Los puntos |
| Usa | M2 `TrendAnalyzer::trend()` y los umbrales | Dirección, alerta de tendencia y bandas |

## 8. Códigos de error

| Código | HTTP | Cuándo |
|---|:---:|---|
| `VALIDATION_FAILED` | 422 | Un `indicator` o un `range` que no existen |
| `NOT_FOUND` | 404 | El astronauta no existe |

> **Pocos datos no es un error:** con menos de 3 puntos, la tendencia es `stable` sin alerta, y la gráfica dice "Aún no hay suficientes datos".

## 9. Criterios de aceptación

| # | Dado | Cuando | Entonces |
|:---:|---|---|---|
| 1 | 30 días de datos simulados | Se abre el dashboard con 30 días | Se ve una línea por día, con sus bandas de color |
| 2 | Astronauta 2, días 18–21, con el sueño cayendo (evento de la demo) | Se piden las tendencias | `sleep` sale con `trend_alert: true` y aparece el banner |
| 3 | Un indicador con 2 mediciones | Se pide su tendencia | `stable`, sin alerta |
| 4 | El selector en 7 días | Se cambia a 30 días | Se recarga la serie sin recargar la página |
| 5 | `range=90d` | Se pide la serie | 422 `VALIDATION_FAILED` |

## 10. Listo cuando

- [ ] Se ven 30 días de datos simulados con sus colores y sus tendencias.
- [ ] El banner de tendencia aparece con los eventos de la demo.
- [ ] Las gráficas se animan al cargar.
