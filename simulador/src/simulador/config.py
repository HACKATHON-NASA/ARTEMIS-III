"""Lectura de los archivos `config/*.yaml` (una sola vez por proceso)."""

from copy import deepcopy
from functools import lru_cache
from typing import Any

import yaml

from simulador.models import Environment, Indicator
from simulador.settings import CONFIG_DIR


@lru_cache
def load_yaml(name: str) -> dict[str, Any]:
    """Lee `config/{name}.yaml`. El resultado queda en memoria."""
    path = CONFIG_DIR / f"{name}.yaml"
    with path.open(encoding="utf-8") as handle:
        data = yaml.safe_load(handle) or {}
    if not isinstance(data, dict):
        raise ValueError(f"{path.name} debe ser un diccionario")
    return data


def _merge(base: dict[str, Any], extra: dict[str, Any]) -> dict[str, Any]:
    result = deepcopy(base)
    for key, value in extra.items():
        if isinstance(value, dict) and isinstance(result.get(key), dict):
            result[key] = _merge(result[key], value)
        else:
            result[key] = value
    return result


@lru_cache
def indicator_config(indicator: Indicator, environment: Environment) -> dict[str, Any]:
    """Umbrales de un indicador, con los ajustes del entorno aplicados."""
    data = load_yaml("thresholds")
    base = data["indicators"].get(indicator.value)
    if base is None:
        raise KeyError(f"Indicador sin umbrales en thresholds.yaml: {indicator.value}")
    overrides = (data.get("environment_overrides") or {}).get(environment.value, {})
    return _merge(base, overrides.get(indicator.value, {}))


def environment_config(environment: Environment) -> dict[str, Any]:
    """Datos de un entorno (dosis base, gravedad, retraso de comunicación)."""
    envs = load_yaml("environments")["environments"]
    if environment.value not in envs:
        raise KeyError(f"Entorno sin datos en environments.yaml: {environment.value}")
    return envs[environment.value]


def all_environments() -> dict[str, dict[str, Any]]:
    """Todos los entornos."""
    return load_yaml("environments")["environments"]


def scoring_config() -> dict[str, Any]:
    """Puntos por estado y pesos por indicador."""
    return load_yaml("thresholds")["scoring"]


def trends_config() -> dict[str, Any]:
    """Ventana y regla de alerta de tendencia."""
    return load_yaml("thresholds")["trends"]


def body_parts_config() -> dict[str, dict[str, Any]]:
    """Órganos ↔ indicadores ↔ riesgo HRP."""
    return load_yaml("body_parts")["body_parts"]


def recommendations_config() -> dict[str, Any]:
    """Textos de recomendación."""
    return load_yaml("recommendations")
