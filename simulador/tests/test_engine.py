"""Tests de la Fase 5: umbrales, puntaje, tendencias y recomendaciones."""

from datetime import datetime, timedelta, timezone

import pytest

from simulador.config import all_environments, body_parts_config, load_yaml
from simulador.engine import assess, body_status, evaluate, recommend, score, status_of, trend
from simulador.models import (
    BodyPart,
    Environment,
    Indicator,
    Measurement,
    Source,
    Status,
    TrendDirection,
)

G, Y, R = Status.GREEN, Status.YELLOW, Status.RED
I = Indicator


# Tabla de SIMULADOR.md §7, con los bordes de cada banda.
@pytest.mark.parametrize(
    ("indicator", "value", "expected"),
    [
        (I.HR, 60, G), (I.HR, 100, G), (I.HR, 59, Y), (I.HR, 50, Y), (I.HR, 101, Y),
        (I.HR, 120, Y), (I.HR, 49, R), (I.HR, 121, R),
        (I.BP_SYS, 90, G), (I.BP_SYS, 119, G), (I.BP_SYS, 120, Y), (I.BP_SYS, 139, Y),
        (I.BP_SYS, 89, R), (I.BP_SYS, 140, R), (I.BP_SYS, 148, R),
        (I.BP_DIA, 60, G), (I.BP_DIA, 79, G), (I.BP_DIA, 80, Y), (I.BP_DIA, 89, Y),
        (I.BP_DIA, 59, R), (I.BP_DIA, 90, R), (I.BP_DIA, 94, R),
        (I.GLUCOSE, 70, G), (I.GLUCOSE, 99, G), (I.GLUCOSE, 60, Y), (I.GLUCOSE, 69, Y),
        (I.GLUCOSE, 100, Y), (I.GLUCOSE, 125, Y), (I.GLUCOSE, 59, R), (I.GLUCOSE, 126, R),
        (I.TEMP, 36.1, G), (I.TEMP, 37.2, G), (I.TEMP, 35.5, Y), (I.TEMP, 36.0, Y),
        (I.TEMP, 37.3, Y), (I.TEMP, 38.0, Y), (I.TEMP, 35.4, R), (I.TEMP, 38.1, R),
        (I.SPO2, 95, G), (I.SPO2, 100, G), (I.SPO2, 90, Y), (I.SPO2, 94, Y), (I.SPO2, 89, R),
        (I.SLEEP, 7, G), (I.SLEEP, 9, G), (I.SLEEP, 6, Y), (I.SLEEP, 6.9, Y), (I.SLEEP, 5.9, R),
        (I.MOOD, 5, G), (I.MOOD, 4, G), (I.MOOD, 3, Y), (I.MOOD, 2, R), (I.MOOD, 1, R),
        (I.EXERCISE_MIN, 90, G), (I.EXERCISE_MIN, 60, Y), (I.EXERCISE_MIN, 20, R),
    ],
)
def test_umbrales(indicator: Indicator, value: float, expected: Status) -> None:
    assert status_of(indicator, value) == expected


def test_radiacion_relativa_al_entorno() -> None:
    # Marte: base 0,6 mSv/día
    assert status_of(I.RADIATION, 0.6, Environment.MARS) == G
    assert status_of(I.RADIATION, 1.2, Environment.MARS) == Y
    assert status_of(I.RADIATION, 1.21, Environment.MARS) == R
    assert status_of(I.RADIATION, 0.3, Environment.MARS, sep_active=True) == R
    # La misma dosis es normal en tránsito (base 1,8)
    assert status_of(I.RADIATION, 1.21, Environment.TRANSIT) == G


def test_evaluate_da_mensaje_legible() -> None:
    result = evaluate(I.BP_SYS, 148)
    assert result.status == R
    assert result.message == "Presión sistólica 148 mmHg: fuera de rango"
    assert "SEP" in evaluate(I.RADIATION, 0.5, sep_active=True).message


def test_score() -> None:
    assert score({I.HR: G, I.SLEEP: G}) == 100
    assert score({I.SLEEP: Y}) == 60
    # hr pesa 1,5: (20×1,5 + 100×1) / 2,5 = 52
    assert score({I.HR: R, I.SLEEP: G}) == 52
    assert score({}) == 100


def test_body_status_toma_el_peor() -> None:
    parts = body_status({I.HR: G, I.BP_SYS: R, I.SLEEP: Y})
    assert parts[BodyPart.HEART] == R
    assert parts[BodyPart.BRAIN] == Y
    assert BodyPart.LUNGS not in parts  # sin datos


def _m(indicator: Indicator, value: float, hours: int, astronaut: int = 1) -> Measurement:
    return Measurement(
        astronaut_id=astronaut,
        indicator=indicator,
        value=value,
        unit="x",
        measured_at=datetime(2026, 11, 1, tzinfo=timezone.utc) + timedelta(hours=hours),
        source=Source.SIMULATED_REFERENCE,
    )


def test_assess_usa_la_ultima_medicion() -> None:
    measurements = [
        _m(I.BP_SYS, 148, 0),  # vieja, ya no cuenta
        _m(I.BP_SYS, 115, 24),
        _m(I.SLEEP, 5.5, 24),
        _m(I.SPO2, 98, 24),
    ]
    result = assess(measurements, Environment.MARS)
    assert result.body_status[BodyPart.HEART] == G
    assert result.body_status[BodyPart.BRAIN] == R
    assert result.score == round((100 * 1.5 + 20 + 100 * 1.5) / 4)
    assert result.summary.startswith(f"Puntaje {result.score}/100. Atención: cerebro")


def test_assess_todo_verde() -> None:
    result = assess([_m(I.HR, 70, 0)], Environment.ISS)
    assert result.score == 100
    assert "Todos los sistemas en verde" in result.summary


def test_tendencia_del_sueno_de_la_demo() -> None:
    # Astronauta 2, días 18–21: 5,8 → 5,0 h
    result = trend(I.SLEEP, [7.2, 7.0, 7.1, 6.9, 5.8, 5.5, 5.0])
    assert result.direction == TrendDirection.DOWN
    assert result.trend_alert is True


def test_tendencia_alerta_aunque_siga_en_verde() -> None:
    result = trend(I.SPO2, [99, 99, 98, 97, 96])  # todo verde, pero cae
    assert result.trend_alert is True
    assert status_of(I.SPO2, 96) == G


def test_tendencia_estable_y_sin_alerta() -> None:
    result = trend(I.HR, [70, 71, 70, 72, 70, 71, 70])
    assert result.direction == TrendDirection.STABLE
    assert result.trend_alert is False


def test_tendencia_away_hr_que_se_aleja_del_centro() -> None:
    assert trend(I.HR, [80, 76, 70, 64, 58]).trend_alert is True  # baja y se aleja
    assert trend(I.HR, [100, 95, 90, 85]).trend_alert is False  # vuelve al centro


def test_recommend_rojo_avisa_a_tierra_con_retraso() -> None:
    rec = recommend(I.BP_SYS, R, Environment.MARS)
    assert rec.notify_ground is True
    assert rec.ground_delay_seconds == 1200
    assert "Presión alta" in rec.recommendation

    green = recommend(I.BP_SYS, G, Environment.MARS)
    assert green.notify_ground is False and green.ground_delay_seconds == 0


def test_config_completa() -> None:
    """Cada indicador tiene umbrales, recomendaciones y órgano; los 4 entornos existen."""
    thresholds = load_yaml("thresholds")["indicators"]
    recs = load_yaml("recommendations")["recommendations"]
    mapped = {i for part in body_parts_config().values() for i in part["indicators"]}
    for indicator in Indicator:
        assert indicator.value in thresholds
        assert {"green", "yellow", "red"} <= set(recs[indicator.value])
        assert indicator.value in mapped
    assert set(all_environments()) == {e.value for e in Environment}
