"""Tests de la Fase 2: colector DONKI (sin internet, con respx)."""

import logging
from datetime import date

import httpx
import pytest
import respx

from simulador.collectors.donki import (
    EVENT_TYPES,
    DonkiClient,
    DonkiUnavailableError,
    classify_risk,
    daily_space_weather,
)
from simulador.models import RiskLevel
from simulador.settings import Settings

BASE = "https://donki.test/DONKI"
FALLBACK = "https://fallback.test/DONKI"
KEY = "clave-super-secreta"
START, END = date(2026, 11, 1), date(2026, 11, 5)


@pytest.fixture
def client() -> DonkiClient:
    settings = Settings(
        _env_file=None,
        nasa_api_key=KEY,
        donki_base_url=BASE,
        donki_fallback_url=FALLBACK,
        http_timeout_seconds=2,
    )
    return DonkiClient(settings)


def _mock_all(router: respx.Router, base: str, payloads: dict[str, object]) -> None:
    for event_type in EVENT_TYPES:
        router.get(f"{base}/{event_type}").mock(
            return_value=httpx.Response(200, json=payloads.get(event_type, []))
        )


# ----------------------------------------------------------------- reglas


@pytest.mark.parametrize(
    ("sep", "flare", "kp", "expected"),
    [
        (True, None, None, (RiskLevel.HIGH, 3.0)),
        (True, "X2.0", 8, (RiskLevel.HIGH, 3.0)),
        (False, "X1.1", None, (RiskLevel.HIGH, 2.0)),
        (False, "M5.0", None, (RiskLevel.MEDIUM, 1.5)),
        (False, "C9.9", 6, (RiskLevel.MEDIUM, 1.5)),
        (False, "C9.9", 5.67, (RiskLevel.LOW, 1.0)),
        (False, None, None, (RiskLevel.LOW, 1.0)),
    ],
)
def test_classify_risk(sep, flare, kp, expected) -> None:
    assert classify_risk(sep, flare, kp) == expected


def test_daily_space_weather_agrupa_por_dia() -> None:
    events = {
        "FLR": [
            {"flrID": "F1", "classType": "C3.0", "peakTime": "2026-11-02T01:00Z"},
            {"flrID": "F2", "classType": "M2.1", "peakTime": "2026-11-02T09:00Z"},
            {"flrID": "F3", "classType": "X1.0", "beginTime": "2026-11-04T00:00Z"},
        ],
        "SEP": [{"sepID": "S1", "eventTime": "2026-11-03T04:00Z"}],
        "GST": [
            {
                "gstID": "G1",
                "startTime": "2026-11-05T00:00Z",
                "allKpIndex": [
                    {"observedTime": "2026-11-05T03:00Z", "kpIndex": 5.33},
                    {"observedTime": "2026-11-05T06:00Z", "kpIndex": 6.67},
                ],
            }
        ],
        "CME": [{"activityID": "C1", "startTime": "2026-11-01T12:00Z"}],
    }
    days = {d.date.day: d for d in daily_space_weather(events, START, END)}

    assert len(days) == 5
    assert days[1].risk_level == RiskLevel.LOW and days[1].events == ["C1"]
    assert days[2].flare_max_class == "M2.1" and days[2].radiation_multiplier == 1.5
    assert days[3].sep_event and days[3].risk_level == RiskLevel.HIGH
    assert days[4].radiation_multiplier == 2.0
    assert days[5].geomagnetic_kp_max == 6.67 and days[5].risk_level == RiskLevel.MEDIUM
    assert all(d.source == "nasa_donki" for d in days.values())


# ------------------------------------------------------------------- HTTP


@respx.mock
async def test_fetch_all_pide_en_paralelo_y_usa_memoria(client: DonkiClient) -> None:
    _mock_all(respx.mock, BASE, {"SEP": [{"sepID": "S1", "eventTime": "2026-11-03T04:00Z"}]})

    result = await client.fetch_all(START, END)
    assert result.stale is False
    assert len(result.events["SEP"]) == 1
    assert respx.mock.calls.call_count == len(EVENT_TYPES)

    await client.fetch_all(START, END)  # segunda vez: desde memoria
    assert respx.mock.calls.call_count == len(EVENT_TYPES)


@respx.mock
async def test_cuerpo_vacio_es_lista_vacia(client: DonkiClient) -> None:
    for event_type in EVENT_TYPES:
        respx.get(f"{BASE}/{event_type}").mock(return_value=httpx.Response(200, text=""))
    result = await client.fetch_all(START, END)
    assert all(events == [] for events in result.events.values())


@respx.mock
async def test_usa_respaldo_sin_key_si_falla_la_principal(client: DonkiClient) -> None:
    for event_type in EVENT_TYPES:
        respx.get(f"{BASE}/{event_type}").mock(return_value=httpx.Response(503))
    _mock_all(respx.mock, FALLBACK, {"FLR": [{"flrID": "F1", "classType": "X1.0"}]})

    result = await client.fetch_all(START, END)
    assert result.events["FLR"][0]["flrID"] == "F1"
    fallback_calls = [c for c in respx.mock.calls if c.request.url.host == "fallback.test"]
    assert fallback_calls
    assert all("api_key" not in str(c.request.url) for c in fallback_calls)


@respx.mock
async def test_si_todo_falla_usa_lo_ultimo_en_memoria_marcado_stale(client: DonkiClient) -> None:
    _mock_all(respx.mock, BASE, {"SEP": [{"sepID": "S1"}]})
    await client.fetch_all(START, END)

    client.settings.cache_ttl_minutes = 1
    for entry in client._memory.values():
        entry.fetched_at -= 3600  # vencido
    client._results.clear()
    respx.mock.reset()
    respx.mock.routes.clear()
    respx.route(host="donki.test").mock(side_effect=httpx.ConnectError("caído"))
    respx.route(host="fallback.test").mock(side_effect=httpx.ConnectError("caído"))

    result = await client.fetch_all(START, END)
    assert result.stale is True
    assert result.events["SEP"] == [{"sepID": "S1"}]


@respx.mock
async def test_sin_nada_en_memoria_y_todo_caido_lanza_error(client: DonkiClient) -> None:
    respx.route().mock(side_effect=httpx.ConnectError("caído"))
    with pytest.raises(DonkiUnavailableError):
        await client.fetch_all(START, END)


@respx.mock
async def test_si_flr_falla_se_lee_de_las_notificaciones(client: DonkiClient) -> None:
    notification = {
        "messageType": "FLR",
        "messageID": "N1",
        "messageIssueTime": "2026-11-04T15:48Z",
        "messageBody": "Significant flare detected by GOES. Flare intensity has crossed "
        "the threshold of M5.0. Peak class X1.2.",
    }
    _mock_all(respx.mock, BASE, {"notifications": [notification]})
    respx.get(f"{BASE}/FLR").mock(return_value=httpx.Response(404))
    respx.get(f"{FALLBACK}/FLR").mock(side_effect=httpx.ConnectTimeout("lento"))

    result = await client.fetch_all(START, END)
    assert result.stale is False
    assert result.derived == {"FLR": "notifications"}
    assert result.events["FLR"][0]["classType"] == "X1.2"

    day4 = daily_space_weather(result.events, START, END)[3]
    assert day4.flare_max_class == "X1.2" and day4.radiation_multiplier == 2.0


@respx.mock
async def test_no_insiste_con_lo_caido(client: DonkiClient) -> None:
    """Un endpoint caído no se vuelve a pedir hasta que pase el TTL."""
    _mock_all(respx.mock, BASE, {})
    flr = respx.get(f"{BASE}/FLR").mock(return_value=httpx.Response(404))
    fallback = respx.get(f"{FALLBACK}/FLR").mock(side_effect=httpx.ConnectTimeout("lento"))

    await client.fetch_all(START, END)
    await client.fetch_all(START, date(2026, 11, 6))  # otro rango: no está en memoria
    assert flr.call_count == 1
    assert fallback.call_count == 1


@respx.mock
async def test_sin_respaldo_configurado_no_lo_llama() -> None:
    settings = Settings(_env_file=None, nasa_api_key=KEY, donki_base_url=BASE, donki_fallback_url="")
    no_fallback = DonkiClient(settings)
    _mock_all(respx.mock, BASE, {})
    respx.get(f"{BASE}/FLR").mock(return_value=httpx.Response(404))

    result = await no_fallback.fetch_all(START, END)
    assert result.derived == {"FLR": "notifications"}
    assert all(c.request.url.host == "donki.test" for c in respx.mock.calls)


@respx.mock
async def test_la_key_no_sale_en_los_logs(
    client: DonkiClient, caplog: pytest.LogCaptureFixture
) -> None:
    respx.route(host="donki.test").mock(return_value=httpx.Response(500))
    respx.route(host="fallback.test").mock(return_value=httpx.Response(500))
    with caplog.at_level(logging.DEBUG):
        with pytest.raises(DonkiUnavailableError):
            await client.fetch_all(START, END)
    assert KEY not in caplog.text
