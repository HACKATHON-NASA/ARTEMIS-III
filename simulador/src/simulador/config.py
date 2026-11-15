"""Lectura de los archivos `config/*.yaml` (una sola vez por proceso)."""

from functools import lru_cache
from typing import Any

import yaml

from simulador.models import Environment
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


def environment_config(environment: Environment) -> dict[str, Any]:
    """Datos de un entorno (dosis base, gravedad, retraso de comunicación)."""
    envs = load_yaml("environments")["environments"]
    if environment.value not in envs:
        raise KeyError(f"Entorno sin datos en environments.yaml: {environment.value}")
    return envs[environment.value]


def all_environments() -> dict[str, dict[str, Any]]:
    """Todos los entornos."""
    return load_yaml("environments")["environments"]
