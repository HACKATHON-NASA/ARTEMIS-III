"""Tests de la Fase 1: configuración y modelos."""

from datetime import date

import pytest
from pydantic import ValidationError

from simulador.models import ApiResponse, MissionRequest, Source, SpaceWeatherDay
from simulador.settings import BASE_DIR, Settings


def test_settings_lee_valores_y_oculta_la_key(monkeypatch: pytest.MonkeyPatch) -> None:
    """La configuración carga del entorno y la key no aparece al imprimirla."""
    monkeypatch.setenv("NASA_API_KEY", "clave-secreta-123")
    monkeypatch.setenv("CORS_ORIGINS", "http://a.test, http://b.test")
    settings = Settings(_env_file=None)

    assert settings.nasa_api_key.get_secret_value() == "clave-secreta-123"
    assert "clave-secreta-123" not in repr(settings)
    assert "clave-secreta-123" not in str(settings.model_dump())
    assert settings.cors_origins == ["http://a.test", "http://b.test"]
    assert settings.usa_demo_key is False


def test_settings_por_defecto_usa_demo_key(monkeypatch: pytest.MonkeyPatch) -> None:
    """Sin key configurada se usa DEMO_KEY."""
    monkeypatch.delenv("NASA_API_KEY", raising=False)
    settings = Settings(_env_file=None)
    assert settings.usa_demo_key is True
    assert settings.api_port == 8001


def test_base_dir_apunta_a_simulador() -> None:
    """BASE_DIR es la carpeta simulador/."""
    assert (BASE_DIR / "requirements.txt").exists()


def test_mission_request_valida_rangos() -> None:
    """Los parámetros de la misión se validan."""
    assert MissionRequest().days == 30
    with pytest.raises(ValidationError):
        MissionRequest(days=0)
    with pytest.raises(ValidationError):
        MissionRequest(environment="jupiter")


def test_space_weather_day_lleva_source() -> None:
    """Todo dato de DONKI lleva su origen."""
    dia = SpaceWeatherDay(date=date(2026, 11, 10))
    assert dia.source == Source.NASA_DONKI
    respuesta = ApiResponse[SpaceWeatherDay](data=dia).model_dump(mode="json")
    assert respuesta["message"] == "ok"
    assert respuesta["data"]["source"] == "nasa_donki"
