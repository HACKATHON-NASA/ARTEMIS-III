# 🚨 M5 · Recomendaciones y alertas a Tierra

> [← Módulos](../README.md) · [Plan §3 M5](../../PLAN_ASTROHEALTH.md#-m5--recomendaciones-y-alertas-a-tierra) · Anterior: [M4](../M4-dashboard/README.md) · Siguiente: [M6 Entorno](../M6-entorno/README.md)

| Backend | Frontend | Fase | Responsable | Depende de |
|---|---|:---:|:---:|---|
| `app/Modules/Alertas/` | `resources/js/modules/Alertas/` | 4 | P1 (API) · P3 (animación) | [M1](../M1-registro/README.md) evento `MeasurementsRecorded`, `IndicatorCatalog` · [M6](../M6-entorno/README.md) `MissionQuery` |

## 1. Para qué sirve

Es el **canal de salud entre la nave y Tierra**, en vivo. Aquí llega **sola** cualquier novedad de un astronauta:

| Tipo (`kind`) | Quién la crea | Nivel |
|---|---|---|
| `vital` | El sistema, cuando un valor sale en amarillo o rojo (sensores o registro manual) | `yellow` · `red` |
| `sos` | El astronauta: emergencia | `red` |
| `help` | El astronauta: pide ayuda médica, con un motivo | `yellow` |
| `report` | El astronauta: cuenta una novedad no urgente | `info` |

**Toda alerta sale hacia Tierra sola al crearse** (ya no hay botón "Enviar a Tierra") y llega con el retraso real de la comunicación: 1 s en la ISS, 1.3 s en la Luna, 20 minutos en Marte. Tierra la **atiende y responde**, y la respuesta tarda lo mismo en volver a la nave. Para los valores fuera de rango, además, dice **qué hacer, paso a paso**.

## 2. En la demo

> *"Elena pulsa SOS → la señal viaja 20 min a Tierra → al médico le salta el aviso → responde y la respuesta vuelve a la nave."* (30 s)

1. El astronauta tiene **SOS · Pedir ayuda · Novedad** en su Inicio y en el centro de alertas.
2. El `GroundLinkModal` muestra la señal viajando nave → Tierra, con la cuenta regresiva.
3. En Tierra (cualquier pantalla) salta un **aviso en vivo**; el SOS no se cierra solo. El centro pone arriba lo urgente sin atender.
4. **Atender** → respuesta rápida o escrita → el recorrido marca *Enviada → En Tierra → Atendida → Respuesta en la nave*.
5. Interruptor **"Demo ×60"**: el retraso de Marte dura 20 s, para mostrarlo en vivo.

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
| GET | `/api/v1/alerts?astronaut_id=&mission_id=&state=open\|acknowledged&since=` | El historial (la más reciente primero). Con `since`, solo lo nuevo: el frontend pregunta cada 5 s (o se cambia por un canal en tiempo real) |
| POST | `/api/v1/alerts` | El astronauta manda `{ "kind": "sos\|help\|report", "category": "…", "note": "…" }`. Sale hacia Tierra ya: el backend pone `sent_at` y `ground_eta_at` |
| PATCH | `/api/v1/alerts/{id}/acknowledge` | Tierra la atiende con `{ "reply": "…" }` (`acknowledged_at` + `reply`) |

Las de tipo `vital` las crea el backend al escuchar `MeasurementsRecorded` (M1), también con `sent_at` y `ground_eta_at` desde el primer momento.

**Una alerta**

```json
{
  "data": {
    "id": 58,
    "astronaut_id": 1,
    "kind": "sos",
    "indicator": null,
    "level": "red",
    "message": "SOS de Elena",
    "note": "Me desmayé un momento en el módulo de ejercicio",
    "source": "astronaut",
    "sent_at": "2026-11-14T08:01:00Z",
    "ground_eta_at": "2026-11-14T08:21:00Z",
    "delay_seconds": 1200,
    "acknowledged_at": null,
    "reply": null,
    "created_at": "2026-11-14T08:01:00Z"
  },
  "message": "ok"
}
```

> ⚠️ El frontend ya funciona así con datos de prueba (`resources/js/modules/Alertas/Utils/alertsMockData.ts`, compartidos entre ventanas con localStorage para la demo).

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
| Infrastructure | `Repositorios/Alert/FindAlertRepository.php` · `InsertAlertRepository.php` · `UpdateAlertRepository.php` · `Repositorios/Recommendation/FindRecommendationRepository.php` · `UpsertRecommendationRepository.php` | Uno por operación |
| Http | `Http/Controllers/Api/ListAlertsController.php` · `SendAlertToGroundController.php` · `AcknowledgeAlertController.php` · `ListRecommendationsController.php` | Uno por acción |
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
| `Stores/useAlertsStore.ts` | `alerts`, `history`, `pendingTotal`, `refresh()` (en vivo), `send(astronautId, message)`, `acknowledge(id, reply)` |
| `Composables/useLiveAlerts.ts` | Cada 5 s pide lo nuevo y avisa con un toast (lo monta el menú lateral) |
| `Components/AstronautComms.vue` · `MessageDialog.vue` · `AttendDialog.vue` | Los tres botones del astronauta, su formulario y la respuesta de Tierra |
| Principal `modules/Alertas/Alerts.vue` | El centro de alertas |

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


## Indicaciones del médico (frontend con datos de prueba)

El médico de vuelo o el de la tripulación (permiso `advice.send`) le escribe a un tripulante **qué hacer**: tema (sueño, ejercicio, alimentación, medicamento, descanso, salud mental u otra), título, pasos, si es importante y por cuántos días vale. Se escribe desde la **ficha del tripulante** (`/tripulacion/{id}`, `modules/Cuerpo/CrewMember.vue`), viaja con el retraso de la comunicación y le llega al astronauta en su **Inicio** (`AdviceInbox.vue`), donde la marca como **"Entendido"**. En la ficha se ve en qué va cada una: viajando (con cuenta regresiva), en la nave sin leer o entendida.

Rutas propuestas: `GET/POST /api/v1/advice?astronaut_id=` y `PATCH /api/v1/advice/{id}/read`. ⚠️ Hoy en `Utils/adviceMock.ts` (localStorage, compartido entre ventanas).

**Historial por misión** (`/indicaciones`, `modules/Alertas/AdviceHistory.vue`, `health.read.any`): todas las indicaciones de la tripulación de una misión, con cifras (viajando, sin leer, entendidas con su %, importantes sin leer), filtros por estado, tripulante y tema, enlace a la ficha de cada uno y "Nueva indicación" eligiendo a quién (`advice.send`).
