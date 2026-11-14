"""Recomendación por indicador y estado; si es rojo, aviso a Tierra con su retraso."""

from simulador.config import environment_config, indicator_config, recommendations_config
from simulador.models import Environment, Indicator, Recommendation, Status


def recommend(
    indicator: Indicator,
    status: Status,
    environment: Environment = Environment.MARS,
    value: float | None = None,
) -> Recommendation:
    """Texto de `recommendations.yaml`; en rojo, `notify_ground` y el retraso del entorno.

    Con `value` por debajo del rango verde se usa el texto `*_low` si existe
    (p. ej. presión baja en vez de alta).
    """
    cfg = recommendations_config()
    texts = cfg["recommendations"].get(indicator.value) or cfg["default"]
    text = texts.get(status.value) or cfg["default"][status.value]
    green_min = (indicator_config(indicator, environment).get("green") or {}).get("min")
    if value is not None and green_min is not None and value < green_min:
        text = texts.get(f"{status.value}_low", text)

    notify = status == Status.RED
    delay = float(environment_config(environment)["comm_delay_seconds"]) if notify else 0.0
    return Recommendation(
        indicator=indicator,
        status=status,
        recommendation=text,
        notify_ground=notify,
        ground_delay_seconds=delay,
    )
