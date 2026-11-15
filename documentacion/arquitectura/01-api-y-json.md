# 01 · API y formato JSON

> [← Guía técnica](README.md) · [ARCHITECTURE.md](ARCHITECTURE.md) · Siguiente: [Base de datos](02-base-de-datos.md)

Este es el contrato que comparten Laravel, Vue y Python. **Hay un solo formato** para toda la API.

## 1. Reglas de la API

| Regla | Valor |
|---|---|
| Prefijo | `/api/v1` |
| Autenticación | `Authorization: Bearer <JWT>` en toda ruta protegida ([sección 8](#8-autenticación-jwt)) |
| Rutas | En inglés, en plural, sin verbos. Las únicas acciones son las del plan: `acknowledge`, `done` |
| Fechas | ISO 8601 en UTC con `Z`: `2026-11-14T08:00:00Z` |
| Números | Número JSON con punto decimal: `36.8` |
| Enumerados | Exactamente como en la base de datos: `green`, `yellow`, `red`, `mars`, `heart` |
| Origen del dato | Cada medición lleva `source` ([valores](#5-valores-de-source)) |

## 2. Respuesta correcta

```json
{
  "data": { "id": 812, "indicator": "hr", "value": 72, "unit": "bpm", "status": "green", "measured_at": "2026-11-14T08:00:00Z", "source": "manual" },
  "message": "ok"
}
```

Las listas que pueden crecer van paginadas, con `meta`:

```json
{
  "data": [ { "…": "…" } ],
  "message": "ok",
  "meta": { "page": 1, "per_page": 25, "total": 31, "last_page": 2 }
}
```

## 3. Respuesta de error

```json
{
  "data": null,
  "message": "Los datos no son válidos.",
  "error_code": "VALIDATION_FAILED",
  "errors": { "measurements.0.value": ["La frecuencia cardíaca debe estar entre 20 y 250."] }
}
```

| Clave | Cuándo va | Qué tiene |
|---|---|---|
| `data` | Siempre | El recurso, la lista, o `null` en los errores |
| `message` | Siempre | `"ok"`, o un texto en español para una persona |
| `error_code` | Solo en errores | El código de error ([sección 4](#4-códigos-de-error)) |
| `errors` | Solo en errores | Los errores por campo, o `{}` si no son de un campo |
| `meta` | Solo en listas paginadas | Los datos de paginación |

**Cómo se construye:** los controladores usan `App\Shared\Http\ApiResponse` y los errores los arma `App\Shared\Http\ApiExceptionRenderer`. Los errores salen solos: el manejador de `bootstrap/app.php` convierte las validaciones, las excepciones de negocio (`BusinessError`) y los errores inesperados al formato de arriba. **Nadie arma un JSON a mano.**

## 4. Códigos de error

**Generales** (`App\Shared\Http\GeneralCode`):

| Código | HTTP | Cuándo |
|---|:---:|---|
| `BAD_REQUEST` | 400 | La petición está mal formada |
| `UNAUTHENTICATED` | 401 | Sin token (los detalles, con los códigos `AUTH_*` de M0) |
| `FORBIDDEN` | 403 | Con token, pero su rol no tiene el permiso o el recurso no es suyo |
| `NOT_FOUND` | 404 | No existe |
| `METHOD_NOT_ALLOWED` | 405 | El método HTTP no existe en esa ruta |
| `CONFLICT` | 409 | La acción choca con el estado actual |
| `PAYLOAD_TOO_LARGE` | 413 | El cuerpo es demasiado grande |
| `VALIDATION_FAILED` | 422 | La entrada no pasa el `FormRequest` |
| `TOO_MANY_REQUESTS` | 429 | Demasiadas peticiones |
| `INTERNAL_ERROR` | 500 | Error no previsto. Responde "Ocurrió un error inesperado." |
| `SERVICE_UNAVAILABLE` | 503 | Mantenimiento |

**De cada módulo** (enum `Domain/Enums/ErrorCode` del módulo):

| Prefijo | Módulo | Prefijo | Módulo |
|---|---|---|---|
| `AUTH_` | M0 Identidad | | |
| `MEASUREMENTS_` | M1 Registro | `ALERTS_` | M5 Alertas |
| `EVALUATION_` | M2 Evaluación | `ENVIRONMENT_` | M6 Entorno |
| `BODY_` | M3 Cuerpo | `GAMIFICATION_` | M7 Gamificación |
| `DASHBOARD_` | M4 Dashboard | `REMINDERS_` | M8 Recordatorios |

Cada ficha de módulo trae su tabla de códigos. Un código publicado **no cambia**.

## 5. Valores de `source`

| Valor | Qué significa | Quién lo pone |
|---|---|---|
| `manual` | Lo registró el astronauta en el formulario | M1 |
| `device` | Lo envió un sensor real (o el simulador sin más detalle) | Ingesta |
| `simulated_reference` | Simulado con rangos fisiológicos de referencia | Simulador (ingesta) |
| `simulated_calibrated_osdr` | Simulado y calibrado con datos reales de Inspiration4 (NASA OSDR) | Simulador (ingesta) |
| `nasa_donki` | Clima espacial real (NASA DONKI) | Simulador (ingesta) |
| `simulated_event` | Evento inyectado para la demo | Simulador (ingesta) |

## 6. Contrato con Python

Hay **dos direcciones**:

| Dirección | Qué viaja | Detalle |
|---|---|---|
| 🤖 Simulador → Laravel | Las lecturas de los "sensores", en lotes, con JWT de dispositivo | [M1 · ingesta](../../astrohealth/docs/backend/M1-registro/README.md#1-la-ingesta-de-las-máquinas) |
| Laravel → 🐍 motor de Python | Pedidos de evaluación, puntaje y tendencias | Esta sección |

Laravel llama al simulador con `Http::baseUrl(config('services.simulador.url'))`, un *timeout* corto y sin reintentos en la petición del usuario. Las variables van en `astrohealth/.env`:

```env
SIMULADOR_URL=http://localhost:8001/api/v1
SIMULADOR_TIMEOUT=3
```

| Endpoint de Python | Lo usa | Para qué |
|---|---|---|
| `POST /evaluate` | M2 (`Evaluator`) | Color de una medición |
| `POST /assess` | M2 (`Evaluator`) | Puntaje 0–100 y estado por órgano |
| `POST /trends` | M2 (`TrendAnalyzer`) | Dirección `up/down/stable` y alerta de tendencia |
| `GET /space-weather/now` | M2 (`SpaceWeather`), lo pide M6 | Riesgo de radiación de hoy (DONKI real) |

Todas las llamadas a Python viven en `app/Modules/Evaluacion/Infrastructure/Clients/`. Ningún otro módulo llama a Python directamente: lo pide por un contrato de M2.

**Si Python no responde** (por *timeout* o por un error 5xx): M2 evalúa con la tabla `thresholds` y las reglas de `Domain`, registra un aviso en el *log* y la respuesta **no falla**. El detalle está en la [ficha de M2](../modulos/M2-evaluacion/README.md#6-python-y-respaldo-local).

## 7. Mapa completo de rutas

| Método | Ruta | Módulo | Acceso |
|---|---|---|---|
| POST | `/api/v1/auth/login` | M0 | Público · *throttle* |
| POST | `/api/v1/auth/refresh` | M0 | *Cookie* `refresh_token` |
| POST | `/api/v1/auth/logout` | M0 | Token |
| GET | `/api/v1/auth/me` | M0 | Token |
| POST | `/api/v1/auth/device-token` | M0 | Público · *throttle* |
| POST | `/api/v1/ingest/readings` | M1 | `measurements.ingest` (dispositivo) |
| GET | `/api/v1/indicators` | M1 | `crew.read` |
| POST | `/api/v1/measurements` | M1 | `measurements.write.own` |
| GET | `/api/v1/astronauts/{id}/measurements?indicator=&range=7d\|30d` | M4 | `health.read` |
| GET | `/api/v1/astronauts/{id}/assessment` | M2 | `health.read` |
| GET | `/api/v1/astronauts/{id}/trends` | M4 | `health.read` |
| GET | `/api/v1/body-parts` | M3 | `crew.read` |
| GET | `/api/v1/astronauts/{id}/body-status` | M3 | `health.read` |
| GET | `/api/v1/recommendations?indicator=&status=` | M5 | `crew.read` |
| GET | `/api/v1/alerts` | M5 | `alerts.read` |
| POST | `/api/v1/alerts` (SOS, ayuda o novedad) | M5 | `alerts.send` (suya) |
| PATCH | `/api/v1/alerts/{id}/acknowledge` | M5 | `alerts.acknowledge` |
| GET | `/api/v1/environments` | M6 | `crew.read` |
| GET | `/api/v1/missions/current` | M6 | `crew.read` |
| PATCH | `/api/v1/missions/{id}` | M6 | `mission.update` |
| GET · POST | `/api/v1/missions` | M6 | `missions.manage` |
| GET · PUT | `/api/v1/thresholds?environment=` · `/api/v1/thresholds/{indicator}` | M2 | `crew.read` · `guide.manage` |
| PUT | `/api/v1/recommendations/{indicator}/{status}` | M5 | `guide.manage` |
| GET · POST · PUT | `/api/v1/psych-tests` · `/api/v1/psych-tests/{id}` | M2 | `crew.read` · `guide.manage` |
| GET · POST | `/api/v1/psych-results` | M2 | `health.read` (de la tripulación, `health.read.any`) · el astronauta manda las suyas |
| POST · PATCH | `/api/v1/astronauts` · `/api/v1/astronauts/{id}` | M6 | `missions.manage` |
| GET | `/api/v1/astronauts` | M6 | `crew.read` |
| GET | `/api/v1/astronauts/{id}/gamification` | M7 | `gamification.read` |
| GET | `/api/v1/leaderboard` | M7 | `gamification.read` |
| GET · POST · PATCH · DELETE | `/api/v1/reminders` | M8 | `reminders.manage.own` |
| POST | `/api/v1/reminders/{id}/done` | M8 | `reminders.manage.own` |

`health.read` = el médico y el admin ven a cualquiera; el astronauta, solo a sí mismo. La matriz completa está en el [backend](../../astrohealth/docs/backend/README.md#3-roles-y-permisos).

## 8. Autenticación (JWT)

| Quién | Cómo obtiene el token | Vida | Renovación |
|---|---|---|---|
| Persona | `POST /auth/login` (correo + contraseña) | 15 min | `POST /auth/refresh` con la *cookie* `HttpOnly`; el *refresh* rota en cada uso |
| Máquina | `POST /auth/device-token` (`client_id` + `client_secret`) | 15 min | Pide otro con sus credenciales |

- Toda petición protegida lleva `Authorization: Bearer <access_token>`.
- Un token vencido responde **401 `AUTH_TOKEN_EXPIRED`**: Vue llama a `/auth/refresh` y repite la petición **una vez**.
- Vue guarda el *access token* **en memoria** (Pinia), nunca en `localStorage`.

El diseño completo (*claims*, roles, permisos, seguridad y pruebas) está en [M0 · Identidad · backend](../../astrohealth/docs/backend/M0-identidad/README.md).
