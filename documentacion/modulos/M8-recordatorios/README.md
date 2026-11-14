# ⏰ M8 · Recordatorios de rutina

> [← Módulos](../README.md) · [Plan §3 M8](../../PLAN_ASTROHEALTH.md#-m8--recordatorios-de-rutina) · Anterior: [M7 Gamificación](../M7-gamificacion/README.md)

| Backend | Frontend | Fase | Responsable | Depende de |
|---|---|:---:|:---:|---|
| `app/Modules/Recordatorios/` | `resources/js/modules/Recordatorios/` | 5 | P1 (API) · P4 (UI) | [M6](../M6-entorno/README.md) `CrewQuery` |

## 1. Para qué sirve

Lleva **la rutina del día**: medir la presión, hacer ejercicio, registrar el sueño, revisar el ánimo y leer el dosímetro. Avisa a la hora indicada y, al marcar un recordatorio como hecho, suma puntos (M7).

## 2. En la demo

1. En el **Inicio**, la lista del día con sus casillas: hechos ✓ y pendientes.
2. A la hora indicada aparece un *toast* animado: *"Hora de medir tu presión 🩺"*, y una notificación del navegador si el usuario la permitió.
3. Se marca **hecho** → `+5` en la barra de M7 (y confeti, si eso completa una insignia).

## 3. Datos

Es dueño de **`reminders`** ([columnas](../../arquitectura/02-base-de-datos.md#m8--reminders)).

**Estado de un recordatorio** (se calcula, no se guarda):

| Estado | Regla (`Domain/Reglas/ReminderState.php`) |
|---|---|
| `done` | `last_done_at` es de hoy |
| `due` | No está hecho y ya pasó su hora (`time`) |
| `upcoming` | No está hecho y su hora todavía no llega |

**`ReminderSeeder`** (los 5 por defecto del plan, para cada astronauta):

| `type` | Título | Hora |
|---|---|---|
| `measure_bp` | Hora de medir tu presión 🩺 | 08:00 |
| `exercise` | Ejercicio de resistencia: 2 h 🏋️ | 10:00 |
| `dosimeter` | Lee tu dosímetro de radiación ☢️ | 12:00 |
| `mood_check` | ¿Cómo te sientes hoy? 🙂 | 18:00 |
| `log_sleep` | Registra cómo dormiste 😴 | 21:00 |

## 4. API

| Método | Ruta | Qué hace |
|---|---|---|
| GET | `/api/v1/reminders?astronaut_id=2` | Los recordatorios del día, con su `state`, ordenados por hora |
| POST | `/api/v1/reminders` | Crea uno (`type`, `title`, `time`) |
| PATCH | `/api/v1/reminders/{id}` | Cambia el título o la hora |
| DELETE | `/api/v1/reminders/{id}` | Lo elimina (204) |
| POST | `/api/v1/reminders/{id}/done` | Lo marca como hecho hoy y publica `ReminderCompleted` |

```json
{
  "data": [
    { "id": 11, "type": "measure_bp", "title": "Hora de medir tu presión 🩺", "time": "08:00", "state": "done", "last_done_at": "2026-11-14T08:03:00Z" },
    { "id": 12, "type": "exercise", "title": "Ejercicio de resistencia: 2 h 🏋️", "time": "10:00", "state": "due", "last_done_at": null }
  ],
  "message": "ok"
}
```

## 5. Backend por capas

| Capa | Archivo | Qué hace |
|---|---|---|
| Domain | `Domain/Enums/ReminderType.php` · `ReminderState.php` · `ErrorCode.php` | |
| Domain | `Domain/Reglas/ReminderStateRule.php` | `done`, `due` o `upcoming`, a partir de la hora y de `last_done_at` |
| Domain | `Domain/Eventos/ReminderCompleted.php` | `reminderId`, `astronautId`, `type` y `doneAt` |
| Application | `Application/CompleteReminder.php` | Si ya está hecho hoy, responde sin volver a sumar. Si no, guarda `last_done_at` y publica el evento |
| Application | `Application/ListTodayReminders.php` · `CreateReminder.php` · `UpdateReminder.php` · `DeleteReminder.php` | El CRUD |
| Infrastructure | `Infrastructure/Models/Reminder.php` · `Repositorios/Reminder/FindReminderRepository.php` · `InsertReminderRepository.php` · `UpdateReminderRepository.php` · `DeleteReminderRepository.php` | Uno por operación |
| Http | `Http/Controllers/Api/ListRemindersController.php` · `StoreReminderController.php` · `UpdateReminderController.php` · `DeleteReminderController.php` · `CompleteReminderController.php` | Uno por acción (CRUD + `done`) |
| Http | `Http/Requests/StoreReminderRequest.php` · `UpdateReminderRequest.php` | `time` con formato `HH:MM` y `type` válido |
| Http | `Http/Resources/ReminderResource.php` | Incluye el `state` calculado |
| Policies | `Policies/ReminderPolicy.php` | Solo se cambian los recordatorios del astronauta propio |
| Console | `Console/MarkDueRemindersCommand.php` | `reminders:mark-due`, programado cada minuto con `schedule:run`. Registra en el *log* cuáles quedaron pendientes (el plan pide el comando programado). El aviso visual lo dispara Vue |
| Database | `Migrations/` · `Seeders/ReminderSeeder.php` | |

## 6. Frontend

| Archivo | Qué hace |
|---|---|
| `Components/ReminderList.vue` | La lista del día: hora, título y una casilla. Los `due` se resaltan |
| `Components/ReminderItem.vue` | Un recordatorio. La casilla llama a `done` y anima el ✓ |
| `Components/ReminderEditor.vue` | Un diálogo para crear o cambiar uno (título y hora) |
| Global `components/ToastNotification.vue` | El *toast* animado, con `vue-sonner` |
| Global `composables/useNotifications.ts` | Pide el permiso con un botón ("Activar avisos") y muestra `new Notification(…)` si se concedió |
| `Composables/useReminderClock.ts` | Cada 30 s revisa si algún `upcoming` pasó a `due`. Si es así, lanza el *toast* y la notificación **una sola vez** por recordatorio y día |
| `Stores/useRemindersStore.ts` | `today`, `load()`, `complete(id)`. Después de `complete`, llama a `useGamificationStore().refresh()` desde la página |
| Página | La lista va en `pages/Home.vue`. La edición, en un diálogo desde ahí |

> El orden "completar → refrescar la gamificación" lo coordina la **página**, no el *store* de M8: así un módulo no importa a otro ([regla del frontend](../../arquitectura/03-frontend-vue.md#2-carpetas)).

## 7. Contratos y eventos

| Tipo | Nombre | Detalle |
|---|---|---|
| Publica | `ReminderCompleted` | M7 suma +5 y revisa la insignia `on_time` |
| Usa | M6 `CrewQuery::exists()` | Valida el astronauta al crear un recordatorio |

## 8. Códigos de error

| Código | HTTP | Cuándo |
|---|:---:|---|
| `VALIDATION_FAILED` | 422 | Una hora sin el formato `HH:MM` o un tipo desconocido |
| `FORBIDDEN` | 403 | Cambiar el recordatorio de otro astronauta |
| `REMINDERS_NOT_FOUND` | 404 | El recordatorio no existe |

## 9. Criterios de aceptación

| # | Dado | Cuando | Entonces |
|:---:|---|---|---|
| 1 | Son las 09:00 | Se piden los recordatorios | `measure_bp` (08:00) sale `due`, y `exercise` (10:00) sale `upcoming` |
| 2 | Un recordatorio `due` | `POST /done` | Pasa a `done`, se publica `ReminderCompleted` y M7 suma +5 |
| 3 | Un recordatorio ya hecho hoy | `POST /done` otra vez | Responde 200 sin publicar el evento: no suma dos veces |
| 4 | El día siguiente | Se piden los recordatorios | Todos vuelven a `upcoming` o `due` |
| 5 | La app abierta a las 07:59 | Llegan las 08:00 | Aparece el *toast* una vez (y la notificación, si hay permiso) |
| 6 | La hora `25:00` | `POST /reminders` | 422 `VALIDATION_FAILED` |

## 10. Listo cuando

- [ ] Aparece el recordatorio.
- [ ] Se marca como hecho.
- [ ] Suma puntos.
