"""Semáforo de una medición (verde, amarillo o rojo) según `config/thresholds.yaml`."""

from typing import Any

from simulador.config import environment_config, indicator_config
from simulador.models import Environment, EvaluateResult, Indicator, Status

_STATUS_TEXT = {
    Status.GREEN: "dentro de lo normal",
    Status.YELLOW: "a vigilar",
    Status.RED: "fuera de rango",
}


def _inside(value: float, band: dict[str, Any] | None) -> bool:
    """`True` si el valor cumple los límites de la banda (`min` >=, `max` <=, `lt` <)."""
    if band is None:
        return False
    if "min" in band and value < band["min"]:
        return False
    if "max" in band and value > band["max"]:
        return False
    if "lt" in band and value >= band["lt"]:
        return False
    return True


def _format(value: float) -> str:
    return f"{value:.2f}".rstrip("0").rstrip(".") if value % 1 else f"{value:.0f}"


def status_of(
    indicator: Indicator,
    value: float,
    environment: Environment = Environment.MARS,
    sep_active: bool = False,
) -> Status:
    """Estado del semáforo (sin mensaje): lo usa el generador miles de veces."""
    cfg = indicator_config(indicator, environment)
    if cfg.get("relative_to_environment"):
        if sep_active:
            return Status.RED
        base = environment_config(environment)["base_dose_msv_day"]
        if value <= base * cfg["green_max_factor"]:
            return Status.GREEN
        if value <= base * cfg["yellow_max_factor"]:
            return Status.YELLOW
        return Status.RED

    if _inside(value, cfg.get("green")):
        return Status.GREEN
    if _inside(value, cfg.get("yellow")):
        return Status.YELLOW
    return Status.RED


def evaluate(
    indicator: Indicator,
    value: float,
    environment: Environment = Environment.MARS,
    sep_active: bool = False,
) -> EvaluateResult:
    """Evalúa una medición y devuelve `{status, message}`."""
    status = status_of(indicator, value, environment, sep_active)
    cfg = indicator_config(indicator, environment)
    message = f"{cfg['label']} {_format(value)} {cfg['unit']}: {_STATUS_TEXT[status]}"
    if cfg.get("relative_to_environment"):
        base = environment_config(environment)["base_dose_msv_day"]
        message += f" (base del entorno {_format(base)} mSv/día)"
        if sep_active:
            message += " · evento SEP activo"
    return EvaluateResult(status=status, message=message)
