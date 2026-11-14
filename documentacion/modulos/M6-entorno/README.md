# 🌍 M6 · Selector de entorno de misión

> [← Módulos](../README.md) · [Plan §3 M6](../../PLAN_ASTROHEALTH.md#-m6--selector-de-entorno-de-misión) · Anterior: [M5](../M5-alertas/README.md) · Siguiente: [M7 Gamificación](../M7-gamificacion/README.md)

| Backend | Frontend | Fase | Responsable | Depende de |
|---|---|:---:|:---:|---|
| `app/Modules/Entorno/` | `resources/js/modules/Entorno/` | **1** (tablas) · 4 (pantalla) | P1 (API) · P3 (UI) | [M2](../M2-evaluacion/README.md) `SpaceWeather` (opcional) |

## 1. Para qué sirve

Define **dónde está la misión** (ISS, tránsito, Luna o Marte) y **quiénes la tripulan**. Al cambiar de entorno cambian la radiación de referencia, los riesgos que se destacan y el retraso de la comunicación con la Tierra. Es dueño de `missions` y `astronauts`, que todos los módulos necesitan: **sus tablas se hacen en la fase 1**.

## 2. En la demo

> *"Cambia a ISS y se ve cómo cambian los riesgos y el retraso."* (10 s)

1. Pantalla **Misión**: 4 tarjetas con ilustración (estación, nave, Luna y Marte).
2. Clic en ISS → el fondo hace una transición, el indicador de gravedad cambia (`0.38 g` → `~0 g`) y el retraso pasa de 20 min a 1 s.
3. Los riesgos destacados cambian. El contador muestra el **día de misión** ("Día 24 de 30").

## 3. Datos

Es dueño de **`missions`** y **`astronauts`** ([columnas](../../arquitectura/02-base-de-datos.md#m6--missions)).

Los datos de cada entorno van en **`config/environments.php`**. No tienen tabla, porque no cambian durante la misión:

| `code` | Nombre | Gravedad | Radiación base (mSv/día) ¹ | Retraso de una vía | Riesgos destacados |
|---|---|---|---|---|---|
| `iss` | Estación Espacial | ~0 g (órbita) | 0.5 | 1 s | Microgravedad, aislamiento |
| `transit` | Tránsito | 0 g | 1.8 | 300 s (crece con la distancia) | Radiación, aislamiento, distancia |
| `moon` | Luna | 0.166 g | 1.4 | 1.3 s | Radiación, polvo lunar, gravedad alterada |
| `mars` | Marte | 0.38 g | 0.6 | 1200 s (de 4 a 24 min; 20 en la demo) | Distancia de la Tierra, radiación, aislamiento |

¹ Son los valores de referencia del simulador (`simulador/config/environments.yaml`). **Se verifican y se citan antes de presentar.** Los dos archivos deben decir lo mismo.

**`MissionSeeder`:** la misión **Ares-1** (`mars`, 30 días, empezó hace 23 días, así que hoy es el día 24) y 4 astronautas ficticios. Sus usuarios (`astro1@astrohealth.test` … `astro4@astrohealth.test`, contraseña `password`, solo en local) los crea `UserSeeder` de M0, enlazados por `users.astronaut_id`.

## 4. API

| Método | Ruta | Qué hace |
|---|---|---|
| GET | `/api/v1/environments` | Los 4 entornos de `config/environments.php` |
| GET | `/api/v1/missions/current` | La misión actual, su entorno, su día de misión y su tripulación |
| PATCH | `/api/v1/missions/{id}` | Cambia el `environment` |
| GET | `/api/v1/astronauts` | La tripulación (para el selector de astronauta y el ranking) |

**`GET /api/v1/missions/current`**

```json
{
  "data": {
    "id": 1,
    "name": "Ares-1",
    "environment": { "code": "mars", "name": "Marte", "gravity_g": 0.38, "radiation_base_msv": 0.6, "ground_delay_seconds": 1200, "risks": ["Distancia de la Tierra", "Radiación", "Aislamiento"] },
    "start_date": "2026-10-22",
    "day_count": 30,
    "current_day": 24,
    "space_weather": { "risk_level": "low", "source": "nasa_donki", "stale": false },
    "crew": [ { "id": 1, "name": "…", "role": "Comandante", "avatar": "/img/crew/1.svg" } ]
  },
  "message": "ok"
}
```

`space_weather` viene del clima espacial **real** de la NASA (DONKI), pedido a M2. Si Python no responde, el campo sale en `null` y la pantalla no lo muestra.

## 5. Backend por capas

| Capa | Archivo | Qué hace |
|---|---|---|
| Domain | `Domain/Enums/EnvironmentCode.php` · `ErrorCode.php` | |
| Domain | `Domain/Reglas/MissionDay.php` | `current_day` a partir de `start_date` y de hoy, entre 1 y `day_count` |
| Domain | `Domain/Eventos/MissionEnvironmentChanged.php` | `missionId`, `from` y `to` |
| Application | `Application/ChangeEnvironment.php` | Valida y guarda el entorno nuevo, y publica el evento |
| Application | `Application/GetCurrentMission.php` · `ListEnvironments.php` · `ListCrew.php` | |
| Infrastructure | `Infrastructure/Models/Mission.php` · `Astronaut.php` | |
| Infrastructure | `Repositorios/Mission/FindMissionRepository.php` · `UpdateMissionRepository.php` · `UpsertMissionRepository.php` · `Repositorios/Astronaut/FindAstronautRepository.php` · `UpsertAstronautRepository.php` | Los `Find…` implementan `MissionQuery` y `CrewQuery`. Los `Upsert…` los usa `MissionSeeder` |
| Infrastructure | `Infrastructure/EnvironmentConfig.php` | Lee `config/environments.php` |
| Http | `Http/Controllers/Api/ShowCurrentMissionController.php` · `UpdateMissionController.php` · `ListEnvironmentsController.php` · `ListAstronautsController.php` | Uno por acción |
| Http | `Http/Requests/UpdateMissionRequest.php` | `environment` debe ser `iss`, `transit`, `moon` o `mars` |
| Database | `Migrations/` · `Seeders/MissionSeeder.php` | **Fase 1** |

## 6. Frontend

| Archivo | Qué hace |
|---|---|
| `Components/EnvironmentSelector.vue` | Las 4 `EnvironmentCard`. La activa va resaltada. Al hacer clic, pide confirmación y llama al *store* |
| `Components/EnvironmentCard.vue` | Ilustración SVG, nombre, gravedad y retraso |
| `Components/GravityIndicator.vue` | Barra o dial con la gravedad (`0.38 g`), animado al cambiar |
| `Components/MissionDayCounter.vue` | "Día 24 de 30", con una barra de progreso |
| `Components/RiskList.vue` | Los riesgos del entorno, con su icono |
| `Components/MissionBackdrop.vue` | El fondo de la página (estrellas + planeta), con transición de color al cambiar de entorno |
| Global `composables/useCurrentAstronaut.ts` | El astronauta seleccionado (selector en `AppLayout`). Lo usan todos los módulos para saber a quién consultar |
| `Stores/useMissionStore.ts` | `mission`, `environments`, `crew`, `load()`, `changeEnvironment(code)` |
| Página `pages/Mission.vue` | Contador y gravedad → selector → riesgos |

**Al cambiar de entorno**, M1 vuelve a evaluar las últimas mediciones (por el evento) y la página vuelve a cargar los *stores* del puntaje (M2) y del cuerpo (M3): los colores ya llegan recalculados.

## 7. Contratos y eventos

| Tipo | Nombre | Detalle |
|---|---|---|
| Ofrece | `Contratos/MissionQuery` | `current()`, `environmentOf(int $astronautId)`, `groundDelaySeconds(int $astronautId)`, `radiationBase(string $environment)` |
| Ofrece | `Contratos/CrewQuery` | `all()`, `exists(int $id)`, `belongsToMission(int $astronautId, int $missionId)`, `missionOf(int $astronautId)` |
| Publica | `MissionEnvironmentChanged` | M1 lo escucha y vuelve a evaluar las últimas mediciones con los umbrales del entorno nuevo (el "recálculo de colores" del plan) |
| Usa | M2 `SpaceWeather::now()` | Riesgo del clima espacial, opcional |

## 8. Códigos de error

| Código | HTTP | Cuándo |
|---|:---:|---|
| `VALIDATION_FAILED` | 422 | Un entorno que no existe |
| `ENVIRONMENT_MISSION_NOT_FOUND` | 404 | No hay ninguna misión cargada (falta el *seeder*) |

## 9. Criterios de aceptación

| # | Dado | Cuando | Entonces |
|:---:|---|---|---|
| 1 | La misión en Marte | Se cambia a la ISS | `environment = iss`, el retraso es de 1 s y se publica `MissionEnvironmentChanged` |
| 2 | La ISS | Se envía una alerta a Tierra (M5) | `delay_seconds = 1` |
| 3 | Una radiación de 0.9 mSv/día | Se evalúa en Marte (base 0.6) y en tránsito (base 1.8) | Amarillo en Marte y verde en tránsito |
| 4 | `start_date` hace 23 días | Se pide la misión actual | `current_day = 24` |
| 5 | `environment = "jupiter"` | `PATCH /missions/1` | 422 `VALIDATION_FAILED` |

## 10. Listo cuando

- [ ] Al cambiar de entorno cambian los riesgos mostrados y el retraso de la comunicación.
- [ ] Las tablas `missions` y `astronauts` y su *seeder* estuvieron listos en la fase 1.
