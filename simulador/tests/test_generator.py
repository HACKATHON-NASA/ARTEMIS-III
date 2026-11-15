"""Tests de la Fase 6: generador de misión (sin internet)."""

import json
from datetime import date, timedelta
from pathlib import Path

import pytest

from simulador.engine import recommend
from simulador.exporters.json_csv import export_csv, export_json
from simulador.generator.radiation import mission_space_weather
from simulador.generator.seed import generate_mission
from simulador.generator.vitals import GlucoseModel
from simulador.models import (
    AnalyteCalibration,
    Environment,
    Indicator,
    MissionRequest,
    MissionResult,
    RiskLevel,
    Source,
    SpaceWeatherDay,
    Status,
)

START = date(2026, 10, 1)


def quiet_weather(days: int = 30, sep_day: int | None = None) -> list[SpaceWeatherDay]:
    result = []
    for i in range(days):
        sep = sep_day == i + 1
        result.append(
            SpaceWeatherDay(
                date=START + timedelta(days=i),
                sep_event=sep,
                risk_level=RiskLevel.HIGH if sep else RiskLevel.LOW,
                radiation_multiplier=3.0 if sep else 1.0,
            )
        )
    return result


async def mission(**kwargs) -> MissionResult:
    weather = kwargs.pop("weather", None) or quiet_weather(kwargs.get("days", 30))
    request = MissionRequest(start_date=START, use_real_space_weather=False, **kwargs)
    return await generate_mission(request, space_weather=weather)


def values(result: MissionResult, astronaut: int, indicator: Indicator) -> dict[int, float]:
    """{día de misión: valor} (primera toma del día)."""
    out: dict[int, float] = {}
    for m in result.measurements:
        if m.astronaut_id == astronaut and m.indicator == indicator:
            out.setdefault((m.measured_at.date() - START).days + 1, m.value)
    return out


async def test_tamano_y_contenido() -> None:
    result = await mission(days=30, crew_size=4)
    # 12 tomas por día: hr×2, spo2×2 y 8 indicadores ×1
    assert len(result.measurements) == 30 * 4 * 12
    assert {m.indicator for m in result.measurements} == set(Indicator)
    assert all(m.status is not None for m in result.measurements)
    assert [c.id for c in result.crew] == [1, 2, 3, 4]
    assert result.mission.start_date == START


async def test_reproducible_con_la_misma_semilla() -> None:
    a = await mission(seed=42)
    b = await mission(seed=42)
    c = await mission(seed=43)
    dump = lambda r: [m.model_dump() for m in r.measurements]  # noqa: E731
    assert dump(a) == dump(b)
    assert dump(a) != dump(c)


async def test_cada_astronauta_es_independiente_del_tamano_de_la_tripulacion() -> None:
    two = await mission(crew_size=2, seed=7)
    four = await mission(crew_size=4, seed=7)
    assert values(two, 1, Indicator.HR) == values(four, 1, Indicator.HR)


async def test_eventos_de_la_demo() -> None:
    result = await mission(seed=42)

    sep_days = [i + 1 for i, d in enumerate(result.space_weather) if d.sep_event]
    assert sep_days == [12]
    assert result.space_weather[11].source == Source.SIMULATED_EVENT

    sleep = values(result, 2, Indicator.SLEEP)
    assert [sleep[d] for d in (18, 19, 20, 21)] == [5.8, 5.5, 5.2, 5.0]

    assert values(result, 3, Indicator.BP_SYS)[24] == 148
    assert values(result, 3, Indicator.BP_DIA)[24] == 94

    red = [a for a in result.alerts if not a.trend_alert]
    bp_alert = next(a for a in red if a.astronaut_id == 3 and a.indicator == Indicator.BP_SYS)
    assert bp_alert.notify_ground and bp_alert.ground_delay_seconds == 1200
    assert "148" in bp_alert.message

    radiation_day12 = [
        m for m in result.measurements
        if m.indicator == Indicator.RADIATION and (m.measured_at.date() - START).days + 1 == 12
    ]
    assert all(m.status == Status.RED for m in radiation_day12)
    assert any(a.trend_alert and a.indicator == Indicator.SLEEP and a.astronaut_id == 2
               for a in result.alerts)


async def test_sep_real_no_se_reemplaza() -> None:
    result = await mission(weather=quiet_weather(30, sep_day=5))
    assert [i + 1 for i, d in enumerate(result.space_weather) if d.sep_event] == [5]


async def test_sin_eventos_de_demo() -> None:
    result = await mission(inject_demo_events=False)
    assert not any(d.sep_event for d in result.space_weather)
    assert 148 not in values(result, 3, Indicator.BP_SYS).values()


async def test_mision_corta_no_inventa_eventos_fuera_de_rango() -> None:
    result = await mission(days=10)
    assert len(result.space_weather) == 10
    assert not any(d.sep_event for d in result.space_weather)


async def test_radiacion_normal_en_verde_y_alta_con_sep() -> None:
    result = await mission(seed=1)
    for m in result.measurements:
        if m.indicator != Indicator.RADIATION:
            continue
        day = (m.measured_at.date() - START).days + 1
        assert m.status == (Status.RED if day == 12 else Status.GREEN)


def test_glucosa_calibrada_o_de_referencia() -> None:
    cal = AnalyteCalibration(
        unit="mg/dL", baseline_mean=75.3, baseline_sd=11.3, postflight_mean=93.2,
        delta_pct=23.8, n_subjects=4,
    )
    calibrated = GlucoseModel.from_calibration({"glucose": cal})
    assert calibrated.source == Source.SIMULATED_CALIBRATED_OSDR
    assert calibrated.delta_pct == 23.8
    assert GlucoseModel.from_calibration(None).source == Source.SIMULATED_REFERENCE


async def test_clima_simulado_sin_donki() -> None:
    weather = await mission_space_weather(START, 5, use_real=False, today=START)
    assert len(weather) == 5
    assert all(d.source == Source.SIMULATED_REFERENCE and not d.sep_event for d in weather)


def test_recomendacion_para_valores_bajos() -> None:
    low = recommend(Indicator.BP_DIA, Status.RED, Environment.MARS, value=55)
    high = recommend(Indicator.BP_DIA, Status.RED, Environment.MARS, value=95)
    assert low.recommendation.startswith("Diastólica baja")
    assert high.recommendation.startswith("Diastólica alta")


async def test_exporta_json_y_csv(tmp_path: Path) -> None:
    result = await mission(days=3, mission_name="Prueba Marte")
    json_path = export_json(result, tmp_path)
    csv_paths = export_csv(result, tmp_path)

    assert json_path.name == "Prueba_Marte_42.json"
    data = json.loads(json_path.read_text(encoding="utf-8"))
    assert len(data["measurements"]) == 3 * 4 * 12
    assert data["meta"]["seed"] == 42
    lines = csv_paths[0].read_text(encoding="utf-8").splitlines()
    assert len(lines) == 1 + 3 * 4 * 12
    assert "source" in lines[0]


@pytest.mark.parametrize("env", list(Environment))
async def test_todos_los_entornos(env: Environment) -> None:
    result = await mission(environment=env, days=5)
    assert len(result.measurements) == 5 * 4 * 12
