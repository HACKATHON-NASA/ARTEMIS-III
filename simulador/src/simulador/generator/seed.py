"""Orquestador: genera una misión completa (SIMULADOR.md §6).

1. Clima espacial real (DONKI) + SEP de demo si hace falta.
2. Valores crudos de cada astronauta, calibrados con OSDR.

No evalúa nada (ni semáforo ni alertas): eso lo hace el sistema principal.
"""

import asyncio
from datetime import date, datetime, timedelta, timezone


from simulador.calibration.osdr_calibration import CALIBRATION_FILE, load_calibration
from simulador.generator.crew import load_crew
from simulador.generator.events import inject_sep
from simulador.generator.radiation import mission_space_weather
from simulador.generator.vitals import GlucoseModel, generate_astronaut
from simulador.models import (
    Measurement,
    MissionInfo,
    MissionMeta,
    MissionRequest,
    MissionResult,
    SpaceWeatherDay,
)


def _load_calibration_safe() -> tuple[dict | None, str | None]:
    try:
        return load_calibration(), CALIBRATION_FILE.name
    except FileNotFoundError:
        return None, None


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

    return MissionResult(
        mission=MissionInfo(
            name=request.mission_name, environment=request.environment,
            days=request.days, start_date=start,
        ),
        crew=crew,
        space_weather=space_weather,
        measurements=measurements,
        meta=MissionMeta(
            seed=request.seed,
            generated_at=datetime.now(timezone.utc),
            calibration_file=calibration_file,
        ),
    )


def generate_mission_sync(request: MissionRequest, **kwargs) -> MissionResult:
    """Versión sin `await` para scripts."""
    return asyncio.run(generate_mission(request, **kwargs))
