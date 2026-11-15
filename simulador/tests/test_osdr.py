"""Tests de la Fase 3: colector OSDR (sin internet)."""

from pathlib import Path

import httpx
import pytest
import respx

from simulador.collectors.osdr import (
    OsdrFile,
    download_clinical,
    inspect_file,
    is_clinical,
    load_all_long,
    load_long,
    normalize_analyte,
)
from simulador.settings import Settings

CBC = """ANALYTE,VALUE,RANGE_MIN,RANGE_MAX,UNITS,TEST_TYPE,SUBJECT_ID,SEX,TEST_DATE
HEMOGLOBIN,14.1,13.2,17.1,g/dL,CBC,C001,M,L-92
HEMOGLOBIN,14.1,13.2,17.1,g/dL,CBC,C001,M,L-92
HEMOGLOBIN (FEMALE),13.5,11.7,15.5,g/dL,CBC,C003,F,R+1
HEMOGLOBIN,n/a,11.7,15.5,g/dL,CBC,C003,F,R+45
"""
CMP = """ANALYTE,VALUE,RANGE_MIN,RANGE_MAX,UNITS,TEST_TYPE,SUBJECT_ID,SEX,timepoint
GLUCOSE,83,65,99,mg/dL,CMP,C001,M,L-92
GLUCOSE,90,65,99,mg/dL,CMP,C001,M,R+1
"""
WIDE = "Sample Name,glucose_value_mg\nC001_L-3,80\n"


@pytest.fixture
def raw(tmp_path: Path) -> Path:
    (tmp_path / "OSD-569__cbc.csv").write_text(CBC, encoding="utf-8")
    (tmp_path / "OSD-575__cmp.csv").write_text(CMP, encoding="utf-8")
    (tmp_path / "OSD-575__cmp_TRANSFORMED.csv").write_text(WIDE, encoding="utf-8")
    return tmp_path


def test_normalize_analyte() -> None:
    assert normalize_analyte("HEMOGLOBIN (FEMALE)") == "HEMOGLOBIN"
    assert normalize_analyte(" glucose ") == "GLUCOSE"


def test_inspect_detecta_test_date_como_tiempo(raw: Path) -> None:
    summary = inspect_file(raw / "OSD-569__cbc.csv")
    assert summary.dataset == "OSD-569"
    assert summary.timepoints == ["L-92", "R+1", "R+45"]
    assert summary.subjects == ["C001", "C003"]


def test_load_long_limpia_sin_inventar(raw: Path) -> None:
    tidy = load_long(raw / "OSD-569__cbc.csv")
    assert len(tidy) == 2  # duplicado y "n/a" fuera
    assert set(tidy["analyte"]) == {"HEMOGLOBIN"}
    row = tidy[tidy["subject"] == "C003"].iloc[0]
    assert row["timepoint"] == "R+1" and row["day"] == 1 and row["phase"] == "postflight"
    assert tidy[tidy["subject"] == "C001"].iloc[0]["day"] == -92


def test_load_all_long_omite_formato_ancho(raw: Path) -> None:
    tidy = load_all_long(raw)
    assert set(tidy["dataset"]) == {"OSD-569", "OSD-575"}
    assert len(tidy) == 4


def test_is_clinical() -> None:
    def f(name: str, cat: str, size: int = 1000) -> OsdrFile:
        return OsdrFile("OSD-1", name, size, cat, "u")

    assert is_clinical(f("a.csv", "Complete Blood Count"))
    assert not is_clinical(f("a.xlsx", "Complete Blood Count"))
    assert not is_clinical(f("a.csv", "long read RNA-Seq"))
    assert not is_clinical(f("a.csv", "Comprehensive Metabolic Panel", 50_000_000))


@respx.mock
async def test_download_clinical_solo_baja_lo_clinico(tmp_path: Path) -> None:
    base = "https://osdr.test"
    listing = {
        "studies": {
            "OSD-569": {
                "study_files": [
                    {"file_name": "cbc.csv", "file_size": 100, "category": "Complete Blood Count",
                     "remote_url": "/dl?file=cbc.csv"},
                    {"file_name": "rna.xlsx", "file_size": 9e7, "category": "RNA-Seq",
                     "remote_url": "/dl?file=rna.xlsx"},
                    {"file_name": "../evil.csv", "file_size": 10, "category": "Complete Blood Count",
                     "remote_url": "/dl?file=evil"},
                ]
            }
        }
    }
    respx.get(f"{base}/osdr/data/osd/files/569").mock(return_value=httpx.Response(200, json=listing))
    dl = respx.get(f"{base}/dl").mock(return_value=httpx.Response(200, text=CBC))

    settings = Settings(_env_file=None, osdr_base_url=base)
    paths = await download_clinical(("OSD-569",), dest=tmp_path, settings=settings)

    assert [p.name for p in paths] == ["OSD-569__cbc.csv"]
    assert dl.call_count == 1
    assert not (tmp_path.parent / "evil.csv").exists()
