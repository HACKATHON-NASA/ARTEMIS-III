# 🛰️ AstroHealth — Simulador (Python)

> Instrucciones para Claude Code. Todo este trabajo vive **solo dentro de la carpeta `simulador/`**.
> La app (Laravel + Vue) está en `astrohealth/` y **no se toca** desde aquí.

---

## 0. Contexto del proyecto

- **Reto:** NASA Space Apps Challenge 2026 — *Crear software de monitorización de la salud para astronautas en misiones espaciales.*
- **Qué es el simulador:** un servicio en Python que:
  1. **Trae datos REALES de la NASA**
     - **DONKI:** clima espacial (fulguraciones solares, partículas energéticas solares, tormentas geomagnéticas). Se usa para la radiación.
     - **OSDR:** datos clínicos reales de astronautas de la misión Inspiration4 (datasets OSD-569, OSD-570, OSD-571 y OSD-575: hemograma y paneles metabólicos).
  2. **Genera datos simulados de salud** de una tripulación durante N días de misión, **calibrados con los datos reales de OSDR** y afectados por los **eventos reales de DONKI**.
  3. **Evalúa** cada medición: semáforo verde/amarillo/rojo, puntaje de salud, tendencias y recomendaciones.
  4. **Expone todo por una API REST (FastAPI)** que consume la app Laravel.

- **Regla de honestidad (importante para los jueces):** cada dato debe llevar su origen:
  - `"source": "nasa_donki"`
  - `"source": "nasa_osdr"`
  - `"source": "simulated_calibrated_osdr"`
  - `"source": "simulated_reference"` (cuando no hay dato NASA para calibrar, como el pulso o la presión)

---

## 1. Stack y reglas de código

- Python **3.11+**
- Librerías: `fastapi`, `uvicorn[standard]`, `httpx`, `pydantic>=2`, `pydantic-settings`, `python-dotenv`, `pandas`, `numpy`, `sqlalchemy`, `pymysql`, `apscheduler`, `pytest`, `pytest-asyncio`, `respx`
- Tipado completo y docstrings en español.
- Nada de valores mágicos en el código: umbrales, entornos y recomendaciones van en archivos `config/*.yaml` o `*.json`.
- Toda llamada externa tiene **timeout, reintentos (3) y caché local**. Si la NASA falla, se usa la última caché y se marca `"stale": true`.
- **Reproducible:** el generador recibe `seed` y produce siempre lo mismo con la misma semilla.
- **NUNCA** se sube la API key. Va en `simulador/.env`, que debe estar en `.gitignore`.

---

## 2. Estructura de carpetas

```
simulador/
├── .env.example
├── .env                       # NO se sube (NASA_API_KEY, DB_*)
├── requirements.txt
├── README.md
├── config/
│   ├── thresholds.yaml        # umbrales verde/amarillo/rojo por indicador y entorno
│   ├── environments.yaml      # ISS, tránsito, Luna, Marte
│   ├── recommendations.yaml   # qué hacer por indicador y estado
│   ├── body_parts.yaml        # órgano ↔ indicadores ↔ riesgo NASA HRP
│   └── crew.yaml              # tripulación de ejemplo (4 astronautas)
├── data/
│   ├── osdr/raw/              # archivos originales descargados de OSDR (NO editar)
│   ├── osdr/processed/        # calibración calculada (JSON)
│   ├── donki/cache/           # respuestas cacheadas de DONKI
│   └── output/                # datasets generados (JSON / CSV)
├── src/simulador/
│   ├── __init__.py
│   ├── settings.py            # carga .env con pydantic-settings
│   ├── models.py              # modelos Pydantic (contratos)
│   ├── collectors/
│   │   ├── donki.py
│   │   └── osdr.py
│   ├── calibration/
│   │   └── osdr_calibration.py
│   ├── generator/
│   │   ├── crew.py
│   │   ├── vitals.py
│   │   ├── radiation.py
│   │   ├── events.py          # eventos de deterioro para la demo
│   │   └── seed.py            # orquestador
│   ├── engine/
│   │   ├── evaluate.py        # semáforo
│   │   ├── score.py           # puntaje 0–100
│   │   ├── trends.py          # tendencias
│   │   └── recommend.py
│   ├── exporters/
│   │   ├── json_csv.py
│   │   └── mysql.py           # opcional: escribe a la BD de la app
│   ├── scheduler.py           # refresca DONKI cada X minutos
│   └── api/main.py            # FastAPI
├── scripts/
│   ├── fetch_donki.py
│   ├── fetch_osdr.py
│   ├── build_calibration.py
│   └── generate_mission.py
└── tests/
    ├── test_donki.py
    ├── test_osdr.py
    ├── test_generator.py
    ├── test_engine.py
    └── test_api.py
```

---

## 3. Variables de entorno (`.env.example`)

```env
NASA_API_KEY=pon_aqui_tu_key
DONKI_BASE_URL=https://api.nasa.gov/DONKI
DONKI_FALLBACK_URL=https://kauai.ccmc.gsfc.nasa.gov/DONKI/WS/get
OSDR_BASE_URL=https://osdr.nasa.gov
CACHE_TTL_MINUTES=30
HTTP_TIMEOUT_SECONDS=15

API_HOST=0.0.0.0
API_PORT=8001
CORS_ORIGINS=http://localhost:5173,http://localhost:8000

# Opcional: exportar directo a la BD de Laravel
DB_ENABLED=false
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=astrohealth
DB_USERNAME=root
DB_PASSWORD=
```

---

## 4. Módulo DONKI (`collectors/donki.py`)

**Objetivo:** traer eventos reales de clima espacial y convertirlos en **riesgo de radiación por día**.

**Endpoints a consumir** (todos con `startDate`, `endDate` en formato `yyyy-MM-dd` y `api_key`):

| Código | Evento | Uso en la app |
|---|---|---|
| `FLR` | Fulguración solar | Alerta (clase M o X = importante) |
| `SEP` | Partículas energéticas solares | **Evento principal de radiación**: sube la dosis y dispara la alerta "refugio" |
| `GST` | Tormenta geomagnética | Riesgo moderado (relevante en la ISS) |
| `CME` | Eyección de masa coronal | Aviso anticipado de posible SEP |
| `notifications` | Notificaciones | Feed de noticias en la app |

**Funciones:**
```python
async def fetch_events(event_type: str, start: date, end: date) -> list[dict]
async def fetch_all(start: date, end: date) -> dict[str, list[dict]]
def daily_space_weather(events: dict, start: date, end: date) -> list[SpaceWeatherDay]
```

**`SpaceWeatherDay`:**
```json
{
  "date": "2026-11-10",
  "flare_max_class": "M2.1",
  "sep_event": true,
  "geomagnetic_kp_max": 6,
  "risk_level": "high",
  "radiation_multiplier": 3.0,
  "events": ["SEP-2026-11-10T04:00:00-SEP-001"],
  "source": "nasa_donki"
}
```

**Reglas de `risk_level` y `radiation_multiplier`:**
- Hay SEP → `high`, ×3.0
- Fulguración clase X → `high`, ×2.0
- Fulguración clase M o Kp ≥ 6 → `medium`, ×1.5
- En cualquier otro caso → `low`, ×1.0

**Requisitos:**
- Si `DONKI_BASE_URL` falla, reintentar con `DONKI_FALLBACK_URL` (no lleva api_key).
- Cachear en `data/donki/cache/{tipo}_{start}_{end}.json`.
- Manejar respuestas vacías (DONKI devuelve `[]` o cuerpo vacío cuando no hay eventos).
- Respetar los límites de la API: no hacer más de una consulta por tipo cada `CACHE_TTL_MINUTES`.

---

## 5. Módulo OSDR (`collectors/osdr.py` + `calibration/osdr_calibration.py`)

**Objetivo:** usar datos clínicos reales de Inspiration4 para calibrar el generador.

**Datasets:** `OSD-569`, `OSD-570`, `OSD-571`, `OSD-575` (hemograma completo y paneles metabólicos de sangre).

**Paso 1 — Descarga (`fetch_osdr.py`):**
- Intentar listar y descargar los archivos del estudio por la API de OSDR (`{OSDR_BASE_URL}/osdr/data/osd/files/569`, etc. ⚠️ **verificar la ruta exacta** en la documentación de OSDR antes de codificar).
- Si la descarga automática no funciona, el equipo pondrá los archivos a mano en `data/osdr/raw/`. **El código debe funcionar con archivos locales** sin depender de internet.

**Paso 2 — Inspección (antes de programar la calibración):**
- Abrir los archivos de `data/osdr/raw/`, listar las columnas, los analitos y los puntos de tiempo reales.
- Imprimir un resumen y **no inventar nombres de columnas**: detectar los que existan.
- Los puntos de tiempo vienen como pre-vuelo (`L-`), y post-vuelo (`R+`). Usar los que aparezcan en los datos.

**Paso 3 — Calibración (`build_calibration.py`)** → `data/osdr/processed/calibration.json`:
- Por cada analito disponible (por ejemplo: glucosa, calcio, leucocitos, hemoglobina, plaquetas, creatinina…):
  - `baseline_mean` y `baseline_sd`: promedio y desviación de los puntos **pre-vuelo**
  - `postflight_mean`: promedio del **primer punto post-vuelo**
  - `delta_pct`: `(postflight_mean - baseline_mean) / baseline_mean * 100`
  - `unit`
  - `n_subjects`
- Formato:
```json
{
  "glucose": {
    "unit": "mg/dL",
    "baseline_mean": 0,
    "baseline_sd": 0,
    "postflight_mean": 0,
    "delta_pct": 0,
    "n_subjects": 4,
    "source": "nasa_osdr",
    "datasets": ["OSD-575"]
  }
}
```
(Los `0` son marcadores; los valores reales salen de los archivos.)

**Uso en el generador:**
- Los indicadores que tienen analito en OSDR (glucosa y, si se quieren mostrar, leucocitos, calcio y hemoglobina) se generan con media `baseline_mean`, ruido `baseline_sd` y una **deriva progresiva** hacia `delta_pct` a lo largo de la misión. Se marcan como `simulated_calibrated_osdr`.
- Los signos vitales (pulso, presión, temperatura, SpO₂, sueño y ánimo) **no están en estos datasets**: se generan con rangos fisiológicos de referencia y se marcan como `simulated_reference`.

---

## 6. Generador de misión (`generator/`)

**Entrada (`generate_mission.py` o `POST /simulate`):**
```json
{
  "mission_name": "Ares-1",
  "environment": "mars",
  "days": 30,
  "start_date": "2026-10-15",
  "crew_size": 4,
  "seed": 42,
  "use_real_space_weather": true,
  "inject_demo_events": true
}
```

**Tripulación (`config/crew.yaml`):** 4 astronautas ficticios con nombre, rol, edad y su propia línea base (pulso en reposo, presión, horas de sueño habituales).

**Indicadores por día y por astronauta:**

| Indicador | Unidad | Frecuencia | Origen |
|---|---|---|---|
| `hr` (frecuencia cardíaca) | lpm | 2/día | simulated_reference |
| `bp_sys` / `bp_dia` (presión) | mmHg | 1/día | simulated_reference |
| `glucose` | mg/dL | 1/día | simulated_calibrated_osdr |
| `temp` | °C | 1/día | simulated_reference |
| `spo2` | % | 2/día | simulated_reference |
| `sleep` | horas | 1/día | simulated_reference |
| `mood` | 1–5 | 1/día | simulated_reference |
| `radiation` | mSv/día | 1/día | DONKI (multiplicador) × dosis base del entorno |
| `exercise_min` | min | 1/día | simulated_reference |

**Efectos realistas a modelar (simples, pero coherentes con los riesgos del HRP de la NASA):**
- **Microgravedad:** el pulso en reposo baja un poco en las primeras semanas y la presión es más variable.
- **Sueño:** tiende a bajar (6–6.5 h) y empeora el día siguiente a un evento SEP (estrés, refugio).
- **Ánimo:** baja lentamente con los días de aislamiento y sube si hubo ejercicio y buen sueño.
- **Radiación:** `dosis_base_entorno × radiation_multiplier` del día en DONKI.
- **Ejercicio:** si `exercise_min < 90`, aumenta el riesgo de huesos y músculos (sin indicador directo, solo nivel de riesgo).
- **Ruido gaussiano** en todo, controlado por `seed`.

**Dosis base por entorno (`config/environments.yaml`)**
⚠️ Son valores de referencia aproximados de la literatura; verificar y citar antes de la presentación:

| Entorno | Gravedad | Dosis base aprox. | Retraso de comunicación (una vía) |
|---|---|---|---|
| `iss` | ~0 g (órbita) | ~0.5 mSv/día | ~0 s |
| `transit` | 0 g | ~1.8 mSv/día | crece con la distancia |
| `moon` | 0.166 g | ~1.4 mSv/día | ~1.3 s |
| `mars` | 0.38 g | ~0.6 mSv/día | 4–24 min (demo: 20 min) |

**Eventos de demo (`inject_demo_events: true`) — para que la presentación tenga historia:**
- Astronauta 2, días 18–21: sueño cayendo (5.8 → 5.0 h) → tendencia negativa.
- Astronauta 3, día 24: presión 148/94 → **rojo** → alerta a Tierra.
- Si DONKI no trae ningún SEP en el rango: inyectar un SEP simulado el día 12, marcado `"source": "simulated_event"`.

**Salida** (`data/output/{mission}_{seed}.json` + CSV):
```json
{
  "mission": {"name": "Ares-1", "environment": "mars", "days": 30},
  "crew": [{"id": 1, "name": "...", "role": "..."}],
  "space_weather": [ /* SpaceWeatherDay[] */ ],
  "measurements": [
    {
      "astronaut_id": 1,
      "indicator": "hr",
      "value": 72,
      "unit": "bpm",
      "measured_at": "2026-10-15T08:00:00Z",
      "status": "green",
      "source": "simulated_reference"
    }
  ],
  "alerts": [],
  "meta": {"seed": 42, "generated_at": "...", "calibration_file": "calibration.json"}
}
```

---

## 7. Motor de evaluación (`engine/`)

**`evaluate.py`**: `evaluate(indicator, value, environment) -> {status, message}` usando `config/thresholds.yaml`.

Umbrales base (pueden variar por entorno). ⚠️ Son referencias generales para la demo, **no criterio médico**:

| Indicador | 🟢 | 🟡 | 🔴 |
|---|---|---|---|
| hr | 60–100 | 50–59 / 101–120 | <50 / >120 |
| bp_sys | 90–119 | 120–139 | <90 / ≥140 |
| bp_dia | 60–79 | 80–89 | <60 / ≥90 |
| glucose | 70–99 | 60–69 / 100–125 | <60 / ≥126 |
| temp | 36.1–37.2 | 35.5–36.0 / 37.3–38.0 | <35.5 / >38.0 |
| spo2 | ≥95 | 90–94 | <90 |
| sleep | 7–9 | 6–6.9 | <6 |
| mood | 4–5 | 3 | 1–2 |
| radiation (diaria) | ≤ base del entorno | ≤ 2× base | > 2× base o evento SEP activo |

**`score.py`:** verde = 100, amarillo = 60, rojo = 20; promedio ponderado (hr, bp, spo2 y radiation pesan ×1.5) → entero de 0 a 100.

**`trends.py`:** regresión lineal sobre las últimas 7 mediciones → `up | down | stable`. Si empeora 3 días seguidos → `trend_alert: true` aunque el valor siga en verde.

**`recommend.py`:** devuelve la recomendación de `config/recommendations.yaml` según el indicador y el estado. Si es rojo: `notify_ground: true` y `ground_delay_seconds` según el entorno.

**Mapeo a órganos (`config/body_parts.yaml`):**
- `heart` ← hr, bp_sys, bp_dia
- `lungs` ← spo2
- `brain` ← sleep, mood
- `immune` ← temp
- `metabolism` ← glucose
- `bones` ← exercise_min
- `whole_body` ← radiation

Cada órgano lleva el peor estado de sus indicadores.

---

## 8. API REST (`api/main.py`) — contrato con Laravel

Base: `http://localhost:8001/api/v1` · CORS desde `CORS_ORIGINS` · documentación automática en `/docs`.

| Método | Ruta | Qué hace |
|---|---|---|
| GET | `/health` | Estado del servicio, última actualización de DONKI, si hay calibración cargada |
| GET | `/space-weather?start=&end=` | Días de clima espacial (DONKI real) |
| GET | `/space-weather/now` | Riesgo de hoy + eventos activos (para el banner de la app) |
| GET | `/calibration` | Calibración OSDR (para mostrarla en la app: "datos reales NASA") |
| POST | `/simulate` | Genera una misión (cuerpo de la sección 6) y devuelve el JSON |
| POST | `/evaluate` | `{indicator, value, environment}` → `{status, message}` |
| POST | `/assess` | Lista de mediciones → `{score, body_status, summary}` |
| POST | `/trends` | Serie de un indicador → `{direction, trend_alert}` |
| GET | `/recommendations?indicator=&status=` | Recomendación |
| GET | `/environments` | Datos de los 4 entornos |

Todas las respuestas usan el formato `{"data": ..., "message": "ok"}` y los errores `{"data": null, "message": "...", "error_code": "..."}`.

---

## 9. Exportar a la app (`exporters/mysql.py`, opcional)

- Si `DB_ENABLED=true`, inserta la misión generada en las tablas de Laravel: `missions`, `astronauts`, `measurements`, `alerts`.
- **Antes de escribir:** leer las migraciones reales en `../astrohealth/database/migrations/` y adaptar los nombres de columnas. **No inventar el esquema.**
- Comando: `python scripts/generate_mission.py --env mars --days 30 --seed 42 --export mysql`

---

## 10. Fases para Claude Code (ir una por una)

Cada fase termina con sus tests en verde antes de pasar a la siguiente.

| Fase | Tarea | Listo cuando |
|---|---|---|
| **1** | Estructura, `requirements.txt`, `.env.example`, `settings.py`, `models.py`, `.gitignore` | `pip install -r requirements.txt` funciona y la configuración carga |
| **2** | `collectors/donki.py` + `scripts/fetch_donki.py` + caché + fallback | `python scripts/fetch_donki.py --days 30` imprime los eventos reales y guarda la caché |
| **3** | `collectors/osdr.py`: inspeccionar los archivos de `data/osdr/raw/` e imprimir columnas, analitos y puntos de tiempo | Hay un resumen real de los datos (sin columnas inventadas) |
| **4** | `calibration/osdr_calibration.py` + `build_calibration.py` | Existe `calibration.json` con valores reales |
| **5** | Archivos de `config/*.yaml` + `engine/` (evaluate, score, trends, recommend) | Pasan los tests de los umbrales |
| **6** | `generator/` completo + `generate_mission.py` | Genera 30 días × 4 astronautas, reproducible con `seed` |
| **7** | `api/main.py` con todos los endpoints | `/docs` funciona y todos los endpoints responden |
| **8** | `scheduler.py` (refresca DONKI cada 30 min) + exportador MySQL | La app ve el clima espacial actualizado |
| **9** | `README.md` del simulador: instalación, fuentes NASA, arquitectura y ejemplos con curl | Alguien nuevo lo corre en 5 minutos |

### Prompts sugeridos para Claude Code

- **Fase 1:** `Lee simulador/SIMULADOR.md y haz la Fase 1. Solo dentro de simulador/.`
- **Fase 2:** `Haz la Fase 2 del SIMULADOR.md. Usa la NASA_API_KEY del .env. Prueba con los últimos 30 días y muéstrame qué eventos trajo.`
- **Fase 3:** `Haz la Fase 3. Ya puse los archivos de OSD-569 (y los demás) en simulador/data/osdr/raw/. Inspecciónalos y muéstrame columnas, analitos y puntos de tiempo antes de seguir.`
- **Fases 4 a 9:** `Sigue con la Fase N del SIMULADOR.md.`

---

## 11. Reglas finales

- No modificar nada fuera de `simulador/`.
- No inventar datos de la NASA: si un archivo o un campo no existe, avisar y detenerse.
- Todo dato lleva `source`.
- La API key nunca se imprime en los logs ni se sube al repositorio.
- Aviso en el README y en `/health`: *"Herramienta de apoyo para la demo. No reemplaza el criterio médico."*

---

## 12. Fuentes

- NASA DONKI: https://ccmc.gsfc.nasa.gov/tools/DONKI · https://api.nasa.gov
- NASA OSDR: https://osdr.nasa.gov
- Inspiration4 en OSDR (guía de acceso): https://www.ncbi.nlm.nih.gov/pmc/articles/PMC11094041/
- NASA HRP, peligros: https://www.nasa.gov/hrp/hazards/
- NASA HRP, riesgos: https://www.nasa.gov/hrp/risks/
