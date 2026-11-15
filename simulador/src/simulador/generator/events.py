"""Eventos de deterioro para que la demo tenga historia (SIMULADOR.md §6).

- SEP simulado el día 12 si DONKI no trae ninguno en el rango (`simulated_event`).
- Astronauta 2, días 18–21: el sueño cae de 5,8 a 5,0 h.
- Astronauta 3, día 24: presión 148/94 (el sistema principal decide si es alerta).

Los días se cuentan desde 1. Si la misión es más corta, el evento no ocurre.
"""

from typing import Any

from simulador.config import load_yaml
from simulador.models import RiskLevel, Source, SpaceWeatherDay


def demo_config() -> dict[str, Any]:
    """Parámetros de los eventos de demo (`generator.yaml`)."""
    return load_yaml("generator")["demo_events"]


def inject_sep(space_weather: list[SpaceWeatherDay]) -> list[SpaceWeatherDay]:
    """Inyecta un SEP simulado si no hay ninguno real. Devuelve una lista nueva."""
    sep_day = demo_config()["sep_day"]
    if any(d.sep_event for d in space_weather) or len(space_weather) < sep_day:
        return space_weather

    result = list(space_weather)
    day = result[sep_day - 1]
    result[sep_day - 1] = day.model_copy(
        update={
            "sep_event": True,
            "risk_level": RiskLevel.HIGH,
            "radiation_multiplier": 3.0,
            "events": [*day.events, "SIMULATED-SEP-001"],
            "source": Source.SIMULATED_EVENT,
        }
    )
    return result


def sleep_override(astronaut_id: int, day_number: int) -> float | None:
    """Horas de sueño forzadas por la demo para ese astronauta y día, si las hay."""
    cfg = demo_config()["sleep_decline"]
    if astronaut_id != cfg["astronaut_id"]:
        return None
    return cfg["days"].get(day_number)


def bp_override(astronaut_id: int, day_number: int) -> tuple[float, float] | None:
    """Presión forzada por la demo `(sistólica, diastólica)`, si la hay."""
    cfg = demo_config()["blood_pressure_spike"]
    if astronaut_id == cfg["astronaut_id"] and day_number == cfg["day"]:
        return cfg["bp_sys"], cfg["bp_dia"]
    return None
