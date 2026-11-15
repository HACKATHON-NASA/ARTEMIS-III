"""Modelos Pydantic: los contratos de datos del simulador y de su API.

Todo dato lleva su `source` (regla de honestidad para los jueces).
"""

from datetime import date, datetime
from enum import StrEnum
from typing import Generic, TypeVar

from pydantic import BaseModel, Field


# ---------------------------------------------------------------------------
# Enumeraciones
# ---------------------------------------------------------------------------


class Source(StrEnum):
    """Origen de cada dato."""

    NASA_DONKI = "nasa_donki"
    NASA_OSDR = "nasa_osdr"
    SIMULATED_CALIBRATED_OSDR = "simulated_calibrated_osdr"
    SIMULATED_REFERENCE = "simulated_reference"
    SIMULATED_EVENT = "simulated_event"


class RiskLevel(StrEnum):
    """Nivel de riesgo de radiación de un día."""

    LOW = "low"
    MEDIUM = "medium"
    HIGH = "high"


class Environment(StrEnum):
    """Entorno de la misión."""

    ISS = "iss"
    TRANSIT = "transit"
    MOON = "moon"
    MARS = "mars"


class Indicator(StrEnum):
    """Indicadores que miden los aparatos simulados (el ánimo lo registra el astronauta en la app)."""

    HR = "hr"
    BP_SYS = "bp_sys"
    BP_DIA = "bp_dia"
    GLUCOSE = "glucose"
    TEMP = "temp"
    SPO2 = "spo2"
    SLEEP = "sleep"
    RADIATION = "radiation"
    EXERCISE_MIN = "exercise_min"


# ---------------------------------------------------------------------------
# DONKI
# ---------------------------------------------------------------------------


class SpaceWeatherDay(BaseModel):
    """Clima espacial de un día y su efecto en la radiación."""

    date: date
    flare_max_class: str | None = None
    sep_event: bool = False
    geomagnetic_kp_max: float | None = None
    risk_level: RiskLevel = RiskLevel.LOW
    radiation_multiplier: float = Field(default=1.0, ge=1.0)
    events: list[str] = Field(default_factory=list)
    source: Source = Source.NASA_DONKI
    stale: bool = False


# ---------------------------------------------------------------------------
# OSDR
# ---------------------------------------------------------------------------


class AnalyteCalibration(BaseModel):
    """Calibración de un analito a partir de los datos de Inspiration4."""

    unit: str
    baseline_mean: float
    baseline_sd: float = Field(ge=0)
    postflight_mean: float
    delta_pct: float
    n_subjects: int = Field(ge=1)
    source: Source = Source.NASA_OSDR
    datasets: list[str] = Field(default_factory=list)
    analyte_name: str | None = None
    """Nombre original en OSDR (p. ej. `PLATELET COUNT`)."""
    n_baseline_samples: int | None = None
    postflight_timepoint: str | None = None
    """Punto de tiempo usado como primer post-vuelo (p. ej. `R+1`)."""


# ---------------------------------------------------------------------------
# Misión
# ---------------------------------------------------------------------------


class MissionRequest(BaseModel):
    """Parámetros para generar una misión (`POST /simulate`)."""

    mission_name: str = Field(default="Ares-1", min_length=1, max_length=80)
    environment: Environment = Environment.MARS
    days: int = Field(default=30, ge=1, le=365)
    start_date: date | None = None
    crew_size: int = Field(default=4, ge=1, le=4)
    seed: int = 42
    use_real_space_weather: bool = True
    inject_demo_events: bool = True


class CrewMember(BaseModel):
    """Astronauta ficticio con su línea base."""

    id: int
    name: str
    role: str
    age: int | None = None
    baseline: dict[str, float] = Field(default_factory=dict)


class Measurement(BaseModel):
    """Valor crudo de un aparato: sin semáforo (lo evalúa el sistema principal)."""

    astronaut_id: int
    indicator: Indicator
    value: float
    unit: str
    measured_at: datetime
    source: Source


class MissionInfo(BaseModel):
    """Datos generales de la misión."""

    name: str
    environment: Environment
    days: int
    start_date: date | None = None


class MissionMeta(BaseModel):
    """Metadatos de la generación (para reproducirla)."""

    seed: int
    generated_at: datetime
    calibration_file: str | None = None


class MissionResult(BaseModel):
    """Salida completa de una misión generada."""

    mission: MissionInfo
    crew: list[CrewMember]
    space_weather: list[SpaceWeatherDay]
    measurements: list[Measurement]
    meta: MissionMeta


# ---------------------------------------------------------------------------
# Sobre de la API
# ---------------------------------------------------------------------------

T = TypeVar("T")


class ApiResponse(BaseModel, Generic[T]):
    """Respuesta correcta: `{"data": ..., "message": "ok"}`."""

    data: T
    message: str = "ok"


class ApiError(BaseModel):
    """Respuesta de error: `{"data": null, "message": "...", "error_code": "..."}`."""

    data: None = None
    message: str
    error_code: str
