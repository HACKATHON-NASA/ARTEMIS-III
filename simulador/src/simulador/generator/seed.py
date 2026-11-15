"""Orquestador: genera una misión completa (SIMULADOR.md §6).

1. Clima espacial real (DONKI) + SEP de demo si hace falta.
2. Mediciones de cada astronauta, calibradas con OSDR.
3. Alertas: cada medición en rojo y cada tendencia que empeora.
"""

import asyncio
from collections import defaultdict
from datetime import date, datetime, timedelta, timezone

import numpy as np

from simulador.calibration.osdr_calibration import CALIBRATION_FILE, load_calibration
from simulador.config import environment_config, indicator_config
from simulador.engine.evaluate import evaluate
from simulador.engine.recommend import recommend
from simulador.engine.trends import trend
from simulador.generator.crew import load_crew
from simulador.generator.events import inject_sep
from simulador.generator.radiation import mission_space_weather
from simulador.generator.vitals import GlucoseModel, generate_astronaut
from simulador.models import (
    Alert,
    Environment,
    Indicator,
    Measurement,
    MissionInfo,
    MissionMeta,
    MissionRequest,
    MissionResult,
    SpaceWeatherDay,
    Status,
)


def _load_calibration_safe() -> tuple[dict | None, str | None]:
    try:
        return load_calibration(), CALIBRATION_FILE.name
    except FileNotFoundError:
        return None, None


def build_alerts(
    measurements: list[Measurement], environment: Environment, sep_days: set[date]
) -> list[Alert]:
    """Alertas de la misión: rojos (aviso a Tierra) y tendencias que empeoran."""
    alerts: list[Alert] = []
    delay = float(environment_config(environment)["comm_delay_seconds"])

    for m in measurements:
        if m.status != Status.RED:
            continue
        sep = m.indicator == Indicator.RADIATION and m.measured_at.date() in sep_days
        text = evaluate(m.indicator, m.value, environment, sep).message
        advice = recommend(m.indicator, Status.RED, environment, m.value).recommendation
        alerts.append(
            Alert(
                astronaut_id=m.astronaut_id, indicator=m.indicator, status=Status.RED,
                message=f"{text}. {advice}", measured_at=m.measured_at,
                notify_ground=True, ground_delay_seconds=delay, source=m.source,
            )
        )

    # Tendencias sobre el valor medio de cada día (una alerta al empezar a empeorar).
    daily: dict[tuple[int, Indicator], dict[date, list[Measurement]]] = defaultdict(
        lambda: defaultdict(list)
    )
    for m in measurements:
        daily[(m.astronaut_id, m.indicator)][m.measured_at.date()].append(m)

    for (astronaut_id, indicator), by_day in daily.items():
        if not indicator_config(indicator, environment).get("trend_alerts", True):
            continue  # p. ej. radiación: depende del sol, no del cuerpo
        days = sorted(by_day)
        means = [float(np.mean([m.value for m in by_day[d]])) for d in days]
        active = False
        for i in range(3, len(days)):
            alert_now = trend(indicator, means[: i + 1], environment).trend_alert
            last = by_day[days[i]][-1]
            if alert_now and not active:
                label = indicator_config(indicator, environment)["label"]
                alerts.append(
                    Alert(
                        astronaut_id=astronaut_id, indicator=indicator, status=last.status,
                        message=f"Tendencia: {label.lower()} empeora 3 días seguidos "
                                f"(último valor {last.value:g} {last.unit}).",
                        measured_at=last.measured_at, trend_alert=True, source=last.source,
                    )
                )
            active = alert_now

    alerts.sort(key=lambda a: (a.measured_at, a.astronaut_id))
    return alerts


async def generate_mission(
    request: MissionRequest,
    space_weather: list[SpaceWeatherDay] | None = None,
    today: date | None = None,
) -> MissionResult:
    """Genera la misión. `space_weather` permite pasar el clima ya calculado (tests)."""
    today = today or date.today()
    start = request.start_date or today - timedelta(days=request.days - 1)

    if space_weather is None:
        space_weather = await mission_space_weather(
            start, request.days, request.use_real_space_weather, today
        )
    if request.inject_demo_events:
        space_weather = inject_sep(space_weather)

    crew = load_crew(request.crew_size)
    calibration, calibration_file = _load_calibration_safe()
    glucose = GlucoseModel.from_calibration(calibration)

    measurements: list[Measurement] = []
    for member in crew:
        measurements.extend(
            generate_astronaut(
                member, start, space_weather, request.environment, glucose,
                request.seed, request.inject_demo_events,
            )
        )
    measurements.sort(key=lambda m: (m.measured_at, m.astronaut_id))

    sep_days = {d.date for d in space_weather if d.sep_event}
    return MissionResult(
        mission=MissionInfo(
            name=request.mission_name, environment=request.environment,
            days=request.days, start_date=start,
        ),
        crew=crew,
        space_weather=space_weather,
        measurements=measurements,
        alerts=build_alerts(measurements, request.environment, sep_days),
        meta=MissionMeta(
            seed=request.seed,
            generated_at=datetime.now(timezone.utc),
            calibration_file=calibration_file,
        ),
    )


def generate_mission_sync(request: MissionRequest, **kwargs) -> MissionResult:
    """Versión sin `await` para scripts."""
    return asyncio.run(generate_mission(request, **kwargs))
