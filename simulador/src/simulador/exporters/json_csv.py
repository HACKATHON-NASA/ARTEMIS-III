"""Guarda una misión generada en `data/output/` como JSON y CSV."""

import csv
import re
from pathlib import Path

from simulador.models import MissionResult
from simulador.settings import OUTPUT_DIR


def _base_name(result: MissionResult) -> str:
    name = re.sub(r"[^A-Za-z0-9_-]+", "_", result.mission.name).strip("_") or "mission"
    return f"{name}_{result.meta.seed}"


def export_json(result: MissionResult, folder: Path = OUTPUT_DIR) -> Path:
    """`{mission}_{seed}.json` con la misión completa."""
    folder.mkdir(parents=True, exist_ok=True)
    path = folder / f"{_base_name(result)}.json"
    path.write_text(result.model_dump_json(indent=2), encoding="utf-8")
    return path


def export_csv(result: MissionResult, folder: Path = OUTPUT_DIR) -> list[Path]:
    """`{mission}_{seed}_measurements.csv` y `{mission}_{seed}_alerts.csv`."""
    folder.mkdir(parents=True, exist_ok=True)
    paths: list[Path] = []
    for suffix, rows in (("measurements", result.measurements), ("alerts", result.alerts)):
        path = folder / f"{_base_name(result)}_{suffix}.csv"
        dumped = [r.model_dump(mode="json") for r in rows]
        fields = list(dumped[0]) if dumped else []
        with path.open("w", newline="", encoding="utf-8") as handle:
            writer = csv.DictWriter(handle, fieldnames=fields)
            writer.writeheader()
            writer.writerows(dumped)
        paths.append(path)
    return paths
