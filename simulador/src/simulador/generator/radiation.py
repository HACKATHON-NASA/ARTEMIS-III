"""Clima espacial de la misión (DONKI real) y dosis diaria de radiación."""

import logging
from datetime import date, timedelta

import numpy as np

from simulador.collectors.donki import DonkiUnavailableError, daily_space_weather, get_donki_client
from simulador.config import environment_config, load_yaml
from simulador.models import Environment, RiskLevel, Source, SpaceWeatherDay

logger = logging.getLogger(__name__)


def _quiet_day(day: date) -> SpaceWeatherDay:
    """Día sin datos de DONKI (futuro o sin conexión): riesgo bajo, marcado como simulado."""
    return SpaceWeatherDay(
        date=day, risk_level=RiskLevel.LOW, radiation_multiplier=1.0,
        source=Source.SIMULATED_REFERENCE,
    )


async def mission_space_weather(
    start: date, days: int, use_real: bool, today: date | None = None
) -> list[SpaceWeatherDay]:
    """Un `SpaceWeatherDay` por día de misión.

    Los días hasta hoy salen de DONKI real (`nasa_donki`); los días futuros o
    sin conexión quedan tranquilos y marcados `simulated_reference`.
    """
    today = today or date.today()
    end = start + timedelta(days=days - 1)
    by_day = {start + timedelta(days=i): _quiet_day(start + timedelta(days=i)) for i in range(days)}

    real_end = min(end, today)
    if use_real and start <= real_end:
        try:
            result = await get_donki_client().fetch_all(start, real_end)
            for day in daily_space_weather(result.events, start, real_end, result.stale):
                by_day[day.date] = day
        except DonkiUnavailableError as exc:
            logger.warning("DONKI no disponible (%s): clima espacial simulado", exc)

    return [by_day[d] for d in sorted(by_day)]


def daily_dose(
    environment: Environment, multiplier: float, rng: np.random.Generator
) -> float:
    """Dosis del día = base del entorno × multiplicador DONKI × blindaje del hábitat."""
    cfg = load_yaml("generator")["radiation"]
    base = environment_config(environment)["base_dose_msv_day"]
    shielding = min(rng.normal(cfg["shielding_mean"], cfg["shielding_sd"]), cfg["shielding_max"])
    return round(base * multiplier * shielding, 3)
