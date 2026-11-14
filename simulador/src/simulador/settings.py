"""Configuración del simulador, cargada desde `simulador/.env`.

La API key se guarda como `SecretStr`: nunca aparece al imprimir
la configuración ni en los logs.
"""

from functools import lru_cache
from pathlib import Path
from typing import Annotated

from pydantic import Field, SecretStr, field_validator
from pydantic_settings import BaseSettings, NoDecode, SettingsConfigDict

# simulador/src/simulador/settings.py → simulador/
BASE_DIR: Path = Path(__file__).resolve().parents[2]
CONFIG_DIR: Path = BASE_DIR / "config"
DATA_DIR: Path = BASE_DIR / "data"
OSDR_RAW_DIR: Path = DATA_DIR / "osdr" / "raw"
OSDR_PROCESSED_DIR: Path = DATA_DIR / "osdr" / "processed"
DONKI_CACHE_DIR: Path = DATA_DIR / "donki" / "cache"
OUTPUT_DIR: Path = DATA_DIR / "output"


class Settings(BaseSettings):
    """Variables de entorno del simulador (ver `.env.example`)."""

    model_config = SettingsConfigDict(
        env_file=BASE_DIR / ".env",
        env_file_encoding="utf-8",
        extra="ignore",
    )

    # NASA
    nasa_api_key: SecretStr = Field(default=SecretStr("DEMO_KEY"))
    donki_base_url: str = "https://api.nasa.gov/DONKI"
    # Respaldo opcional (kauai). Vacío = desactivado: desde esta red no responde.
    donki_fallback_url: str = ""
    osdr_base_url: str = "https://osdr.nasa.gov"
    cache_ttl_minutes: int = Field(default=30, ge=1)
    http_timeout_seconds: float = Field(default=15, gt=0)
    http_connect_timeout_seconds: float = Field(default=4, gt=0)

    # API
    api_host: str = "0.0.0.0"
    api_port: int = Field(default=8001, ge=1, le=65535)
    cors_origins: Annotated[list[str], NoDecode] = ["http://localhost:5173", "http://localhost:8000"]

    # Exportador MySQL (opcional)
    db_enabled: bool = False
    db_host: str = "127.0.0.1"
    db_port: int = 3306
    db_database: str = "astrohealth"
    db_username: str = "root"
    db_password: SecretStr = Field(default=SecretStr(""))

    @field_validator("cors_origins", mode="before")
    @classmethod
    def _separar_origenes(cls, valor: object) -> object:
        """Acepta `CORS_ORIGINS` como lista separada por comas."""
        if isinstance(valor, str) and not valor.strip().startswith("["):
            return [origen.strip() for origen in valor.split(",") if origen.strip()]
        return valor

    @property
    def usa_demo_key(self) -> bool:
        """Indica si se está usando la key pública de prueba (límite bajo)."""
        key = self.nasa_api_key.get_secret_value()
        return key in ("", "DEMO_KEY", "pon_aqui_tu_key")


@lru_cache
def get_settings() -> Settings:
    """Devuelve la configuración (una sola instancia por proceso)."""
    return Settings()
