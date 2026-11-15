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


class Status(StrEnum):
    """Semáforo de una medición."""

    GREEN = "green"
    YELLOW = "yellow"
    RED = "red"


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
    """Indicadores que genera y evalúa el simulador."""

    HR = "hr"
    BP_SYS = "bp_sys"
    BP_DIA = "bp_dia"
    GLUCOSE = "glucose"
    TEMP = "temp"
    SPO2 = "spo2"
    SLEEP = "sleep"
    MOOD = "mood"
    RADIATION = "radiation"
    EXERCISE_MIN = "exercise_min"


class TrendDirection(StrEnum):
    """Dirección de la tendencia de un indicador."""

    UP = "up"
    DOWN = "down"
    STABLE = "stable"


class BodyPart(StrEnum):
    """Órganos o sistemas a los que se asignan los indicadores."""

    HEART = "heart"
    LUNGS = "lungs"
    BRAIN = "brain"
    IMMUNE = "immune"
    METABOLISM = "metabolism"
    BONES = "bones"
    WHOLE_BODY = "whole_body"


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
    """Una medición de un indicador de un astronauta."""

    astronaut_id: int
    indicator: Indicator
    value: float
    unit: str
    measured_at: datetime
    status: Status | None = None
    source: Source


class Alert(BaseModel):
    """Alerta generada por una medición en rojo o una tendencia negativa."""

    astronaut_id: int
    indicator: Indicator
    status: Status
    message: str
    measured_at: datetime
    notify_ground: bool = False
    ground_delay_seconds: float = 0
    trend_alert: bool = False
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
    alerts: list[Alert] = Field(default_factory=list)
    meta: MissionMeta


# ---------------------------------------------------------------------------
# Motor de evaluación
# ---------------------------------------------------------------------------


class EvaluateRequest(BaseModel):
    """Entrada de `POST /evaluate`."""

    indicator: Indicator
    value: float
    environment: Environment = Environment.MARS
    sep_active: bool = False
    """Solo para radiación: un evento SEP activo pone el semáforo en rojo."""


class EvaluateResult(BaseModel):
    """Resultado del semáforo de una medición."""

    status: Status
    message: str


class AssessRequest(BaseModel):
    """Entrada de `POST /assess`."""

    measurements: list[Measurement] = Field(min_length=1)
    environment: Environment = Environment.MARS


class AssessResult(BaseModel):
    """Puntaje global y estado por órgano."""

    score: int = Field(ge=0, le=100)
    body_status: dict[BodyPart, Status]
    summary: str


class TrendRequest(BaseModel):
    """Entrada de `POST /trends`: serie de un indicador, de la más antigua a la más reciente."""

    indicator: Indicator
    values: list[float] = Field(min_length=2)
    environment: Environment = Environment.MARS


class TrendResult(BaseModel):
    """Resultado del análisis de tendencia."""

    direction: TrendDirection
    trend_alert: bool


class Recommendation(BaseModel):
    """Recomendación para un indicador y un estado."""

    indicator: Indicator
    status: Status
    recommendation: str
    notify_ground: bool = False
    ground_delay_seconds: float = 0


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
