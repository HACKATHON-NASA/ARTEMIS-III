"""Tests de la Fase 7: API REST (sin internet; DONKI simulado)."""

from datetime import date, timedelta

import pytest
from fastapi.testclient import TestClient

from simulador.api import main as api_main
from simulador.collectors.donki import DonkiResult, DonkiUnavailableError
from simulador.generator import radiation


class FakeDonki:
    """Sustituto de DONKI: un SEP hace 2 días y una notificación."""

    def __init__(self, fail: bool = False) -> None:
        self.fail = fail
        self.last_updated = None
        self.calls = 0

    async def fetch_all(self, start: date, end: date) -> DonkiResult:
        self.calls += 1
        if self.fail:
            raise DonkiUnavailableError("caído")
        sep_day = (date.today() - timedelta(days=2)).isoformat()
        return DonkiResult(
            events={
                "FLR": [], "GST": [], "CME": [],
                "SEP": [{"sepID": "SEP-1", "eventTime": f"{sep_day}T04:00Z"}],
                "notifications": [{"messageID": "N1", "messageType": "SEP",
                                   "messageIssueTime": f"{sep_day}T05:00Z"}],
            }
        )


@pytest.fixture
def donki(monkeypatch: pytest.MonkeyPatch) -> FakeDonki:
    fake = FakeDonki()
    monkeypatch.setattr(api_main, "get_donki_client", lambda: fake)
    monkeypatch.setattr(radiation, "get_donki_client", lambda: fake)
    return fake


@pytest.fixture
def client(donki: FakeDonki) -> TestClient:
    with TestClient(api_main.create_app(prewarm=False)) as c:
        yield c


def test_health(client: TestClient) -> None:
    body = client.get("/api/v1/health").json()
    assert body["message"] == "ok"
    assert body["data"]["status"] == "ok"
    assert "No reemplaza el criterio médico" in body["data"]["disclaimer"]


def test_docs_disponibles(client: TestClient) -> None:
    assert client.get("/docs").status_code == 200
    paths = client.get("/openapi.json").json()["paths"]
    for path in ["/health", "/space-weather", "/space-weather/now", "/calibration", "/simulate",
                 "/environments"]:
        assert f"/api/v1{path}" in paths
    for path in ["/evaluate", "/assess", "/trends", "/recommendations"]:
        assert f"/api/v1{path}" not in paths  # la evaluación la hace el sistema principal


def test_space_weather(client: TestClient) -> None:
    body = client.get("/api/v1/space-weather").json()
    days = body["data"]
    assert len(days) == 30
    assert days[-3]["sep_event"] is True and days[-3]["risk_level"] == "high"
    assert days[0]["source"] == "nasa_donki"


def test_space_weather_rango_invalido(client: TestClient) -> None:
    response = client.get("/api/v1/space-weather", params={"start": "2026-11-10", "end": "2026-11-01"})
    assert response.status_code == 422
    assert response.json() == {
        "data": None, "message": "start debe ser anterior o igual a end",
        "error_code": "INVALID_DATE_RANGE",
    }


def test_space_weather_now(client: TestClient) -> None:
    data = client.get("/api/v1/space-weather/now").json()["data"]
    assert data["today"]["date"] == date.today().isoformat()
    assert date.today() - timedelta(days=2) in [
        date.fromisoformat(d) for d in data["last_7_days_high_risk"]
    ]
    assert data["notifications"][0]["id"] == "N1"


def test_donki_caido_da_503(monkeypatch: pytest.MonkeyPatch) -> None:
    monkeypatch.setattr(api_main, "get_donki_client", lambda: FakeDonki(fail=True))
    with TestClient(api_main.create_app(prewarm=False)) as c:
        response = c.get("/api/v1/space-weather")
    assert response.status_code == 503
    assert response.json()["error_code"] == "DONKI_UNAVAILABLE"


def test_simulate(client: TestClient) -> None:
    body = {"days": 30, "seed": 42, "environment": "mars", "start_date": "2026-10-01",
            "use_real_space_weather": False}
    data = client.post("/api/v1/simulate", json=body).json()["data"]
    assert len(data["crew"]) == 4
    assert len(data["measurements"]) == 30 * 4 * 11
    assert data["meta"]["seed"] == 42
    assert "alerts" not in data
    assert all("status" not in m for m in data["measurements"])

    again = client.post("/api/v1/simulate", json=body).json()["data"]
    assert again["measurements"] == data["measurements"]


def test_simulate_con_clima_real_usa_donki(client: TestClient, donki: FakeDonki) -> None:
    data = client.post("/api/v1/simulate", json={"days": 10}).json()["data"]
    sources = {d["source"] for d in data["space_weather"]}
    assert "nasa_donki" in sources
    assert donki.calls == 1


def test_simulate_valida(client: TestClient) -> None:
    response = client.post("/api/v1/simulate", json={"days": 0})
    assert response.status_code == 422
    body = response.json()
    assert body["error_code"] == "VALIDATION_ERROR" and body["data"] is None
    assert body["message"].startswith("days")


def test_environments(client: TestClient) -> None:
    data = client.get("/api/v1/environments").json()["data"]
    assert {e["id"] for e in data} == {"iss", "transit", "moon", "mars"}


def test_ruta_inexistente(client: TestClient) -> None:
    response = client.get("/api/v1/no-existe")
    assert response.status_code == 404
    assert response.json()["error_code"] == "NOT_FOUND"


def test_cors(client: TestClient) -> None:
    response = client.get("/api/v1/health", headers={"Origin": "http://localhost:5173"})
    assert response.headers["access-control-allow-origin"] == "http://localhost:5173"
