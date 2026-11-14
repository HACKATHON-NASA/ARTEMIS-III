"""Signos vitales y demás indicadores de un astronauta, día por día.

Cada astronauta usa su propio generador aleatorio (semilla + id), así que el
resultado es reproducible y no cambia si se agregan o quitan compañeros.
"""

import math
from dataclasses import dataclass
from datetime import date, datetime, time, timedelta, timezone
from typing import Any

import numpy as np

from simulador.config import environment_config, load_yaml
from simulador.engine.evaluate import status_of
from simulador.generator import events
from simulador.generator.radiation import daily_dose
from simulador.models import (
    AnalyteCalibration,
    CrewMember,
    Environment,
    Indicator,
    Measurement,
    Source,
    SpaceWeatherDay,
)


@dataclass(frozen=True)
class GlucoseModel:
    """Parámetros de la glucosa: calibrados con OSDR o de referencia."""

    mean: float
    sd: float
    delta_pct: float
    source: Source

    @classmethod
    def from_calibration(cls, calibration: dict[str, AnalyteCalibration] | None) -> "GlucoseModel":
        """Usa `calibration.json` si existe; si no, la referencia de `generator.yaml`."""
        cfg = load_yaml("generator")["glucose"]
        glucose = (calibration or {}).get("glucose")
        if glucose is None:
            return cls(
                cfg["fallback_mean"], cfg["fallback_sd"], cfg["fallback_delta_pct"],
                Source.SIMULATED_REFERENCE,
            )
        return cls(
            glucose.baseline_mean, glucose.baseline_sd, glucose.delta_pct,
            Source.SIMULATED_CALIBRATED_OSDR,
        )


def generate_astronaut(
    member: CrewMember,
    start: date,
    space_weather: list[SpaceWeatherDay],
    environment: Environment,
    glucose: GlucoseModel,
    seed: int,
    inject_demo_events: bool,
) -> list[Measurement]:
    """Todas las mediciones de un astronauta durante la misión."""
    cfg = load_yaml("generator")
    noise, schedule, units = cfg["noise_sd"], cfg["schedule"], cfg["units"]
    micro, sleep_cfg, mood_cfg = cfg["microgravity"], cfg["sleep"], cfg["mood"]
    base: dict[str, Any] = member.baseline

    rng = np.random.default_rng([seed, member.id])
    zero_g = 1.0 - float(environment_config(environment)["gravity_g"])
    days = len(space_weather)
    glucose_mean = glucose.mean + base.get("glucose_z", 0.0) * glucose.sd
    glucose_noise = glucose.sd * cfg["glucose"]["within_person_fraction"]

    out: list[Measurement] = []

    def add(indicator: Indicator, value: float, day: date, hour: int, source: Source,
            sep_active: bool = False) -> None:
        out.append(
            Measurement(
                astronaut_id=member.id,
                indicator=indicator,
                value=value,
                unit=units[indicator.value],
                measured_at=datetime.combine(day, time(hour), tzinfo=timezone.utc),
                status=status_of(indicator, value, environment, sep_active),
                source=source,
            )
        )

    ref = Source.SIMULATED_REFERENCE
    for index, weather in enumerate(space_weather):
        day_number = index + 1
        day = weather.date
        sep_today = weather.sep_event
        sep_yesterday = index > 0 and space_weather[index - 1].sep_event

        # Sueño de la noche anterior: baja hacia ~6,5 h y empeora tras un SEP.
        drift = 1 - math.exp(-index / sleep_cfg["tau_days"])
        sleep = base["sleep_h"] - (base["sleep_h"] - sleep_cfg["target_h"]) * drift
        sleep += rng.normal(0, noise["sleep"])
        if sep_yesterday:
            sleep -= sleep_cfg["after_sep_drop_h"]
        if inject_demo_events and (forced := events.sleep_override(member.id, day_number)):
            sleep = forced
        sleep = round(max(sleep, 3.0), 1)

        # Ejercicio: en el refugio por un SEP se entrena menos.
        exercise = rng.normal(base["exercise_min"], noise["exercise_min"])
        if sep_today:
            exercise -= cfg["exercise"]["sep_day_reduction_min"]
        exercise = round(max(exercise, 0))

        # Ánimo: baja con el aislamiento; sube con buen sueño y ejercicio.
        mood = base["mood"] - mood_cfg["isolation_drop_per_day"] * index
        mood += mood_cfg["good_sleep_bonus"] if sleep >= 7 else 0
        mood -= mood_cfg["bad_sleep_penalty"] if sleep < 6 else 0
        mood += mood_cfg["exercise_bonus"] if exercise >= 90 else 0
        mood = int(np.clip(round(mood + rng.normal(0, noise["mood"])), 1, 5))

        # Pulso: baja en las primeras semanas de microgravedad; más alto de noche.
        hr_drop = micro["hr_drop_bpm"] * zero_g * (1 - math.exp(-index / micro["hr_drop_tau_days"]))
        hr_values = [
            round(base["hr_rest"] - hr_drop + rng.normal(0, noise["hr"])
                  + (micro["evening_hr_increase"] if hour >= 12 else 0))
            for hour in schedule["hr"]
        ]

        # Presión: más variable en microgravedad.
        bp_factor = 1 + (micro["bp_variability_factor"] - 1) * zero_g
        bp_sys = round(rng.normal(base["bp_sys"], noise["bp_sys"] * bp_factor))
        bp_dia = round(rng.normal(base["bp_dia"], noise["bp_dia"] * bp_factor))
        if inject_demo_events and (forced_bp := events.bp_override(member.id, day_number)):
            bp_sys, bp_dia = forced_bp

        # Glucosa: deriva progresiva hacia el cambio real de Inspiration4.
        progress = index / (days - 1) if days > 1 else 0.0
        glucose_value = round(
            glucose_mean * (1 + glucose.delta_pct / 100 * progress) + rng.normal(0, glucose_noise)
        )

        temp = round(rng.normal(base["temp"], noise["temp"]), 1)
        spo2_values = [
            round(min(rng.normal(base["spo2"], noise["spo2"]), 100.0), 1) for _ in schedule["spo2"]
        ]
        dose = daily_dose(environment, weather.radiation_multiplier, rng)
        dose_source = Source.NASA_DONKI if weather.source == Source.NASA_DONKI else weather.source

        for hour, value in zip(schedule["hr"], hr_values, strict=True):
            add(Indicator.HR, value, day, hour, ref)
        for hour, value in zip(schedule["spo2"], spo2_values, strict=True):
            add(Indicator.SPO2, value, day, hour, ref)
        add(Indicator.BP_SYS, bp_sys, day, schedule["bp_sys"][0], ref)
        add(Indicator.BP_DIA, bp_dia, day, schedule["bp_dia"][0], ref)
        add(Indicator.GLUCOSE, glucose_value, day, schedule["glucose"][0], glucose.source)
        add(Indicator.TEMP, temp, day, schedule["temp"][0], ref)
        add(Indicator.SLEEP, sleep, day, schedule["sleep"][0], ref)
        add(Indicator.MOOD, mood, day, schedule["mood"][0], ref)
        add(Indicator.EXERCISE_MIN, exercise, day, schedule["exercise_min"][0], ref)
        add(Indicator.RADIATION, dose, day, schedule["radiation"][0], dose_source, sep_today)

    out.sort(key=lambda m: m.measured_at)
    return out


def mission_dates(start: date, days: int) -> list[date]:
    """Fechas de la misión."""
    return [start + timedelta(days=i) for i in range(days)]
