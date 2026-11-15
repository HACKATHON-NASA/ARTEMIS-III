# 📈 M4 · Dashboard · Backend

> [← Backend](../README.md) · Código: `astrohealth/app/Modules/Dashboard/` · Qué construir: [ficha de M4](../../../../documentacion/modulos/M4-dashboard/README.md) · Plan: F6 de `documentacion/PLAN_BACKEND.md`

| Estado   | Fase | Responsable |
| -------- | :--: | ----------- |
| ✅ Hecho |  F6  | Backend     |

**Qué hace:** arma las **gráficas** (un punto por día de misión), la **tendencia de la semana** y la **comparación de la tripulación** (mapa de riesgos, dosis de radiación acumulada y lo que cambió de color). **No tiene tablas:** lee por los contratos de M1 (mediciones), M2 (bandas y tendencia) y M6 (misión y entorno).

---

## 1. Endpoints

| Método | Ruta                                                              | Permiso                                    | Caso de uso       | Respuesta                                                                                                                                                           |
| ------ | ----------------------------------------------------------------- | ------------------------------------------ | ----------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| GET    | `/api/v1/astronauts/{id}/evolution`                               | Gate `health.read`                         | `GetEvolution`    | Por indicador: `points` (día, valor, color), `trend` (`direction`, `worse`, `from`, `to`) y `bands`                                                                 |
| GET    | `/api/v1/crew/overview?astronauts=1,2,3`                          | `crew.read` + `health.read` por tripulante | `GetCrewOverview` | `snapshot` (último valor de cada indicador y cuántos piden atención), `doses` (radiación acumulada), `changes` (cambios de color en 3 días, lo que empeoró primero) |
| GET    | `/api/v1/crew/series?astronauts=&indicator=&range=7\|14\|mission` | `crew.read` + `health.read` por tripulante | `GetCrewSeries`   | `bands` y una serie por tripulante                                                                                                                                  |

Los tripulantes que la persona no puede ver **se quitan en silencio** de la respuesta.

## 2. Cómo se arma una serie

`Servicios/DailySeries`: toma las mediciones de M1 (`MeasurementQuery::series`) y deja **el último valor de cada día de misión** (el día 1 es el inicio; los días se cuentan en UTC, como `MissionCalendar` de M6). El color de cada punto es el que le puso M2 al guardarlo.

## 3. La tendencia de la semana (decide M2)

`TrendAnalyzer::weekTrend()` → regla `WeekTrendRule` de M2:

- compara el valor de hoy con el promedio de hace una semana (3 días);
- si se movió menos que el mínimo del indicador (`config/evaluation.php` → `trend.min_change`), es **estable**;
- **empeora** si se aleja del centro del rango sano (sirve igual para lo que sube y para lo que baja).

Antes esta cuenta la hacía el frontend (`Utils/trends.ts`, borrado): ahora Vue solo la muestra (P4).

## 4. Estructura de archivos

```
app/Modules/Dashboard/
├── DashboardServiceProvider.php · routes.php
├── Application/GetEvolution · GetCrewOverview · GetCrewSeries · Servicios/DailySeries · DTOs/DayPoint
├── Http/Controllers/Api/Evolution · CrewOverview · CrewSeries · Requests/CrewRequest · Resources/DayPoints
└── Tests/Feature/DashboardApiTest
```

## 5. Pruebas

| Prueba                   | Verifica                                                                                                                                                                                  |
| ------------------------ | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `DashboardApiTest`       | Un punto por día (el último del día) · tendencia del sueño que cae · bandas · nadie más ve mi evolución · mapa, cambios y serie de la tripulación solo con quienes puede ver · validación |
| `WeekTrendRuleTest` (M2) | El sueño que cae empeora · la presión que vuelve a lo normal no · cambios chicos son estables · con pocos días no hay tendencia                                                           |

## 6. Datos de la demo

`DemoHistorySeeder` (M1, solo fuera de producción) carga un valor por día del día 1 a ayer para Ares-1, que termina en el último valor de cada indicador y cuenta las historias de la demo: la presión de Elena sube, el sueño de Kenji cae, el oxígeno de Amara baja y una tormenta solar sube la radiación de todos unos días antes de hoy. Cada valor pasa por M2. Cuando el simulador envíe los 30 días, este seeder ya no hace falta.

> 📝 **Documentar siempre:** este documento se actualiza en el mismo _pull request_ que cambia el código.
