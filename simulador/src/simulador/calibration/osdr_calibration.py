"""Calibración del generador con los datos clínicos reales de Inspiration4 (OSDR).

Por cada analito (SIMULADOR.md §5, paso 3):
- `baseline_mean` / `baseline_sd`: media y desviación de todos los puntos pre-vuelo (`L-`).
- `postflight_mean`: media del primer punto post-vuelo que aparezca en los datos (`R+1`).
- `delta_pct`: `(postflight_mean - baseline_mean) / baseline_mean * 100`.

Solo se usan valores presentes en los archivos: si un analito no tiene datos
pre y post-vuelo, se omite y se informa.
"""

import json
import re
from dataclasses import dataclass, field
from pathlib import Path

import pandas as pd

from simulador.collectors.osdr import load_all_long
from simulador.models import AnalyteCalibration, Source
from simulador.settings import OSDR_PROCESSED_DIR, OSDR_RAW_DIR

CALIBRATION_FILE: Path = OSDR_PROCESSED_DIR / "calibration.json"


@dataclass
class CalibrationReport:
    """Resultado de la calibración: analitos calibrados y omitidos (con el motivo)."""

    analytes: dict[str, AnalyteCalibration]
    skipped: dict[str, str] = field(default_factory=dict)
    excluded_samples: list[str] = field(default_factory=list)
    """Muestras descartadas por error de captura, con el valor que las delató."""


IMPLAUSIBLE_WIDTHS = 2.0
"""Un valor a más de 2 anchos del rango de referencia es un error de captura, no fisiología.

Ejemplo real (OSD-569, C003, L-92): MPV = 330 fL con rango 7,5–12,5, y plaquetas
= 12,9 con rango 140–400: las columnas de esa muestra están cruzadas.
Valores raros pero posibles (glucosa 36 mg/dL con rango 65–99) se conservan.
"""


def find_corrupt_samples(tidy: pd.DataFrame) -> tuple[pd.DataFrame, list[str]]:
    """Quita las muestras (estudio + sujeto + tiempo) con algún valor imposible."""
    width = tidy["range_max"] - tidy["range_min"]
    implausible = (width > 0) & (
        (tidy["value"] > tidy["range_max"] + IMPLAUSIBLE_WIDTHS * width)
        | (tidy["value"] < tidy["range_min"] - IMPLAUSIBLE_WIDTHS * width)
    )
    sample_cols = ["dataset", "subject", "timepoint"]
    bad = tidy[implausible.fillna(False)]
    if bad.empty:
        return tidy, []

    notes = [
        f"{ds} {subj} {tp} ({row_text})"
        for (ds, subj, tp), rows in bad.groupby(sample_cols)
        for row_text in [
            ", ".join(
                f"{r.analyte}={r.value:g} [{r.range_min:g}–{r.range_max:g}]"
                for r in rows.itertuples()
            )
        ]
    ]
    bad_keys = set(map(tuple, bad[sample_cols].to_numpy()))
    keep = ~tidy[sample_cols].apply(tuple, axis=1).isin(bad_keys)
    return tidy[keep], notes


def analyte_key(name: str) -> str:
    """`PLATELET COUNT` → `platelet_count`; `BILIRUBIN; TOTAL` → `bilirubin_total`."""
    return re.sub(r"[^a-z0-9]+", "_", name.lower()).strip("_")


def _main_unit(units: pd.Series) -> str:
    values = units.dropna().astype(str).str.strip()
    values = values[values != ""]
    return values.mode().iloc[0] if not values.empty else ""


def calibrate(tidy: pd.DataFrame) -> CalibrationReport:
    """Calcula la calibración de cada analito a partir de los datos limpios de OSDR."""
    analytes: dict[str, AnalyteCalibration] = {}
    skipped: dict[str, str] = {}
    tidy, excluded = find_corrupt_samples(tidy)

    for name, group in tidy.groupby("analyte"):
        key = analyte_key(str(name))
        pre = group[group["phase"] == "preflight"]
        post = group[group["phase"] == "postflight"]
        if pre.empty or post.empty:
            skipped[key] = "sin datos pre o post-vuelo"
            continue

        first_day = post["day"].min()
        first_post = post[post["day"] == first_day]
        baseline_mean = float(pre["value"].mean())
        if baseline_mean == 0:
            skipped[key] = "media pre-vuelo igual a 0"
            continue

        baseline_sd = float(pre["value"].std(ddof=1)) if len(pre) > 1 else 0.0
        postflight_mean = float(first_post["value"].mean())
        analytes[key] = AnalyteCalibration(
            unit=_main_unit(group["unit"]),
            baseline_mean=round(baseline_mean, 4),
            baseline_sd=round(baseline_sd, 4),
            postflight_mean=round(postflight_mean, 4),
            delta_pct=round((postflight_mean - baseline_mean) / baseline_mean * 100, 2),
            n_subjects=int(group["subject"].nunique()),
            source=Source.NASA_OSDR,
            datasets=sorted(group["dataset"].dropna().astype(str).unique()),
            analyte_name=str(name),
            n_baseline_samples=len(pre),
            postflight_timepoint=str(first_post["timepoint"].iloc[0]),
        )
    return CalibrationReport(
        analytes=dict(sorted(analytes.items())), skipped=skipped, excluded_samples=excluded
    )


def build_calibration(
    raw_dir: Path = OSDR_RAW_DIR, output: Path = CALIBRATION_FILE
) -> CalibrationReport:
    """Lee `data/osdr/raw/`, calibra y escribe `calibration.json`."""
    tidy = load_all_long(raw_dir)
    if tidy.empty:
        raise FileNotFoundError(
            f"No hay CSV de OSDR en {raw_dir}. Corra primero: python scripts/fetch_osdr.py"
        )
    report = calibrate(tidy)
    output.parent.mkdir(parents=True, exist_ok=True)
    payload = {k: v.model_dump(mode="json") for k, v in report.analytes.items()}
    output.write_text(json.dumps(payload, indent=2, ensure_ascii=False), encoding="utf-8")
    return report


_calibration_cache: dict[Path, tuple[float, dict[str, AnalyteCalibration]]] = {}


def load_calibration(path: Path = CALIBRATION_FILE) -> dict[str, AnalyteCalibration]:
    """Lee `calibration.json` (en memoria mientras el archivo no cambie)."""
    if not path.exists():
        raise FileNotFoundError(
            f"No existe {path.name}. Corra: python scripts/build_calibration.py"
        )
    mtime = path.stat().st_mtime
    cached = _calibration_cache.get(path)
    if cached and cached[0] == mtime:
        return cached[1]
    data = json.loads(path.read_text(encoding="utf-8"))
    calibration = {k: AnalyteCalibration.model_validate(v) for k, v in data.items()}
    _calibration_cache[path] = (mtime, calibration)
    return calibration
