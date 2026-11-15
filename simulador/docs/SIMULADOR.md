# 🛰️ AstroHealth — Simulador (Python)

> Instrucciones para Claude Code. Todo este trabajo vive **solo dentro de la carpeta `simulador/`**.
> La app (Laravel + Vue) está en `astrohealth/` y **no se toca** desde aquí.
>
> **Versión 2 (2026-11-15).** El simulador pasa a ser **solo los aparatos de la nave**: mide y envía
> valores crudos. Se eliminó el motor de evaluación (semáforo, puntaje, tendencias, recomendaciones y
> alertas) y el ánimo. Las fases 1 a 7 de la versión 1 ya están hechas (ver §12).

---

## 0. Contexto del proyecto

- **Reto:** NASA Space Apps Challenge 2026 — *Crear software de monitorización de la salud para astronautas en misiones espaciales.*
- **Qué es el simulador:** un servicio en Python que **imita los aparatos médicos de una nave** y de su tripulación:
  1. **Trae datos REALES de la NASA**
     - **DONKI:** clima espacial (fulguraciones, partículas energéticas solares, tormentas geomagnéticas). Mueve la radiación.
     - **OSDR:** análisis de sangre reales de Inspiration4 (OSD-569 hemograma, OSD-575 panel metabólico). Calibran el analizador de sangre y el glucómetro.
  2. **Simula el cuerpo de cada astronauta** (su fisiología día a día y minuto a minuto), afectado por la microgravedad, el sueño, el ejercicio y los eventos reales de DONKI.
  3. **Simula los aparatos que miden ese cuerpo**: cada aparato mide cuando le toca (cada minuto, una vez al día o una vez por semana), con su propio ruido de sensor, y produce **lecturas crudas**.
  4. **Entrega las lecturas**: guardadas (JSON/CSV), por la API REST propia y, en la última fase, **enviadas al sistema principal (Laravel)** como un dispositivo autenticado.

- **Lo que el simulador NO hace:**
  - ❌ No evalúa: nada de semáforo, puntaje, tendencias, recomendaciones ni alertas. **Eso lo hace el sistema principal.**
  - ❌ No simula el ánimo ni ningún test psicológico: **lo hace el astronauta en la app** (entrada manual).
  - ❌ No simula nada que se registre a mano en la app.

- **Regla de honestidad (importante para los jueces):** cada lectura lleva su origen:
  - `"source": "nasa_donki"` — el valor depende de un evento real de DONKI (radiación)
  - `"source": "nasa_osdr"` — dato real de OSDR (solo en `/calibration`)
  - `"source": "simulated_calibrated_osdr"` — simulado y calibrado con OSDR (sangre, glucosa)
  - `"source": "simulated_reference"` — simulado con rangos fisiológicos de referencia (pulso, presión…)
  - `"source": "simulated_event"` — evento inventado para la demo (SEP del día 12, escenarios)

---

## 1. Stack y reglas de código

- Python **3.11+**
- Librerías: `fastapi`, `uvicorn[standard]`, `httpx`, `pydantic>=2`, `pydantic-settings`, `python-dotenv`, `pandas`, `numpy`, `pyyaml`, `apscheduler`, `pytest`, `pytest-asyncio`, `respx`
- Tipado completo y docstrings en español.
- Nada de valores mágicos en el código: aparatos, horarios, ruido y escenarios van en `config/*.yaml`.
- Toda llamada externa tiene **timeout, reintentos (3) y caché**. Si la NASA falla, se usa lo último que llegó y se marca `"stale": true`.
- **Reproducible:** con la misma `seed` se generan exactamente las mismas lecturas.
- **NUNCA** se sube la API key ni el secreto del dispositivo. Van en `simulador/.env`, que está en `.gitignore`.

---

## 2. Estructura de carpetas

```
simulador/
├── .env.example
├── .env                       # NO se sube
├── requirements.txt
├── README.md
├── config/
│   ├── environments.yaml      # ISS, tránsito, Luna, Marte (gravedad, dosis base, retraso)
│   ├── crew.yaml              # 4 astronautas ficticios con su línea base
│   ├── body.yaml              # efectos fisiológicos (microgravedad, sueño, SEP…)   [antes generator.yaml]
│   ├── devices.yaml           # catálogo de aparatos: métricas, horarios, ruido del sensor
│   └── scenarios.yaml         # escenarios de la demo (alteran el cuerpo, no el aparato)
├── data/
│   ├── osdr/raw/              # archivos originales de OSDR (NO editar)
│   ├── osdr/processed/        # calibration.json
│   ├── donki/cache/
│   └── output/                # lecturas generadas (JSON / CSV)
├── src/simulador/
│   ├── settings.py
│   ├── models.py              # contratos: Reading, ReadingBatch, Device…
│   ├── config.py
│   ├── collectors/            # donki.py, osdr.py
│   ├── calibration/           # osdr_calibration.py
│   ├── body/                  # el cuerpo simulado de cada astronauta
│   │   ├── crew.py
│   │   ├── physiology.py      # estado del cuerpo por día y por minuto
│   │   ├── space_weather.py   # clima espacial de la misión + dosis
│   │   └── scenarios.py
│   ├── devices/               # un archivo por aparato
│   │   ├── base.py            # clase Device: cuándo mide y cómo convierte
│   │   ├── biometric_shirt.py
│   │   ├── bp_monitor.py
│   │   ├── thermometer.py
│   │   ├── glucometer.py
│   │   ├── blood_analyzer.py
│   │   ├── sleep_tracker.py
│   │   ├── exercise_machine.py
│   │   ├── radiation_monitor.py
│   │   └── personal_dosimeter.py
│   ├── runtime/
│   │   ├── history.py         # modo historial: genera N días de golpe
│   │   ├── clock.py           # reloj de la simulación (con aceleración)
│   │   └── live.py            # modo en vivo: los aparatos miden cuando les toca
│   ├── exporters/
│   │   ├── json_csv.py
│   │   └── laravel.py         # ÚLTIMA FASE: envío al sistema principal
│   └── api/main.py
├── scripts/
│   ├── fetch_donki.py
│   ├── fetch_osdr.py
│   ├── build_calibration.py
│   ├── generate_mission.py    # modo historial
│   ├── run_live.py            # modo en vivo
│   └── run_api.py
└── tests/
```

---

## 3. Variables de entorno (`.env.example`)

```env
NASA_API_KEY=pon_aqui_tu_key
DONKI_BASE_URL=https://api.nasa.gov/DONKI
DONKI_FALLBACK_URL=
OSDR_BASE_URL=https://osdr.nasa.gov
CACHE_TTL_MINUTES=30
HTTP_TIMEOUT_SECONDS=15
HTTP_CONNECT_TIMEOUT_SECONDS=4

API_HOST=0.0.0.0
API_PORT=8001
CORS_ORIGINS=http://localhost:5173,http://localhost:8000

# Modo en vivo
SIM_TIME_SCALE=1              # 1 = tiempo real; 60 = un minuto de misión por segundo
SIM_SEED=42

# ÚLTIMA FASE: envío al sistema principal (mismos valores que SIMULADOR_CLIENT_* de Laravel)
ASTROHEALTH_ENABLED=false
ASTROHEALTH_API_URL=http://localhost:8000/api/v1
ASTROHEALTH_CLIENT_ID=sim-ares-1
ASTROHEALTH_CLIENT_SECRET=
```

---

## 4. Módulo DONKI (`collectors/donki.py`) — HECHO

Trae eventos reales de clima espacial y los convierte en **riesgo de radiación por día**.

| Código | Evento | Uso |
|---|---|---|
| `FLR` | Fulguración solar | Clase M o X sube el riesgo (el endpoint FLR responde 404: se reconstruye desde `notifications`) |
| `SEP` | Partículas energéticas solares | **Evento principal de radiación** |
| `GST` | Tormenta geomagnética | Kp ≥ 6 = riesgo medio |
| `CME` | Eyección de masa coronal | Aviso anticipado |
| `notifications` | Notificaciones | Feed de noticias |

Reglas de `risk_level` y `radiation_multiplier`:
- Hay SEP → `high`, ×3.0
- Fulguración clase X → `high`, ×2.0
- Fulguración clase M o Kp ≥ 6 → `medium`, ×1.5
- En otro caso → `low`, ×1.0

Caché en memoria `CACHE_TTL_MINUTES`, reintentos, respaldo opcional y `stale` si la NASA no responde.

---

## 5. Módulo OSDR (`collectors/osdr.py` + `calibration/`) — HECHO

- Archivos reales en `data/osdr/raw/`: **OSD-569** (hemograma, CBC) y **OSD-575** (panel metabólico, CMP).
- `calibration.json` tiene ~40 analitos con `baseline_mean`, `baseline_sd`, `postflight_mean`, `delta_pct`, `unit`, `n_subjects`, `datasets` y `postflight_timepoint`.
- **Ojo (honestidad):** esas muestras se tomaron **en Tierra**, antes del vuelo (L-92, L-44, L-3) y después (R+1, R+45, R+82). Sirven para saber **cuánto cambia la sangre por el vuelo**, no son mediciones a bordo.

**Uso en la versión 2:**
- **Analizador de sangre (semanal):** cada analito de `calibration.json` se simula con media `baseline_mean` (más la desviación personal del astronauta), ruido `baseline_sd` y una **deriva progresiva hacia `delta_pct`** a lo largo de la misión.
- **Glucómetro (diario):** usa el analito `glucose` con la misma deriva.
- Sin `calibration.json`, ambos usan valores de referencia y se marcan `simulated_reference`.

---

## 6. Los aparatos (`devices/` + `config/devices.yaml`)

**Idea central:** el cuerpo simulado tiene un valor "verdadero" en cada momento; el aparato lo **mide** cuando le toca, le suma **su ruido de sensor**, lo **redondea** a la resolución del aparato y lo **convierte** a su unidad. El aparato no opina si el valor es bueno o malo.

Los horarios no son exactos: cada toma lleva una variación aleatoria de minutos (`jitter_min`), controlada por la `seed`. Horas en UTC (hora de la nave).

| # | Aparato (`device_type`) | Inspirado en | Qué mide (`metric` · unidad) | Cuándo |
|---|---|---|---|---|
| 1 | **Camiseta biométrica** `biometric_shirt` | Astroskin / Bio-Monitor (CSA, ISS) | `hr` bpm · `spo2` % · `resp_rate` rpm · `skin_temp` °C · `activity` g | **Cada minuto mientras la tiene puesta** |
| 2 | **Tensiómetro** `bp_monitor` | Tensiómetro automático de brazo | `bp_sys` mmHg · `bp_dia` mmHg · `pulse` bpm | 1/día, hacia las **08:00** (± 20 min) |
| 3 | **Termómetro** `thermometer` | Termómetro clínico | `body_temp` °C | 1/día, hacia las **08:00** (± 20 min) |
| 4 | **Glucómetro** `glucometer` | Glucómetro de punción en el dedo | `glucose` mg/dL | 1/día en ayunas, hacia las **07:30** (± 15 min) |
| 5 | **Analizador de sangre** `blood_analyzer` | Bio-Analyzer (CSA, ISS) | ~40 analitos de `calibration.json` (unidad de OSDR) | **1/semana** (días 7, 14, 21…), hacia las 07:00 |
| 6 | **Reloj de sueño** `sleep_tracker` | Actígrafo (Actiwatch) | `sleep_h` h · `awakenings` n · `sleep_efficiency` % | 1/día al despertar, hacia las **06:30** |
| 7 | **Máquina de ejercicio** `exercise_machine` | Cinta / bicicleta / ARED | `exercise_min` min · `exercise_type` | Al terminar cada sesión (1–2 al día) |
| 8 | **Monitor de radiación de la nave** `radiation_monitor` | HERA (Orion) / ISS-RAD | `dose_rate` µSv/h | **Cada minuto**, siempre (es de la nave, no de una persona) |
| 9 | **Dosímetro personal** `personal_dosimeter` | Crew Active Dosimeter (Orion) | `dose_hour` µSv · `dose_cumulative` mSv | **Cada hora**, siempre (lo lleva puesto toda la misión) |

### 6.1 Camiseta biométrica: sesiones de uso

- La camiseta solo manda datos **mientras el astronauta la tiene puesta**. Cada vez que se la pone empieza una **sesión** (`session_id`); al quitársela, la sesión termina.
- Sesiones por defecto (`devices.yaml`, con variación aleatoria de inicio y duración):
  - **Noche:** de ~22:30 a ~06:30 (monitoreo del sueño).
  - **Mañana:** ~1 h después de levantarse.
  - **Ejercicio:** durante cada sesión de la máquina de ejercicio (el pulso y la actividad suben).
- Fuera de una sesión no hay lecturas de la camiseta: **la falta de datos es normal**, no un error.
- Volumen aproximado: ~10 h puestas × 60 min × 5 métricas ≈ **3.000 lecturas por astronauta por día**. Las lecturas de cada minuto viajan en **paquetes** (§8).

### 6.2 Radiación: cómo se mide

- **Monitor de la nave:** `dose_rate` (µSv/h) = dosis base del entorno (`environments.yaml`, mSv/día → µSv/h) × multiplicador DONKI del día × blindaje del hábitat + ruido. Durante un **SEP** la tasa no salta de golpe: sube en unas horas, llega al pico y baja (perfil configurable).
- **Dosímetro personal:** suma la dosis que recibe cada astronauta hora a hora (la tasa del ambiente × su ubicación: dentro del hábitat, en el refugio durante un SEP, o fuera en una actividad extravehicular). Manda la dosis de esa hora y el **acumulado de la misión**.
- `source`: `nasa_donki` en los días con datos reales de DONKI; `simulated_reference` en días tranquilos sin datos; `simulated_event` en el SEP inventado de la demo.

---

## 7. El cuerpo simulado (`body/` + `config/body.yaml`)

Cada astronauta tiene su línea base (`crew.yaml`) y un estado fisiológico que cambia con la misión. Los aparatos leen este estado.

**Efectos (simples, coherentes con los riesgos del NASA HRP):**
- **Microgravedad:** el pulso en reposo baja unos 4 lpm en las primeras semanas; la presión es más variable (escalan con 1 − gravedad del entorno).
- **Ritmo del día:** pulso y temperatura más bajos durmiendo, más altos de tarde y durante el ejercicio.
- **Sueño:** baja poco a poco hacia ~6,5 h; empeora la noche siguiente a un SEP.
- **Ejercicio:** ~110 min/día en 1–2 sesiones; el día de un SEP se entrena menos (el astronauta está en el refugio).
- **Glucosa y sangre:** deriva progresiva hacia el cambio real de Inspiration4 (`delta_pct`).
- **Radiación:** dosis base × DONKI × blindaje (§6.2).
- **Ruido** fisiológico gaussiano en todo, controlado por `seed`. (Aparte va el ruido del sensor de cada aparato.)

**Escenarios de la demo (`scenarios.yaml`, `inject_demo_events: true`):** cambian **el cuerpo**, nunca el aparato. El aparato solo mide lo que hay; quien decide si es una alerta es el sistema principal.
- **Insomnio:** astronauta 2, días 18–21, el sueño cae de 5,8 a 5,0 h.
- **Hipertensión:** astronauta 3, desde el día 24, la presión sube a ~148/94.
- **SEP simulado:** si DONKI no trae ningún SEP en el rango, se inyecta uno el día 12 (`simulated_event`).

---

## 8. Formato de las lecturas (`models.py`)

**Una lectura (`Reading`), sin semáforo:**
```json
{
  "reading_id": "c1b0…",
  "device_id": "shirt-3",
  "device_type": "biometric_shirt",
  "astronaut_id": 3,
  "metric": "hr",
  "value": 71,
  "unit": "bpm",
  "measured_at": "2026-11-15T08:04:00Z",
  "session_id": "shirt-3-20261115-0731",
  "source": "simulated_reference"
}
```
- `astronaut_id` es `null` en el monitor de radiación de la nave.
- `session_id` solo en la camiseta.
- `reading_id` es único y determinista (seed + aparato + hora), para que el sistema principal pueda descartar duplicados si un paquete se reenvía.
- Valores no numéricos (`exercise_type`) van en `value_text` en lugar de `value`.

**Un paquete (`ReadingBatch`):** lo que envía un aparato de una vez.
```json
{
  "device_id": "shirt-3",
  "sent_at": "2026-11-15T08:05:00Z",
  "readings": [ /* Reading[] */ ]
}
```
- Camiseta y monitor de radiación: un paquete **cada minuto** de misión (configurable en `devices.yaml`).
- Los demás aparatos: un paquete por toma.

---

## 9. Modos de funcionamiento (`runtime/`)

**Modo historial** (`generate_mission.py` o `POST /simulate`): genera los N días de una misión de golpe, para rellenar el pasado o hacer pruebas.

Entrada:
```json
{
  "mission_name": "Ares-1",
  "environment": "mars",
  "days": 30,
  "start_date": "2026-10-15",
  "crew_size": 4,
  "seed": 42,
  "use_real_space_weather": true,
  "inject_demo_events": true,
  "devices": ["bp_monitor", "glucometer"]
}
```
`devices` es opcional (por defecto, todos). Salida: `mission`, `crew`, `devices`, `space_weather`, `readings`, `meta` → `data/output/{mission}_{seed}.json` + CSV.

**Modo en vivo** (`run_live.py` o `POST /live/start`): un reloj avanza minuto a minuto (`SIM_TIME_SCALE` para acelerarlo en la demo) y cada aparato produce su paquete **cuando le toca**. Los paquetes van a una cola de salida: en esta versión se pueden consultar por la API; en la última fase se envían a Laravel. Un `apscheduler` refresca DONKI cada `CACHE_TTL_MINUTES`.

---

## 10. API REST (`api/main.py`)

Base: `http://localhost:8001/api/v1` · CORS desde `CORS_ORIGINS` · documentación en `/docs`.

| Método | Ruta | Qué hace | Estado |
|---|---|---|---|
| GET | `/health` | Estado, última actualización de DONKI, calibración cargada | hecho |
| GET | `/space-weather?start=&end=` | Clima espacial real por día | hecho |
| GET | `/space-weather/now` | Riesgo de hoy y eventos activos | hecho |
| GET | `/calibration` | Calibración OSDR ("datos reales NASA") | hecho |
| GET | `/environments` | Los 4 entornos | hecho |
| GET | `/devices` | Catálogo de aparatos: métricas, unidades, frecuencia | nuevo |
| POST | `/simulate` | Modo historial (§9) | adaptar |
| POST | `/live/start` · `/live/stop` | Arranca o detiene el modo en vivo | nuevo |
| GET | `/live/status` | Reloj de la simulación, aparatos activos, sesiones abiertas | nuevo |
| GET | `/live/readings?since=` | Paquetes producidos desde una fecha | nuevo |

Eliminados en la versión 2: `/evaluate`, `/assess`, `/trends`, `/recommendations`.

Respuestas: `{"data": ..., "message": "ok"}`. Errores: `{"data": null, "message": "...", "error_code": "..."}`.

---

## 11. Envío al sistema principal (`exporters/laravel.py`) — ÚLTIMA FASE

Se hace **al final**, cuando lo demás funcione.
- El simulador es un **dispositivo** en Laravel (módulo Identidad: `devices`, `DeviceSeeder`, `SIMULADOR_CLIENT_ID` / `SIMULADOR_CLIENT_SECRET`).
- Pide su token con `POST {ASTROHEALTH_API_URL}/auth/device-token` usando `ASTROHEALTH_CLIENT_ID` / `ASTROHEALTH_CLIENT_SECRET` y lo renueva cuando vence.
- Envía cada `ReadingBatch` al endpoint de lecturas de Laravel. **Antes de programar:** leer las rutas, requests y migraciones reales de `../astrohealth/` y adaptar el formato. **No inventar el contrato.**
- Si Laravel no responde: reintentos y **cola local** que se vacía al volver la conexión (sin perder ni duplicar lecturas, gracias a `reading_id`).

---

## 12. Fases

Cada fase termina con sus tests en verde antes de pasar a la siguiente.

**Versión 1 (hecha):**

| Fase | Tarea | Estado |
|---|---|---|
| 1 | Estructura, settings, models, `.gitignore` | ✅ |
| 2 | DONKI + caché + respaldo | ✅ |
| 3 | OSDR: descarga e inspección | ✅ |
| 4 | Calibración (`calibration.json`) | ✅ |
| 5 | Motor de evaluación | ✅ → **eliminado en la v2** |
| 6 | Generador de misión | ✅ (base del cuerpo simulado) |
| 7 | API REST | ✅ |
| — | Limpieza v2: sin motor, sin alertas, sin `status`, sin ánimo | ✅ (2026-11-15) |

**Versión 2 (pendiente):**

| Fase | Tarea | Listo cuando |
|---|---|---|
| **A** | Modelos `Reading`, `ReadingBatch`, `Device`; `config/devices.yaml`; `GET /devices` | El catálogo de los 9 aparatos sale por la API |
| **B** | `body/`: cuerpo simulado por día y por minuto + `scenarios.yaml` (mover `generator/` aquí) | El estado del cuerpo es reproducible con `seed` y los escenarios lo alteran |
| **C** | `devices/`: los 9 aparatos (horarios con variación, ruido del sensor, conversión, sesiones de la camiseta, perfil del SEP) | Cada aparato produce sus lecturas con el formato de §8 |
| **D** | Modo historial con aparatos (`/simulate`, `generate_mission.py`, JSON/CSV) | 30 días × 4 astronautas, reproducible |
| **E** | Modo en vivo (`clock.py`, `live.py`, `run_live.py`, `/live/*`) + refresco de DONKI | Con `SIM_TIME_SCALE=60` se ven paquetes llegar en `/live/readings` |
| **F** | `README.md` del simulador | Alguien nuevo lo corre en 5 minutos |
| **G** | **Envío a Laravel** (§11) | Las lecturas llegan al sistema principal autenticadas como dispositivo |

---

## 13. Reglas finales

- No modificar nada fuera de `simulador/`.
- No inventar datos de la NASA: si un archivo o un campo no existe, avisar y detenerse.
- Toda lectura lleva `source`.
- El simulador **no evalúa** ni simula entradas manuales (ánimo, tests): eso es del sistema principal y de la app.
- La API key y el secreto del dispositivo nunca se imprimen en los logs ni se suben al repositorio.
- Aviso en el README y en `/health`: *"Herramienta de apoyo para la demo. No reemplaza el criterio médico."*

---

## 14. Fuentes

- NASA DONKI: https://ccmc.gsfc.nasa.gov/tools/DONKI · https://api.nasa.gov
- NASA OSDR: https://osdr.nasa.gov
- Inspiration4 en OSDR (guía de acceso): https://www.ncbi.nlm.nih.gov/pmc/articles/PMC11094041/
- NASA HRP, peligros: https://www.nasa.gov/hrp/hazards/ · riesgos: https://www.nasa.gov/hrp/risks/
- Aparatos de referencia (verificar y citar antes de la presentación): Astroskin / experimento Bio-Monitor y Bio-Analyzer (Agencia Espacial Canadiense), Actiwatch (estudios de sueño en la ISS), HERA y Crew Active Dosimeter (Artemis I / Orion), ISS-RAD.
- Dosis base: Zeitlin et al., Science 2013 (crucero a Marte) · Hassler et al., Science 2014 (cráter Gale) · Zhang et al., Science Advances 2020 (Luna).
