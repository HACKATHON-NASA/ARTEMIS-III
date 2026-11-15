"""Tendencia de un indicador: regresión lineal sobre las últimas mediciones.

- `direction`: `up`, `down` o `stable` (si el cambio total en la ventana es
  menor que `stable_delta` del indicador).
- `trend_alert`: el indicador empeoró N veces seguidas (por defecto 3), aunque
  siga en verde, si el empeoramiento total supera `alert_delta` (≈ 2× el ruido
  normal) y la recta de toda la ventana empeora también al menos `alert_delta`.
  "Empeorar" depende del indicador (`worse` en thresholds.yaml).
"""

from collections.abc import Sequence

import numpy as np

from simulador.config import indicator_config, trends_config
from simulador.models import Environment, Indicator, TrendDirection, TrendResult


def _badness(values: np.ndarray, worse: str, green: dict[str, float] | None) -> np.ndarray:
    """Transforma la serie para que "más alto" siempre signifique "peor"."""
    if worse == "up":
        return values
    if worse == "down":
        return -values
    # away: distancia al centro de la banda verde
    green = green or {}
    low = green.get("min", float(values.min()))
    high = green.get("max", green.get("lt", float(values.max())))
    return np.abs(values - (low + high) / 2)


def trend(
    indicator: Indicator,
    values: Sequence[float],
    environment: Environment = Environment.MARS,
) -> TrendResult:
    """Calcula la dirección y si hay alerta de tendencia (serie de antigua a reciente)."""
    cfg = indicator_config(indicator, environment)
    rules = trends_config()
    series = np.asarray(values[-rules["window"] :], dtype=float)

    if len(series) < 2:
        return TrendResult(direction=TrendDirection.STABLE, trend_alert=False)

    slope = float(np.polyfit(np.arange(len(series)), series, 1)[0])
    change = slope * (len(series) - 1)
    if abs(change) < cfg.get("stable_delta", 0):
        direction = TrendDirection.STABLE
    else:
        direction = TrendDirection.UP if slope > 0 else TrendDirection.DOWN

    steps = rules["consecutive_worsening"]
    badness = _badness(series, cfg.get("worse", "up"), cfg.get("green"))
    recent = np.diff(badness[-(steps + 1) :])
    # Empeora N veces seguidas, el empeoramiento total supera el ruido normal
    # (alert_delta) y la recta de toda la ventana también empeora al menos eso.
    badness_slope = float(np.polyfit(np.arange(len(badness)), badness, 1)[0])
    trend_alert = (
        len(recent) == steps
        and bool(np.all(recent > 0))
        and float(recent.sum()) >= cfg.get("alert_delta", cfg.get("stable_delta", 0))
        and badness_slope * (len(badness) - 1) >= cfg.get("alert_delta", 0)
    )
    return TrendResult(direction=direction, trend_alert=trend_alert)
