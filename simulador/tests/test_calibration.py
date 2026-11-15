"""Tests de la Fase 4: calibración OSDR."""

import json
from pathlib import Path

import pandas as pd
import pytest

from simulador.calibration.osdr_calibration import (
    analyte_key,
    build_calibration,
    calibrate,
    load_calibration,
)
from simulador.collectors.osdr import TIDY_COLUMNS


def _row(
    analyte: str, value: float, subject: str, timepoint: str, day: int,
    rmin: float = 65, rmax: float = 99,
) -> dict:
    phase = "preflight" if timepoint.startswith("L") else "postflight"
    return dict(
        dataset="OSD-575", analyte=analyte, value=value, unit="mg/dL", subject=subject,
        sex="M", timepoint=timepoint, day=day, phase=phase, range_min=rmin, range_max=rmax,
    )


@pytest.fixture
def tidy() -> pd.DataFrame:
    rows = [
        _row("GLUCOSE", 80, "C001", "L-92", -92),
        _row("GLUCOSE", 90, "C001", "L-3", -3),
        _row("GLUCOSE", 100, "C002", "L-3", -3),
        _row("GLUCOSE", 99, "C001", "R+1", 1),
        _row("GLUCOSE", 99, "C002", "R+1", 1),
        _row("GLUCOSE", 40, "C001", "R+45", 45),  # no es el primer punto: no cuenta
        _row("ONLY PRE", 5, "C001", "L-3", -3),
    ]
    return pd.DataFrame(rows, columns=TIDY_COLUMNS)


def test_analyte_key() -> None:
    assert analyte_key("PLATELET COUNT") == "platelet_count"
    assert analyte_key("BILIRUBIN; TOTAL") == "bilirubin_total"
    assert analyte_key("UREA NITROGEN (BUN)") == "urea_nitrogen_bun"


def test_calibrate_usa_pre_vuelo_y_primer_post_vuelo(tidy: pd.DataFrame) -> None:
    report = calibrate(tidy)
    glucose = report.analytes["glucose"]

    assert glucose.baseline_mean == 90.0
    assert glucose.baseline_sd == 10.0
    assert glucose.postflight_mean == 99.0
    assert glucose.delta_pct == 10.0
    assert glucose.postflight_timepoint == "R+1"
    assert glucose.n_subjects == 2
    assert glucose.source == "nasa_osdr"
    assert glucose.datasets == ["OSD-575"]
    assert report.skipped == {"only_pre": "sin datos pre o post-vuelo"}


def test_build_y_load_calibration(tmp_path: Path) -> None:
    raw = tmp_path / "raw"
    raw.mkdir()
    (raw / "OSD-575__cmp.csv").write_text(
        "ANALYTE,VALUE,UNITS,SUBJECT_ID,SEX,timepoint\n"
        "GLUCOSE,80,mg/dL,C001,M,L-3\nGLUCOSE,100,mg/dL,C002,F,L-3\n"
        "GLUCOSE,99,mg/dL,C001,M,R+1\n",
        encoding="utf-8",
    )
    output = tmp_path / "processed" / "calibration.json"
    build_calibration(raw, output)

    data = json.loads(output.read_text(encoding="utf-8"))
    assert data["glucose"]["baseline_mean"] == 90.0
    assert load_calibration(output)["glucose"].delta_pct == 10.0


def test_sin_archivos_avisa(tmp_path: Path) -> None:
    with pytest.raises(FileNotFoundError):
        build_calibration(tmp_path, tmp_path / "out.json")
    with pytest.raises(FileNotFoundError):
        load_calibration(tmp_path / "no-existe.json")


def test_excluye_muestras_con_columnas_cruzadas(tidy: pd.DataFrame) -> None:
    """Caso real de OSD-569 C003 L-92: MPV=330 delata la muestra; se va completa."""
    corrupt = pd.DataFrame(
        [
            _row("MPV", 330, "C003", "L-92", -92, 7.5, 12.5),
            _row("GLUCOSE", 500, "C003", "L-92", -92),  # misma muestra: también fuera
            _row("GLUCOSE", 36, "C004", "L-3", -3),  # raro pero posible: se queda
        ],
        columns=TIDY_COLUMNS,
    )
    report = calibrate(pd.concat([tidy, corrupt], ignore_index=True))

    assert len(report.excluded_samples) == 1
    assert "C003 L-92" in report.excluded_samples[0] and "MPV=330" in report.excluded_samples[0]
    glucose = report.analytes["glucose"]
    assert glucose.n_baseline_samples == 4  # 3 originales + el 36 de C004
    assert glucose.baseline_mean == (80 + 90 + 100 + 36) / 4
