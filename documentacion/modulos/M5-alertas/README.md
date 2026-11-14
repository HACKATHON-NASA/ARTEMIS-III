# 🚨 M5 · Recomendaciones y alertas a Tierra

> [← Módulos](../README.md) · [Plan §3 M5](../../PLAN_ASTROHEALTH.md#-m5--recomendaciones-y-alertas-a-tierra) · Anterior: [M4](../M4-dashboard/README.md) · Siguiente: [M6 Entorno](../M6-entorno/README.md)

| Backend | Frontend | Fase | Responsable | Depende de |
|---|---|:---:|:---:|---|
| `app/Modules/Alertas/` | `resources/js/modules/Alertas/` | 4 | P1 (API) · P3 (animación) | [M1](../M1-registro/README.md) evento `MeasurementsRecorded`, `IndicatorCatalog` · [M6](../M6-entorno/README.md) `MissionQuery` |

## 1. Para qué sirve

Cuando un valor sale mal, dice **qué hacer, paso a paso**. Si es rojo, crea una **alerta** que se puede **enviar a Tierra**, con una cuenta regresiva del retraso real de la comunicación: segundos en la ISS, 1.3 s en la Luna, 20 minutos en Marte.

## 2. En la demo

> *"Valor rojo → recomendación inmediata → 'Enviar a Tierra' con el contador de 20 min."* (20 s)

1. Se registra una presión de 148/94 → aparece el **`AlertBanner` rojo** arriba de la pantalla.
2. `RecommendationCard`: *"Presión alta → reposa 10 min · hidrátate · vuelve a medir"*.
3. **Enviar a Tierra** → el `GroundLinkModal` muestra la señal viajando nave → Tierra, con la cuenta regresiva `19:59…`.
4. Centro de alertas: el historial, con el estado de cada alerta (pendiente, enviada, confirmada).

## 3. Datos

Es dueño de **`recommendations`** y **`alerts`** ([columnas](../../arquitectura/02-base-de-datos.md#m5--recommendations)).

**`RecommendationSeeder`:** una recomendación por indicador en `yellow` y en `red` (20 filas). Por ejemplo:

| Indicador | Estado | Título | Pasos |
|---|---|---|---|
| `bp_sys` | `red` | Presión alta | Reposa 10 min · Hidrátate · Vuelve a medir · Si sigue alta, avisa a Tierra |
| `spo2` | `red` | Oxígeno bajo | Respira lento y profundo · Revisa el soporte vital del módulo · Vuelve a medir en 5 min |
| `sleep` | `yellow` | Sueño corto | Evita pantallas 1 h antes · Mantén el horario · Ajusta la luz de la cabina |
| `radiation` | `red` | Radiación alta | Ve al refugio de la nave · Registra el dosímetro · Espera indicaciones de Tierra |

> Los textos son orientativos para la demo. Cada uno cita su riesgo de la NASA HRP en `risk_ref`.

**Retraso de la comunicación** (`config/environments.php`, a través de `MissionQuery`):

| Entorno | Retraso de una vía usado en la demo |
|---|---|
| `iss` | 1 s |
| `moon` | 1.3 s |
| `transit` | 300 s (5 min) |
| `mars` | 1200 s (20 min) |

## 4. API

| Método | Ruta | Qué hace |
|---|---|---|
| GET | `/api/v1/recommendations?indicator=bp_sys&status=red` | La recomendación |
| GET | `/api/v1/alerts?astronaut_id=&state=pending\|sent\|acknowledged` | El historial, paginado y con la más reciente primero |
| POST | `/api/v1/alerts/{id}/send-to-ground` | La marca como enviada y calcula `ground_eta_at` |
| PATCH | `/api/v1/alerts/{id}/acknowledge` | Tierra la confirma (`acknowledged_at`) |

**`POST /api/v1/alerts/57/send-to-ground`**

```json
{
  "data": {
    "id": 57,
    "astronaut_id": 2,
    "indicator": "bp_sys",
    "level": "red",
    "message": "Presión sistólica 148 mmHg (crítico)",
    "sent_to_ground": true,
    "sent_at": "2026-11-14T08:01:00Z",
    "ground_eta_at": "2026-11-14T08:21:00Z",
    "delay_seconds": 1200,
    "acknowledged_at": null
  },
  "message": "ok"
}
```

## 5. Backend por capas

| Capa | Archivo | Qué hace |
|---|---|---|
| Domain | `Domain/Enums/AlertLevel.php` · `AlertState.php` · `ErrorCode.php` | |
| Domain | `Domain/Reglas/GroundEta.php` | `sentAt + delaySeconds`. Pura |
| Domain | `Domain/Reglas/AlertTransitions.php` | Pendiente → enviada → confirmada. No se envía dos veces ni se confirma sin enviar |
| Application | `Application/CreateAlertsForRedMeasurements.php` | Una alerta por cada medición roja, sin duplicados (`measurement_id` es único) |
| Application | `Application/SendAlertToGround.php` | Valida la transición, pide el retraso a `MissionQuery` (M6) y guarda |
| Application | `Application/AcknowledgeAlert.php` · `GetRecommendation.php` · `ListAlerts.php` | |
| Infrastructure | `Infrastructure/Listeners/OnMeasurementsRecorded.php` | Escucha el evento de M1 y llama a `CreateAlertsForRedMeasurements`. **Reemplaza al *Observer* del plan** ([decisión D11](../../arquitectura/ARCHITECTURE.md#2-decisiones-del-proyecto)) |
| Infrastructure | `Infrastructure/Models/Alert.php` · `Recommendation.php` | |
| Infrastructure | `Infrastructure/Repositorios/AlertRepository.php` · `RecommendationRepository.php` | |
| Http | `Http/Controllers/Api/AlertController.php` · `RecommendationController.php` | |
| Http | `Http/Requests/ListAlertsRequest.php` · `RecommendationRequest.php` | |
| Http | `Http/Resources/AlertResource.php` · `RecommendationResource.php` | |
| Database | `Migrations/` · `Seeders/RecommendationSeeder.php` | |

## 6. Frontend

| Archivo | Qué hace |
|---|---|
| `Components/RecommendationCard.vue` | Título, pasos numerados (uno por línea de `action_text`), enlace "Fuente: NASA HRP" y el botón "Enviar a Tierra" si es rojo |
| Global `components/AlertBanner.vue` | Banner rojo arriba de la pantalla, en todas las páginas, que entra deslizándose. Muestra la alerta pendiente más reciente. "Ver" lleva al centro de alertas |
| `Components/GroundLinkModal.vue` | SVG con la nave, la Tierra y un pulso de luz que viaja entre ellas (GSAP). Cuenta regresiva `mm:ss` hasta `ground_eta_at`, calculada desde la hora del servidor. Al llegar a 0: "Recibido en Tierra ✓" |
| `Components/AlertList.vue` · `AlertItem.vue` | El centro de alertas: filtros por estado, `StatusBadge` y la hora |
| `Composables/useCountdown.ts` | `remaining` en segundos, a partir de `ground_eta_at`. Un solo `setInterval` que se limpia al desmontar |
| `Stores/useAlertsStore.ts` | `pending`, `history`, `load()`, `sendToGround(id)`, `acknowledge(id)` |
| Página `pages/Alerts.vue` | El centro de alertas |

> **El contador de Marte es real:** 20 minutos. Para la demo, `GroundLinkModal` tiene un botón discreto **"⏩ ×60"** que acelera la animación (no cambia el dato guardado).

## 7. Contratos y eventos

| Tipo | Nombre | Detalle |
|---|---|---|
| Escucha | M1 `MeasurementsRecorded` | Crea la alerta si alguna medición viene en rojo |
| Usa | M6 `MissionQuery::groundDelaySeconds(astronautId)` | El retraso según el entorno |
| Usa | M1 `IndicatorCatalog` | El nombre y la unidad del indicador, para el mensaje |

## 8. Códigos de error

| Código | HTTP | Cuándo |
|---|:---:|---|
| `ALERTS_ALREADY_SENT` | 409 | Se envía a Tierra una alerta que ya se envió |
| `ALERTS_NOT_SENT` | 409 | Se confirma una alerta que todavía no se envió |
| `ALERTS_RECOMMENDATION_NOT_FOUND` | 404 | No hay recomendación para ese indicador y estado (por ejemplo, `green`) |

## 9. Criterios de aceptación

| # | Dado | Cuando | Entonces |
|:---:|---|---|---|
| 1 | Un astronauta | Registra una presión de 148/94 | Se crean 2 alertas `red`, una por medición |
| 2 | Una alerta ya creada | El mismo evento llega dos veces | Sigue habiendo una sola alerta por medición |
| 3 | Misión en Marte, alerta pendiente | Se envía a Tierra a las 08:01:00 | `ground_eta_at = 08:21:00` y `delay_seconds = 1200` |
| 4 | Misión en la Luna | Se envía a Tierra | `delay_seconds = 1.3` |
| 5 | Una alerta enviada | Se envía otra vez | 409 `ALERTS_ALREADY_SENT` |
| 6 | Un rojo nuevo | Se vuelve a cualquier página | El `AlertBanner` aparece arriba |
| 7 | Un valor amarillo | Se pide su recomendación | Llega la recomendación amarilla y no se crea alerta |

## 10. Listo cuando

- [ ] Un valor rojo dispara la alerta.
- [ ] La app muestra qué hacer.
- [ ] "Enviar a Tierra" funciona con el contador del entorno.
