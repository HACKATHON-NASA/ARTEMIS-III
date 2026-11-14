# 🏆 M7 · Gamificación

> [← Módulos](../README.md) · [Plan §3 M7](../../PLAN_ASTROHEALTH.md#-m7--gamificación) · Anterior: [M6](../M6-entorno/README.md) · Siguiente: [M8 Recordatorios](../M8-recordatorios/README.md)

| Backend | Frontend | Fase | Responsable | Depende de |
|---|---|:---:|:---:|---|
| `app/Modules/Gamificacion/` | `resources/js/modules/Gamificacion/` | 5 | P1 (API) · P4 (UI) | [M1](../M1-registro/README.md) evento `MeasurementsRecorded`, `MeasurementQuery` · [M8](../M8-recordatorios/README.md) evento `ReminderCompleted` · [M6](../M6-entorno/README.md) `CrewQuery` |

## 1. Para qué sirve

Hace que **cuidarse sea un hábito**. Registrar los datos, dormir bien, hacer ejercicio y cumplir los recordatorios suma puntos, sube de nivel, mantiene una racha y desbloquea insignias. Hay un ranking de la tripulación.

## 2. En la demo

> *"Completa el recordatorio, sube de nivel, confeti 🎉."* (10 s)

1. Se marca un recordatorio como hecho → `+5` flota sobre la `XpBar`, que se llena y sube de nivel.
2. Si con eso se gana una insignia → **confeti** y un *toast* *"¡Nueva insignia: 7 días en órbita!"*.
3. Pantalla **Logros**: nivel, 🔥 racha, insignias (las bloqueadas en gris) y el ranking.

## 3. Datos

Es dueño de **`achievements`**, **`astronaut_achievements`** y **`points_log`** ([columnas](../../arquitectura/02-base-de-datos.md#m7--achievements-astronaut_achievements-points_log)).

### Puntos (`config/gamification.php`)

| Acción | Puntos | `reason` | `reference` (evita sumar dos veces) |
|---|:---:|---|---|
| Registrar los 10 indicadores del día | **+10** | `daily_complete` | La fecha |
| Racha de 7 días seguidos con el día completo | **+50** | `streak_7` | La fecha del 7.º día |
| Dormir 7 h o más | **+5** | `sleep_7h` | La fecha |
| Hacer ejercicio (`exercise_min` ≥ 90) | **+15** | `exercise` | La fecha |
| Completar un recordatorio | **+5** | `reminder` | `reminder_id` + fecha |

La llave única `(astronaut_id, reason, reference)` garantiza que **nada se cuenta dos veces**, aunque un evento llegue repetido.

### Niveles y racha

| Concepto | Regla (`Domain/Reglas/`) |
|---|---|
| Nivel | `floor(puntos / 100) + 1`. La barra muestra `puntos % 100` de 100 |
| Racha | Días seguidos, hasta hoy, con `daily_complete` en `points_log`. Si hoy todavía no está completo, cuenta hasta ayer |

### Insignias (`AchievementSeeder`)

| `code` | Nombre | Icono | Se gana cuando | Puntos extra |
|---|---|:---:|---|:---:|
| `first_log` | Primer registro | 🚀 | Registra su primera medición | 5 |
| `orbit_7` | 7 días en órbita | 🛰️ | Racha de 7 días | 20 |
| `steel_heart` | Corazón de acero | ❤️ | 7 días seguidos con `hr`, `bp_sys` y `bp_dia` en verde | 20 |
| `space_sleeper` | Dormilón espacial | 😴 | 7 noches con 7 h o más de sueño | 20 |
| `on_time` | Puntual | ⏰ | 10 recordatorios completados | 15 |

## 4. API

| Método | Ruta | Qué hace |
|---|---|---|
| GET | `/api/v1/astronauts/{id}/gamification` | Puntos, nivel, racha, insignias y los últimos puntos ganados |
| GET | `/api/v1/leaderboard` | El ranking de la tripulación por puntos |

```json
{
  "data": {
    "astronaut_id": 2,
    "points": 245,
    "level": 3,
    "level_progress": 45,
    "streak_days": 7,
    "badges": [
      { "code": "orbit_7", "name": "7 días en órbita", "icon": "🛰️", "earned_at": "2026-11-14T08:02:00Z" },
      { "code": "steel_heart", "name": "Corazón de acero", "icon": "❤️", "earned_at": null }
    ],
    "recent": [ { "points": 5, "reason": "reminder", "created_at": "2026-11-14T08:02:00Z" } ]
  },
  "message": "ok"
}
```

## 5. Backend por capas

| Capa | Archivo | Qué hace |
|---|---|---|
| Domain | `Domain/Enums/PointReason.php` · `AchievementCode.php` | |
| Domain | `Domain/Reglas/LevelRule.php` · `StreakRule.php` · `AchievementRules.php` | Reglas puras: reciben fechas y estados y devuelven el resultado |
| Application | `Application/GamificationService.php` | El nombre viene del plan. `award(astronautId, reason, reference)`: suma los puntos si no existen y revisa las insignias. Todo en una transacción |
| Application | `Application/GetGamificationProfile.php` · `GetLeaderboard.php` | |
| Infrastructure | `Infrastructure/Listeners/OnMeasurementsRecorded.php` | Primer registro, día completo (`MeasurementQuery::daysWithAllIndicators`), sueño ≥ 7 h, ejercicio ≥ 90 min y racha |
| Infrastructure | `Infrastructure/Listeners/OnReminderCompleted.php` | +5 y la insignia `on_time` |
| Infrastructure | `Infrastructure/Models/` · `Repositorios/PointsRepository.php` · `AchievementRepository.php` | `insertOrIgnore` en `points_log` (por la llave única) |
| Http | `Http/Controllers/Api/GamificationController.php` · `LeaderboardController.php` | |
| Http | `Http/Resources/GamificationResource.php` · `LeaderboardResource.php` | |
| Database | `Migrations/` · `Seeders/AchievementSeeder.php` | |

> **Los *listeners* de M7 son síncronos** (sin `ShouldQueue`): cuando la respuesta de "registrar" o de "recordatorio hecho" llega a Vue, los puntos ya están sumados y el *store* los puede pedir.

## 6. Frontend

| Archivo | Qué hace |
|---|---|
| `Components/XpBar.vue` | Nivel y barra de `level_progress` animada (`transform: scaleX`). El `+N` flota y desaparece |
| `Components/StreakCounter.vue` | 🔥 y el número de días. Late una vez cuando sube |
| `Components/BadgeGrid.vue` · `BadgeItem.vue` | Las insignias ganadas, a color, con su fecha. Las bloqueadas, en gris y con la condición para ganarlas |
| `Components/Leaderboard.vue` | El ranking, con el avatar y los puntos. Resalta al astronauta actual |
| `Composables/useConfetti.ts` | Envuelve `canvas-confetti`. No hace nada con `prefers-reduced-motion` |
| `Stores/useGamificationStore.ts` | `profile`, `leaderboard`, `refresh()`. **Compara** las insignias de antes y de después del `refresh()`: si hay una nueva, lanza el confeti y el *toast* |
| Página `pages/Achievements.vue` | XpBar y racha → insignias → ranking |

La `XpBar` y el `StreakCounter` también se ven en el encabezado de `AppLayout`, para que los puntos se noten en toda la app.

## 7. Contratos y eventos

| Tipo | Nombre | Detalle |
|---|---|---|
| Escucha | M1 `MeasurementsRecorded` | Puntos por registrar, dormir y hacer ejercicio |
| Escucha | M8 `ReminderCompleted` | Puntos por cumplir un recordatorio |
| Usa | M1 `MeasurementQuery` | Días completos y series para las insignias |
| Usa | M6 `CrewQuery` | Los nombres y avatares del ranking |

## 8. Códigos de error

| Código | HTTP | Cuándo |
|---|:---:|---|
| `NOT_FOUND` | 404 | El astronauta no existe |

## 9. Criterios de aceptación

| # | Dado | Cuando | Entonces |
|:---:|---|---|---|
| 1 | Un astronauta con 9 de 10 indicadores registrados hoy | Registra el que falta | +10 `daily_complete` |
| 2 | El mismo día ya completo | Registra otra medición | No suma otra vez |
| 3 | 6 días seguidos completos | Completa el 7.º | +10, +50 y la insignia `orbit_7` (+20) |
| 4 | 95 puntos | Gana 5 | Pasa al nivel 2 con `level_progress = 0` |
| 5 | Un recordatorio | Se marca como hecho | +5 `reminder`, y en Vue aparece el `+5` en la barra |
| 6 | El mismo evento entregado dos veces | Lo procesa el *listener* | Una sola fila en `points_log` |
| 7 | Una insignia nueva | El *store* refresca | Confeti y *toast*. Con movimiento reducido, solo el *toast* |

## 10. Listo cuando

- [ ] Registrar datos suma puntos.
- [ ] Se desbloquean insignias visibles.
- [ ] El ranking muestra a los 4 astronautas.
