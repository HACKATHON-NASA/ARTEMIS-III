# 🚀 AstroHealth — Plan de Módulos
**NASA Space Apps Challenge 2026 · Reto: Software de monitorización de la salud para astronautas**

Stack: **Laravel (API) + Vue 3 (frontend) + MySQL + Python (FastAPI: motor de evaluación y datos simulados)**

---

## 0. Arquitectura general

```
[Vue 3 + Pinia + Chart.js + SVG]  ──HTTP──▶  [Laravel API REST]  ──▶  [MySQL]
                                                    │
                                                    └──HTTP──▶ [Python FastAPI: /evaluate, /trends, /seed]
```

- **Laravel**: autenticación, CRUD, alertas, gamificación y recordatorios. Es el único que habla con el frontend.
- **Python**: recibe las mediciones, devuelve el semáforo, el puntaje, las tendencias y las recomendaciones. También genera los 30 días de datos simulados.
- **Vue**: toda la parte visual. Cuerpo SVG, dashboard, animaciones.

### Convenciones del equipo
- Ramas: `main` (lo que se presenta) · `dev` · `feature/mX-nombre`
- API con prefijo `/api/v1`, respuestas JSON `{ data, message }`
- Colores del semáforo (usar SIEMPRE estos):
  - 🟢 `#22C55E` normal · 🟡 `#EAB308` precaución · 🔴 `#EF4444` crítico · ⚪ `#64748B` sin datos

---

## 1. Base de datos (MySQL) — HACER PRIMERO

| Tabla | Campos clave |
|---|---|
| `astronauts` | id, name, role, avatar, mission_id, user_id |
| `missions` | id, name, environment (`iss`, `transit`, `moon`, `mars`), start_date, day_count |
| `indicators` | id, code (`hr`, `bp_sys`, `bp_dia`, `glucose`, `temp`, `sleep`, `mood`, `spo2`, `radiation`), name, unit, body_part, help_text |
| `thresholds` | id, indicator_id, environment, green_min, green_max, yellow_min, yellow_max |
| `measurements` | id, astronaut_id, indicator_id, value, status (`green/yellow/red`), measured_at |
| `assessments` | id, astronaut_id, score (0–100), summary, created_at |
| `recommendations` | id, indicator_id, status, title, action_text, risk_ref |
| `alerts` | id, astronaut_id, measurement_id, level, message, sent_to_ground (bool), ground_eta_at, acknowledged_at |
| `body_parts` | id, code (`heart`, `brain`, `eyes`, `bones`, `immune`, `ear`, `lungs`), name, function_text, space_risk_text |
| `reminders` | id, astronaut_id, type, title, cron / time, last_done_at |
| `achievements` | id, code, name, description, icon, points |
| `astronaut_achievements` | astronaut_id, achievement_id, earned_at |
| `points_log` | id, astronaut_id, points, reason, created_at |

---

## 2. Umbrales de referencia (para el motor de evaluación)

> ⚠️ Son valores de referencia generales para la demo, no criterio médico. En la presentación decir: *"umbrales configurables por los médicos de vuelo"*.

| Indicador | Unidad | 🟢 Verde | 🟡 Amarillo | 🔴 Rojo | Parte del cuerpo |
|---|---|---|---|---|---|
| Frecuencia cardíaca | lpm | 60–100 | 50–59 / 101–120 | <50 / >120 | Corazón |
| Presión sistólica | mmHg | 90–119 | 120–139 | <90 / ≥140 | Corazón |
| Presión diastólica | mmHg | 60–79 | 80–89 | <60 / ≥90 | Corazón |
| Glucosa (ayunas) | mg/dL | 70–99 | 60–69 / 100–125 | <60 / ≥126 | Metabolismo |
| Temperatura | °C | 36.1–37.2 | 35.5–36.0 / 37.3–38.0 | <35.5 / >38.0 | Sistema inmune |
| SpO₂ | % | ≥95 | 90–94 | <90 | Pulmones |
| Sueño | horas | 7–9 | 6–6.9 | <6 | Cerebro |
| Estado de ánimo | 1–5 | 4–5 | 3 | 1–2 | Mente |
| Radiación diaria | mSv/día | según entorno | según entorno | según entorno | Todo el cuerpo |

**Puntaje general (0–100):** verde = 100, amarillo = 60, rojo = 20 por indicador → promedio ponderado (corazón, SpO₂ y radiación pesan ×1.5).

**Tendencia:** si un indicador empeora 3 mediciones seguidas, se marca amarillo aunque el valor esté verde ("tendencia negativa").

---

## 3. Los 8 módulos

### 🩺 M1 — Registro de indicadores

**Backend (Laravel)**
- `POST /api/v1/measurements` — guarda una o varias mediciones; llama a Python `/evaluate` y guarda el `status`.
- `GET /api/v1/indicators` — lista con unidad, rango y `help_text`.
- Validaciones por indicador (rangos físicamente posibles: FC 20–250, temp 30–45, etc.).

**Frontend (Vue)**
- `MeasurementForm.vue`: un campo por indicador con su unidad.
- Ánimo con 5 caritas clicables 😞 → 😄.
- Botón **"¿Cómo me mido?"** por campo → modal con instrucción simple y por qué importa en el espacio.
- Al guardar: animación del valor "volando" hacia el órgano del cuerpo.

**Listo cuando:** se registra una medición y queda guardada con su color.

---

### 🚦 M2 — Motor de evaluación (semáforo + puntaje)

**Backend (Python FastAPI)**
- `POST /evaluate` → recibe `{indicator, value, environment}` y devuelve `{status, message}`.
- `POST /assess` → recibe las últimas mediciones y devuelve `{score, status_por_organo, resumen}`.
- `POST /trends` → recibe una serie y devuelve `{direction: up/down/stable, alert: bool}`.
- `POST /seed` → genera 30 días de datos realistas por astronauta (con 1–2 eventos de deterioro para la demo).

**Backend (Laravel)**
- `EvaluationService.php`: cliente HTTP hacia Python con *fallback* local (si Python se cae, Laravel evalúa con la tabla `thresholds`).
- `GET /api/v1/astronauts/{id}/assessment` — puntaje y estado de cada órgano.

**Frontend (Vue)**
- `HealthScore.vue`: medidor circular animado 0–100 con el color del estado.
- `StatusBadge.vue`: chip reutilizable verde/amarillo/rojo.

**Listo cuando:** cada medición devuelve color y el puntaje se actualiza.

---

### 🧍 M3 — Cuerpo humano interactivo ⭐

**Backend (Laravel)**
- `GET /api/v1/body-parts` — función, riesgo espacial y texto de ayuda de cada órgano.
- `GET /api/v1/astronauts/{id}/body-status` — `{heart: 'yellow', brain: 'green', ...}` con los últimos valores asociados.

**Frontend (Vue)**
- `BodyMap.vue`: SVG de figura humana con un `<path>` por zona (corazón, cerebro, ojos, pulmones, huesos/músculos, oído interno, sistema inmune).
- Cada zona se pinta con el color de su estado y **pulsa suavemente** si está en amarillo o rojo.
- Hover: tooltip con nombre y último valor.
- Clic: `BodyPartPanel.vue` (panel lateral deslizable) con:
  1. Función del órgano
  2. Estado actual y valores
  3. Cómo lo afecta el espacio (los 5 peligros de la NASA)
  4. Qué hacer (recomendación del M5)
- Librerías sugeridas: SVG propio + transiciones de Vue / GSAP.

| Órgano | Indicadores | Riesgo espacial (NASA HRP) |
|---|---|---|
| Corazón | FC, presión | Adaptaciones cardiovasculares en microgravedad |
| Cerebro / mente | sueño, ánimo | Aislamiento, confinamiento, alteración del ritmo circadiano |
| Ojos | (síntomas visuales) | SANS: síndrome neuro-ocular |
| Pulmones | SpO₂ | Hipoxia, ambiente cerrado |
| Huesos / músculos | (ejercicio registrado) | Pérdida ósea y muscular por gravedad alterada |
| Oído interno | (mareo reportado) | Alteración vestibular por cambios de gravedad |
| Sistema inmune | temperatura | Alteración inmunitaria, reactivación de virus latentes |

**Listo cuando:** dar clic en un órgano muestra su información y el color cambia según los datos reales.

---

### 📈 M4 — Dashboard con gráficas en el tiempo

**Backend (Laravel)**
- `GET /api/v1/astronauts/{id}/measurements?indicator=hr&range=7d|30d`
- `GET /api/v1/astronauts/{id}/trends` — llama a Python `/trends` para todos los indicadores.

**Frontend (Vue)**
- `DashboardView.vue` con:
  - Tarjetas resumen por indicador (valor actual, color, flecha ↑↓).
  - `TrendChart.vue` (Chart.js / vue-chartjs): línea con **bandas de color de fondo** verde/amarillo/rojo.
  - Selector 7 / 30 días.
  - Banner de tendencia: *"Tu sueño ha bajado 3 días seguidos"*.
- Gráficas animadas al cargar.

**Listo cuando:** se ven 30 días de datos simulados con colores y tendencias.

---

### 🚨 M5 — Recomendaciones y alertas a Tierra

**Backend (Laravel)**
- `GET /api/v1/recommendations?indicator=hr&status=yellow`
- Al guardar una medición roja → se crea un registro en `alerts` automáticamente (Observer de Eloquent).
- `POST /api/v1/alerts/{id}/send-to-ground` — marca enviada y calcula `ground_eta_at` según el entorno:
  - ISS: segundos · Luna: ~1.3 s · Marte: entre 4 y 24 min (usar 20 min en la demo)
- `PATCH /api/v1/alerts/{id}/acknowledge`

**Frontend (Vue)**
- `RecommendationCard.vue`: acción paso a paso (ej.: *"Presión alta → reposa 10 min, hidrátate, vuelve a medir"*).
- `AlertBanner.vue`: banner rojo animado arriba de la pantalla.
- `GroundLinkModal.vue`: animación de señal viajando Tierra ↔ nave con cuenta regresiva del retraso de comunicación.
- Centro de alertas con historial.

**Listo cuando:** un valor rojo dispara la alerta, muestra qué hacer y permite "enviar a Tierra" con el contador.

---

### 🌍 M6 — Selector de entorno de misión

**Backend (Laravel)**
- `GET /api/v1/environments` — ISS, tránsito, Luna y Marte, con gravedad, radiación diaria de referencia, retraso de comunicación y riesgos principales.
- `PATCH /api/v1/missions/{id}` — cambia el entorno; los umbrales se leen por `environment`.

**Frontend (Vue)**
- `EnvironmentSelector.vue`: 4 tarjetas con ilustración (estación, nave, Luna, Marte).
- Al cambiar de entorno: transición del fondo, recálculo de colores y riesgos destacados.
- Indicador de gravedad (ej.: Marte 0.38 g) y contador de días de misión.

**Listo cuando:** al cambiar de entorno cambian los riesgos mostrados y el retraso de comunicación.

---

### 🏆 M7 — Gamificación

**Backend (Laravel)**
- `GamificationService.php`: otorga puntos por eventos:
  - Registrar todos los indicadores del día: +10
  - Racha de 7 días: +50
  - Dormir 7 h o más: +5
  - Completar ejercicio: +15
  - Completar un recordatorio: +5
- `GET /api/v1/astronauts/{id}/gamification` — puntos, nivel, racha, insignias.
- `GET /api/v1/leaderboard` — ranking de la tripulación.

**Frontend (Vue)**
- `XpBar.vue`: barra de nivel animada.
- `StreakCounter.vue`: 🔥 contador de racha.
- `BadgeGrid.vue`: insignias (ej.: "Corazón de acero", "Dormilón espacial", "7 días en órbita").
- `Leaderboard.vue`: ranking de la tripulación.
- Confeti al ganar una insignia.

**Listo cuando:** registrar datos suma puntos y desbloquea insignias visibles.

---

### ⏰ M8 — Recordatorios de rutina

**Backend (Laravel)**
- CRUD `/api/v1/reminders`.
- Comando programado (`schedule:run`) que marca recordatorios pendientes.
- `POST /api/v1/reminders/{id}/done` → suma puntos (M7).
- Recordatorios por defecto: medir presión (mañana), ejercicio de resistencia 2 h, registrar sueño, chequeo de ánimo, dosímetro de radiación.

**Frontend (Vue)**
- `ReminderList.vue`: lista del día con checks.
- `ToastNotification.vue`: notificación emergente animada (*"Hora de medir tu presión 🩺"*).
- Notificaciones del navegador (Notification API) si el usuario lo permite.

**Listo cuando:** aparece el recordatorio, se marca hecho y suma puntos.

---

## 4. Pantallas (navegación)

1. **Login / selección de astronauta**
2. **Inicio** — cuerpo interactivo (M3) + puntaje (M2) + alertas (M5) + recordatorios (M8)
3. **Registrar** — formulario (M1)
4. **Dashboard** — gráficas (M4)
5. **Misión** — selector de entorno (M6)
6. **Logros** — gamificación (M7)
7. **Centro de alertas** — historial y enlace con Tierra (M5)

---

## 5. Reparto del equipo

| Persona | Rol | Módulos |
|---|---|---|
| **P1** | Backend Laravel | BD, migraciones, seeders, API de M1, M5, M6, M7, M8 |
| **P2** | Python / datos | M2 completo, generador de datos simulados, umbrales, tendencias |
| **P3** | Frontend Vue (visual) | M3 cuerpo interactivo, M6 selector, animaciones, layout general |
| **P4** | Frontend Vue (datos) + pitch | M1 formulario, M4 dashboard, M5, M7, M8 en UI, video y presentación |

> Si son 3: P1 toma también el M8 y P4 se reparte entre P3 y P1. Si son 5: el quinto se dedica solo a pitch, video, investigación NASA y pruebas.

---

## 6. Orden de construcción

| Fase | Qué | Responsable |
|---|---|---|
| **1** | Repo, estructura, migraciones, seeders, umbrales, datos simulados de 30 días | P1 + P2 |
| **2** | M1 registro + M2 evaluación conectados de punta a punta | Todos |
| **3** | M3 cuerpo interactivo + M4 dashboard | P3 + P4 |
| **4** | M5 alertas + M6 entorno | P1 + P3 |
| **5** | M7 gamificación + M8 recordatorios | P1 + P4 |
| **6** | Pulir UI, animaciones, responsive, pruebas | Todos |
| **7** | Video (máx. 30 s si lo piden así, revisar reglas), slides, README, despliegue | P4 + todos |

> 🔒 **Regla de oro:** no se empieza la Fase 5 sin que las Fases 2–4 funcionen de punta a punta. Dejar **mínimo 4 horas** para la Fase 7.

---

## 7. Guion de la demo (2 minutos)

1. **Contexto (15 s):** misión a Marte, día 120. El astronauta está a 20 minutos-luz de un médico.
2. **Registro (20 s):** registra sus datos; los valores vuelan al cuerpo.
3. **Cuerpo (25 s):** el corazón pulsa en amarillo; clic → explica el efecto de la microgravedad y qué hacer.
4. **Tendencia (20 s):** la gráfica muestra que su sueño viene cayendo hace 3 días.
5. **Alerta (20 s):** valor rojo → recomendación inmediata → "Enviar a Tierra" con el contador de 20 min.
6. **Entorno (10 s):** cambia a ISS y se ve cómo cambian los riesgos y el retraso.
7. **Gamificación (10 s):** completa el recordatorio, sube de nivel, confeti 🎉.

---

## 8. Checklist final de entrega

- [ ] Los 8 módulos funcionando con datos simulados
- [ ] Desplegado con URL pública
- [ ] Repositorio con README (instalación, arquitectura, capturas)
- [ ] Video demo
- [ ] Slides: problema → solución → demo → impacto → próximos pasos
- [ ] Citar los recursos de la NASA usados (HRP: 5 peligros, riesgos, investigación en curso; CSA radiación)
- [ ] Aviso: "Herramienta de apoyo, no reemplaza criterio médico"

---

## 9. Recursos usados

- NASA HRP — 5 peligros: https://www.nasa.gov/hrp/hazards/
- NASA HRP — Riesgos: https://www.nasa.gov/hrp/risks/
- NASA HRP — Investigación en curso: https://www.nasa.gov/hrp/ongoing-hrp-research/
- CSA — Monitorización de radiación: https://www.asc-csa.gc.ca/eng/news/articles/2026/2026-02-26-monitoring-radiation-astronaut-health.asp
- CSA — ¿Por qué ciencia en el espacio?: https://www.asc-csa.gc.ca/eng/iss/science/why-do-we-conduct-science-experiments-in-space.asp
