"""Puntaje de salud de 0 a 100 y estado por órgano."""

from collections.abc import Iterable, Mapping

from simulador.config import body_parts_config, scoring_config
from simulador.engine.evaluate import status_of
from simulador.models import (
    AssessResult,
    BodyPart,
    Environment,
    Indicator,
    Measurement,
    Status,
)

_SEVERITY = {Status.GREEN: 0, Status.YELLOW: 1, Status.RED: 2}
_STATUS_ES = {Status.GREEN: "verde", Status.YELLOW: "amarillo", Status.RED: "rojo"}


def worst(statuses: Iterable[Status]) -> Status:
    """El peor estado de una lista (verde si está vacía)."""
    return max(statuses, key=_SEVERITY.__getitem__, default=Status.GREEN)


def score(statuses: Mapping[Indicator, Status] | Iterable[tuple[Indicator, Status]]) -> int:
    """Promedio ponderado: verde=100, amarillo=60, rojo=20; hr, bp, spo2 y radiación ×1,5."""
    cfg = scoring_config()
    points, weights = cfg["points"], cfg["weights"]
    items = statuses.items() if isinstance(statuses, Mapping) else statuses

    total = weight_sum = 0.0
    for indicator, status in items:
        weight = float(weights.get(indicator.value, 1))
        total += points[status.value] * weight
        weight_sum += weight
    return round(total / weight_sum) if weight_sum else 100


def body_status(statuses: Mapping[Indicator, Status]) -> dict[BodyPart, Status]:
    """Estado de cada órgano: el peor de sus indicadores (solo los que tienen datos)."""
    result: dict[BodyPart, Status] = {}
    for part, cfg in body_parts_config().items():
        found = [statuses[Indicator(i)] for i in cfg["indicators"] if Indicator(i) in statuses]
        if found:
            result[BodyPart(part)] = worst(found)
    return result


def latest_statuses(
    measurements: Iterable[Measurement], environment: Environment
) -> dict[Indicator, Status]:
    """Peor estado de la última medición de cada indicador (entre todos los astronautas)."""
    latest: dict[tuple[int, Indicator], Measurement] = {}
    for m in measurements:
        key = (m.astronaut_id, m.indicator)
        if key not in latest or m.measured_at >= latest[key].measured_at:
            latest[key] = m

    result: dict[Indicator, Status] = {}
    for (_, indicator), m in latest.items():
        status = m.status or status_of(indicator, m.value, environment)
        result[indicator] = worst([result.get(indicator, Status.GREEN), status])
    return result


def assess(measurements: list[Measurement], environment: Environment) -> AssessResult:
    """Puntaje global, estado por órgano y un resumen en una frase."""
    statuses = latest_statuses(measurements, environment)
    value = score(statuses)
    parts = body_status(statuses)
    labels = body_parts_config()

    problems = [
        f"{labels[p.value]['label'].lower()} ({_STATUS_ES[s]})"
        for p, s in sorted(parts.items(), key=lambda kv: -_SEVERITY[kv[1]])
        if s != Status.GREEN
    ]
    summary = f"Puntaje {value}/100. " + (
        f"Atención: {', '.join(problems)}." if problems else "Todos los sistemas en verde."
    )
    return AssessResult(score=value, body_status=parts, summary=summary)
