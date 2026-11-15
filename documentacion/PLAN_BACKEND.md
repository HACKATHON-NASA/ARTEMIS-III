# Plan de trabajo · Backend de AstroHealth

> [← Documentación](README.md) · [Plan general](PLAN_ASTROHEALTH.md) · [Norma de arquitectura](arquitectura/ARCHITECTURE.md) · [Base de datos](arquitectura/02-base-de-datos.md)

**Objetivo:** que cada pantalla deje de usar datos de prueba (`*Mock*.ts`, `resources/js/mocks/`) y lea y escriba en Laravel + MySQL, cumpliendo la [norma](arquitectura/ARCHITECTURE.md) al pie de la letra.

**Rama:** `development`. **Un commit por paso pequeño** (una tabla, un caso de uso, un endpoint con su prueba).

---

## 0. Dónde estamos (15 nov 2026)

| Módulo | Frontend | Backend | Tablas | Datos de prueba que hay que retirar |
|---|:---:|---|---|---|
| M0 Identidad | ✅ | ✅ login, refresh, logout, `me`, token de dispositivo · ❌ gestión de usuarios y dispositivos | ✅ `users`, `devices`, `refresh_tokens` | `Identidad/Utils/adminMockData.ts` |
| M6 Entorno | ✅ | 🟡 modelos, repositorios y contratos · ❌ endpoints · ❌ pruebas | 🟡 `missions` (le faltan `objective` y `status`), `astronauts` | `Entorno/Utils/missionsMockData.ts`, `environments.ts`, `environmentFacts.ts` |
| M1 Registro | ✅ | ❌ | ❌ | `Registro/Utils/indicatorsMockData.ts`, `recordMock.ts` |
| M2 Evaluación | ✅ | ❌ | ❌ | `mocks/thresholds.ts`, `Evaluacion/Utils/psychMockData.ts`, `psychScoring.ts` |
| M3 Cuerpo | ✅ | ❌ | ❌ | `Cuerpo/Utils/bodyMockData.ts`, `notesMock.ts`, `mocks/crewProfiles.ts` |
| M4 Dashboard | ✅ | ❌ | (no tiene tablas) | `mocks/crewHistory.ts`, `mocks/crewHealth.ts` |
| M5 Alertas | ✅ | ❌ | ❌ | `Alertas/Utils/alertsMock*.ts`, `recommendationsMockData.ts`, `adviceMock.ts` |
| M7 Gamificación | ✅ | ❌ | ❌ | `Gamificacion/Utils/gamificationMock.ts`, `rules.ts` |
| M8 Recordatorios | ✅ | ❌ | ❌ | `Recordatorios/Utils/remindersMock.ts` |
| Simulador | ✅ API propia `:8001` | ❌ no envía lotes a Laravel | — | — |

**Lo que sí está bien:** M0 cumple la norma completa (capas, un repositorio por operación, entidades `readonly`, excepciones de negocio, 15 archivos de pruebas). `app/Shared` ya tiene `ApiResponse`, `BusinessError`, `Transaction` y el `ModuleServiceProvider`. No hay `DB::` fuera de `Infrastructure`, ni comprobaciones de rol fuera de M0. En el frontend ningún archivo pasa de 300 líneas y ningún módulo importa otro.

**Lo que hoy rompe la norma (y este plan arregla):** el frontend calcula semáforo, puntaje, puntos de tests psicológicos, insignias y puntos de gamificación (**P4**), y tiene umbrales escritos en el código (**P7**). Mientras sea una demo con datos de prueba se acepta; cada fase lo mueve al backend.

---

## 1. Receta: cómo se hace cada módulo

Cada fase sigue estos pasos, en este orden. Un módulo está **terminado** cuando cumple todos.

1. **Migraciones** en `app/Modules/<Modulo>/Database/Migrations/` (nunca editar una ya subida: **P11**).
2. **Seeder** con los datos de la demo (los mismos de los *mocks*, para que la pantalla se vea igual) y registrarlo en `DatabaseSeeder` en orden.
3. **Domain:** enums, entidades `readonly`, reglas puras y excepciones de negocio. **Pruebas unitarias** de cada regla.
4. **Infrastructure:** modelo Eloquent con `$fillable`, `Mapper`, un repositorio por operación (`Find`/`Insert`/`Update`/`Delete`/`Upsert`).
5. **Application:** un caso de uso por acción (`execute()`), DTOs de entrada y salida, `Transaction::run()` si escribe varias filas, eventos después del *commit*.
6. **Http:** un controlador `__invoke` por acción, `FormRequest`, `Resource`, ruta en `routes.php` con `auth:api` + `can:<permiso>`.
7. **Provider** registrado en `bootstrap/providers.php`, con los *bindings* de sus contratos.
8. **Pruebas *feature*:** una por criterio *Dado / Cuando / Entonces* de la ficha, más 401 (sin token), 403 (sin permiso) y 422 (datos inválidos).
9. **Frontend:** en el *store*, cambiar la función de prueba por `api()` de `@/lib/api`; **borrar** el archivo `*Mock*` y lo que el backend ya calcula. Los componentes no se tocan.
10. **Documentación:** `astrohealth/docs/backend/Mx-…/README.md` y `docs/database/Mx-…/README.md` al día.
11. **Verificar:** `php artisan test`, `vp check`, `vue-tsc --noEmit`, `npm run build` y la pantalla probada en el navegador con los 3 roles.

---

## 2. Orden de las fases

```mermaid
flowchart LR
    F0["F0 · M0 revisión<br/>+ usuarios y dispositivos"] --> F1["F1 · M6 Entorno"]
    F1 --> F2["F2 · M2 núcleo<br/>umbrales + Evaluator"]
    F2 --> F3["F3 · M1 Registro<br/>+ ingesta del simulador"]
    F3 --> F4["F4 · M3 Cuerpo"]
    F3 --> F5["F5 · M5 Alertas"]
    F4 --> F6["F6 · M4 Dashboard"]
    F5 --> F6
    F3 --> F7["F7 · M7 Gamificación"]
    F7 --> F8["F8 · M8 Recordatorios"]
    F2 --> F9["F9 · M2 tests psicológicos"]
    F5 --> F9
```

**Por qué M6 y M2 van antes que M1:** toda medición pertenece a un astronauta (M6) y se guarda **con su color**, que calcula M2 (contrato `Evaluator`). Sin ellos, M1 no puede cumplir su "listo cuando".

---

## F0 · M0 Identidad: revisión y lo que falta · ✅ hecho (16 nov 2026)

**Revisar (ya existe):**
- [x] `config/permissions.php` tiene todos los permisos que usan las pantallas (`users.manage`, `devices.manage`, `missions.manage`, `mission.update`, `crew.read`, `alerts.read`, `alerts.send`, `alerts.acknowledge`, `advice.send`, `clinical_notes.manage`, `guide.manage`, `gamification.read`, `measurements.ingest`, `measurements.write.own/crew`, `reminders.manage.own`, más el Gate `health.read`) y `AuthorizationMatrixTest` prueba cada uno con cada rol.
- [ ] ⚠️ El Gate `health.read` decide "lo suyo" para el astronauta, pero **los médicos y el admin ven a los tripulantes de todas las misiones** (`health.read.any`). Acotarlo a la misión del médico en F1, ahora que `users.mission_id` existe y va en su token.
- [x] `TokenFactory` incluye `astronaut_id` y `mission_id` en el token; desde F0 también el del médico de vuelo.
- [x] Los seeders crean los mismos usuarios que usan los *mocks* (Elena, Kenji, Amara, Lucas, la Dra. Sofía Ramírez, el admin); la Dra. Sofía sigue Ares-1.

**Hecho en F0:** las 7 rutas de abajo, la columna `users.mission_id`, el comando `identidad:user-password <correo>` (el admin crea a la persona sin contraseña conocida), errores de negocio con su campo, `CreateDevice` con errores de negocio en vez de `InvalidArgumentException`, 20 pruebas nuevas y la pantalla de Usuarios sin `mockUsers`/`mockDevices`.
**Queda con datos de prueba en esa pantalla:** la misión del encabezado (F1) y el contador de alertas (F5). **Mientras no exista F1**, crear un usuario de la nave falla con `AUTH_UNKNOWN_ASTRONAUT`, porque el tripulante nuevo se crea en el store de prueba de M6 y no existe en la base de datos.

**Construir:**

| Endpoint | Permiso | Caso de uso |
|---|---|---|
| `GET /api/v1/users` | `users.manage` | `ListUsers` |
| `POST /api/v1/users` | `users.manage` | `CreateUser` (rol válido con `RoleAssignment`; si es astronauta, enlaza `astronaut_id` por el contrato `CrewQuery`) |
| `PATCH /api/v1/users/{id}` | `users.manage` | `UpdateUser` |
| `PATCH /api/v1/users/{id}/active` | `users.manage` | `SetUserActive` (desactivar revoca sus *refresh tokens*) |
| `GET /api/v1/devices` · `POST` · `PATCH /{id}/active` | `devices.manage` | `ListDevices`, `CreateDevice` (ya existe), `SetDeviceActive` |

**Listo cuando:** la pantalla de Usuarios del admin funciona sin `adminMockData.ts`, y un usuario desactivado ya no puede refrescar su sesión.

---

## F1 · M6 Entorno

**Tablas:** migración nueva que agrega a `missions` → `objective` `string(255)` y `status` `enum(planned, active, finished)`. Los datos de cada entorno (gravedad, radiación diaria, retraso, riesgos) van en `config/environments.php` (**D14**), no en tablas.

| Endpoint | Permiso | Caso de uso |
|---|---|---|
| `GET /api/v1/environments` | autenticado | `ListEnvironments` (lee `config/environments.php`) |
| `GET /api/v1/missions` | `crew.read` | `ListMissions` (con `status` y número de tripulantes) |
| `GET /api/v1/missions/current` | autenticado | `GetCurrentMission` (la del token; calcula `day` con `MissionDayRule`) |
| `POST /api/v1/missions` · `PATCH /api/v1/missions/{id}` | `missions.manage` | `CreateMission`, `UpdateMission` |
| `PATCH /api/v1/missions/{id}/environment` | `mission.update` | `ChangeEnvironment` → evento `EnvironmentChanged` |
| `GET /api/v1/astronauts?mission=` | `crew.read` | `ListCrew` |
| `POST /api/v1/astronauts` · `PATCH /{id}` | `missions.manage` | `AddCrewMember`, `UpdateCrewMember` |

**Domain:** `MissionDayRule` (día actual, sin pasarse de `day_count`), `MissionStatusRule`, enum `Environment`.
**Contratos que ofrece:** `MissionQuery` (`exists`, `environmentOf`, `delayOf`, `current`), `CrewQuery` (`exists`, `missionOf`, `namesOf`).
**Pruebas:** hoy M6 no tiene ninguna; empezar por las de `FindMissionRepository` y `FindAstronautRepository`.
**Listo cuando:** Mi misión, Misiones (admin) y el selector de misión del médico funcionan sin `missionsMockData.ts`, y cambiar el entorno se guarda en la base de datos.

---

## F2 · M2 Evaluación (núcleo): umbrales y semáforo

**Tablas:** `thresholds` (por `indicator_code` + `environment`), `assessments` (con `body_status` en JSON).

| Endpoint | Permiso | Caso de uso |
|---|---|---|
| `GET /api/v1/thresholds?environment=` | autenticado | `ListThresholds` |
| `PUT /api/v1/thresholds/{indicator}?environment=` | `guide.manage` | `UpdateThreshold` (valida verde ⊂ amarillo) |
| `GET /api/v1/astronauts/{id}/assessment` | `health.read` | `GetLatestAssessment` |

**Domain:** `StatusRule::classify($value, $threshold)`, `ScoreRule::weighted($statuses)` (verde 100, amarillo 60, rojo 20; corazón, SpO₂ y radiación ×1,5), `TrendRule` (3 mediciones seguidas empeorando → amarillo). Todo con pruebas unitarias, incluidos los bordes (exactamente 140, exactamente 6 h de sueño).
**Contratos que ofrece:** `Evaluator` (`evaluate(code, value, environment): Status`, `assess(...)`) y `TrendAnalyzer`.
**Infrastructure:** `Clients/PythonEvaluator` (POST `/evaluate`, `/assess`, `/trends` del simulador en `:8001`) **con respaldo local** usando `thresholds` si Python no responde (**D7**). Probado con `Http::fake()`, incluido el caso en que Python se cae.
**Seeder:** `ThresholdSeeder` con los umbrales del plan §2 para los 4 entornos (los mismos de `mocks/thresholds.ts`).
**Arregla:** el sueño de Kenji (5,8 h) que hoy sale Crítico en la gráfica y Precaución en la ficha: habrá una sola regla.
**Listo cuando:** la Guía de salud lee y edita umbrales en la base de datos, y `Evaluator` devuelve el mismo color con Python encendido o apagado.

---

## F3 · M1 Registro + ingesta del simulador

**Tablas:** `indicators` (10, con `exercise_min`), `measurements` (con `source`, `reading_id` único, índice `(astronaut_id, indicator_id, measured_at)`), `ingest_batches`.

| Endpoint | Permiso | Caso de uso |
|---|---|---|
| `GET /api/v1/indicators` | autenticado | `ListIndicators` |
| `POST /api/v1/measurements` | `measurements.write.own` (lo suyo) · `measurements.write.crew` (médico de la tripulación) | `RecordMeasurements` |
| `POST /api/v1/ingest/readings` | `measurements.ingest` (dispositivo) | `IngestReadings` (idempotente por `batch_id`; repetido → devuelve el resumen original) |
| `GET /api/v1/astronauts/{id}/measurements?indicator=&range=` | `health.read` | `ListMeasurements` |
| Comando `registro:import-file <json>` | consola | Plan B sin red (**D8**) |

**Flujo:** `RecordMeasurements` → `Evaluator` (M2) pone el `status` → `InsertMeasurementRepository::insertMany()` dentro de `Transaction::run()` → evento `MeasurementsRecorded` **después del commit** (**D11**).
**Contratos que ofrece:** `IndicatorCatalog`, `MeasurementQuery` (último valor por indicador, serie por rango, mediciones del día).
**Validación:** `StoreMeasurementsRequest` con los rangos físicamente posibles de `indicators.min_possible/max_possible`.
**Simulador (Python, tu parte):** cliente que pide el JWT de dispositivo (`/auth/device-token`) y envía los 30 días por lotes a `/ingest/readings`, con `reading_id` y `batch_id` estables para poder reenviar sin duplicar.
**Listo cuando:** registrar datos guarda cada valor con su color; el simulador carga los 30 días de Ares-1 y reenviar el mismo lote no duplica nada.

---

## F4 · M3 Cuerpo + notas clínicas

**Tablas:** `body_parts` (9 zonas) y, **nueva frente al plan**, `clinical_notes`:

| Columna | Tipo |
|---|---|
| `astronaut_id` | `foreignId → astronauts` |
| `author_id` | `foreignId → users` |
| `kind` | `enum(evolution, impression, plan, follow_up)` |
| `text` | `text` (máx. 1000) |
| `follow_up` | `boolean` |
| `resolved_at` · `resolved_by` | *nullable* |
| `created_at` | sin `updated_at`: las notas no se editan ni se borran |

| Endpoint | Permiso | Caso de uso |
|---|---|---|
| `GET /api/v1/body-parts` | autenticado | `ListBodyParts` |
| `GET /api/v1/astronauts/{id}/body-status` | `health.read` | `GetBodyStatus` (último valor por zona, vía `MeasurementQuery` + `Evaluator`) |
| `GET` · `POST /api/v1/astronauts/{id}/clinical-notes` | `clinical_notes.manage` | `ListClinicalNotes`, `AddClinicalNote` |
| `PATCH /api/v1/clinical-notes/{id}/resolve` | `clinical_notes.manage` | `ResolveClinicalNote` (una nota ya resuelta → error de negocio) |

**Pruebas clave:** un astronauta **nunca** ve las notas clínicas (403), ni las propias.
**Listo cuando:** Inicio y la ficha del tripulante pintan el cuerpo con datos reales, y las notas se guardan y se resuelven.

---

## F5 · M5 Alertas, recomendaciones e indicaciones

**Tablas:** `recommendations` (las 20 de `recommendationsMockData.ts`), `alerts` **ampliada** frente al plan, y `advice` (**nueva**).

`alerts` agrega: `kind` `enum(vital, sos, help, report)` · `level` con `info` · `indicator_code` *nullable* · `category` *nullable* · `note` `text` *nullable* · `source` `enum(device, manual, astronaut)` · `urgent` `boolean` · `reply` `text` *nullable* · `replied_by` *nullable*.

`advice`: `astronaut_id`, `author_id`, `category` `enum(sleep, exercise, nutrition, medication, rest, mental, other)`, `title`, `body`, `important`, `valid_days` *nullable*, `sent_at`, `arrives_at`, `read_at`.

| Endpoint | Permiso | Caso de uso |
|---|---|---|
| `GET /api/v1/alerts?since=` | `alerts.read` | `ListAlerts` (el astronauta, las suyas; el médico, las de su misión) |
| `POST /api/v1/alerts` | `alerts.send` | `SendMessageToGround` (SOS, ayuda, novedad) |
| `PATCH /api/v1/alerts/{id}/acknowledge` | `alerts.acknowledge` | `AcknowledgeAlert` (con `reply` opcional) |
| `GET /api/v1/recommendations?indicator=&status=` | autenticado | `ListRecommendations` |
| `GET` · `POST /api/v1/advice` · `PATCH /api/v1/advice/{id}/read` | `advice.send` / el destinatario | `ListAdvice`, `SendAdvice`, `MarkAdviceRead` |

**Domain:** `GroundDelayRule` (`ground_eta_at = sent_at + retraso del entorno`, leído por `MissionQuery`), `AlertLevelRule`.
**Listeners:** `OnMeasurementsRecorded` (valor rojo → alerta automática; una por medición) y `OnPsychCriticalAnswered` (de F9).
**Tiempo real:** primero *polling* con `?since=` (lo que ya hace `useLiveAlerts`); Reverb/WebSockets solo si sobra tiempo.
**Listo cuando:** un valor rojo crea la alerta sola, el SOS llega al médico con el retraso de Marte, y el médico responde y envía indicaciones que el astronauta marca como leídas.

---

## F6 · M4 Dashboard (sin tablas)

Solo lee por contratos (`MeasurementQuery`, `Evaluator`, `TrendAnalyzer`, `CrewQuery`, `AlertQuery`).

| Endpoint | Permiso | Caso de uso |
|---|---|---|
| `GET /api/v1/astronauts/{id}/trends` | `health.read` | `GetTrends` (Python `/trends` con respaldo `TrendRule`) |
| `GET /api/v1/missions/{id}/crew-measurements?indicator=&range=` | `crew.read` | `GetCrewSeries` |
| `GET /api/v1/missions/{id}/health-summary` | `crew.read` | `GetHealthSummary` (sin atender, urgentes, en precaución, mediciones de hoy, quién necesita atención) |
| `GET /api/v1/missions/{id}/changes?hours=72` | `crew.read` | `GetRecentChanges` |

**Listo cuando:** Panel de salud y Mi evolución funcionan sin `mocks/crewHistory.ts` ni `crewHealth.ts`.

---

## F7 · M7 Gamificación

**Tablas:** `achievements` (las 9 insignias), `astronaut_achievements`, `points_log` (llave única `(astronaut_id, reason, reference)` para no sumar dos veces).

**Domain:** `PointsRule` (valores por `config/gamification.php`, no en el código: **P7**), `StreakRule::days($dates)`, `LevelRule`, una regla por insignia (`iron_body`, `swiss_clock`, `mission_30`…), todas con pruebas unitarias.
**Listeners:** `OnMeasurementsRecorded` (día completo, sueño ≥ 7 h, ejercicio), `OnReminderCompleted`, `OnPsychTestTaken`.

| Endpoint | Permiso | Caso de uso |
|---|---|---|
| `GET /api/v1/astronauts/{id}/gamification` | `gamification.read` | `GetGamificationProfile` |
| `GET /api/v1/leaderboard?mission=` | `gamification.read` | `GetLeaderboard` |

**Listo cuando:** los puntos y las insignias los da el backend; borrar `rules.ts` y `gamificationMock.ts` no cambia lo que se ve.

---

## F8 · M8 Recordatorios

**Tabla:** `reminders` con `type` (incluye `custom`), `title`, `time`, `active`, `last_done_at`.

| Endpoint | Permiso | Caso de uso |
|---|---|---|
| `GET` · `POST` · `PATCH` · `DELETE /api/v1/reminders` | el dueño | `ListReminders`, `CreateReminder`, `UpdateReminder`, `DeleteReminder` |
| `POST /api/v1/reminders/{id}/done` | el dueño | `CompleteReminder` → evento `ReminderCompleted` (hecho hoy dos veces → no suma otra vez) |
| Comando `recordatorios:pending` en el `schedule` | consola | Marca los pendientes de la hora |

**Seeder:** los 5 recordatorios por defecto para cada astronauta.
**Listo cuando:** completar un recordatorio suma +5 puntos una sola vez al día.

---

## F9 · M2 Tests psicológicos (PHQ-9, GAD-7, PSS-4, PVT)

**Tablas nuevas:** `psych_tests` (`code`, `name`, `kind`, `description`, `instructions`, `frequency_days`, `unit`, `scale` JSON, `questions` JSON, `bands` JSON, `source`) y `psych_results` (`test_id`, `astronaut_id`, `taken_at`, `score`, `status`, `band`, `detail`, `flagged`, `answers` JSON).

| Endpoint | Permiso | Caso de uso |
|---|---|---|
| `GET /api/v1/psych-tests` | autenticado | `ListPsychTests` |
| `POST` · `PUT /api/v1/psych-tests/{id}` | `guide.manage` | `CreatePsychTest`, `UpdatePsychTest` |
| `POST /api/v1/psych-results` | rol astronauta | `SubmitPsychTest` (el **backend** puntúa) |
| `GET /api/v1/psych-results?mission=` | `crew.read` / lo suyo | `ListPsychResults` |

**Domain:** `PsychScoringRule` (ítems invertidos, bandas), `PvtScoringRule` (mediana, lapsos > 500 ms, salidas en falso) con pruebas usando los mismos casos de `psychScoring.ts`.
**Seguridad clínica:** el ítem crítico del PHQ-9 publica `PsychCriticalAnswered` → M5 crea una alerta **urgente** a Tierra. Prueba obligatoria. Las respuestas individuales solo las ve el equipo médico.
**Listo cuando:** se borra `psychScoring.ts` del frontend y los resultados son los mismos.

---

## 3. Al final: limpieza y entrega

- [ ] `grep -ri mock resources/js` no devuelve nada fuera de las pruebas.
- [ ] `php artisan migrate:fresh --seed` + el simulador enviando lotes deja la demo de Ares-1 completa.
- [ ] El guion de 2 minutos ([plan §7](PLAN_ASTROHEALTH.md#7-guion-de-la-demo-2-minutos)) se hace de punta a punta con datos reales.
- [ ] Python apagado → la app sigue funcionando con el respaldo local.
- [ ] `PagesTest` y las pruebas de cada módulo en verde.

## 4. Estimación

| Fase | Tamaño | Notas |
|---|:---:|---|
| F0 M0 | S | Patrón ya hecho; es repetirlo |
| F1 M6 | S | Tablas ya existen |
| F2 M2 núcleo | M | Cliente Python + respaldo |
| F3 M1 + ingesta | L | La pieza central; incluye el simulador |
| F4 M3 | S | |
| F5 M5 | L | La que más pantallas toca |
| F6 M4 | M | Consultas de series |
| F7 M7 | M | Muchas reglas, todas puras |
| F8 M8 | S | |
| F9 Tests psicológicos | M | Puntuación + alerta crítica |

**Si el tiempo aprieta:** F0 → F3 y F5 son el corazón de la demo (registrar → color → alerta a Tierra). M7, M8 y F9 pueden quedarse con datos de prueba en la presentación, diciéndolo.
