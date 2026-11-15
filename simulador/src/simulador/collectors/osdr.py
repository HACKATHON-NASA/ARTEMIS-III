"""Colector de NASA OSDR: datos clínicos reales de Inspiration4.

- Lista los archivos de cada estudio con `GET {OSDR_BASE_URL}/osdr/data/osd/files/{n}`
  (ruta verificada el 2026-11-14).
- Descarga solo los CSV clínicos (hemograma y panel metabólico) a `data/osdr/raw/`.
- Todo lo demás funciona con los archivos locales, sin internet.
- La inspección detecta columnas, analitos y puntos de tiempo reales:
  no inventa nombres.
"""

import asyncio
import logging
import re
from dataclasses import dataclass, field
from pathlib import Path
from typing import Any

import httpx
import pandas as pd

from simulador.settings import OSDR_RAW_DIR, Settings, get_settings

logger = logging.getLogger(__name__)

DATASETS: tuple[str, ...] = ("OSD-569", "OSD-570", "OSD-571", "OSD-575")
"""Estudios de Inspiration4 que indica SIMULADOR.md."""

CLINICAL_CATEGORIES: tuple[str, ...] = ("Complete Blood Count", "Comprehensive Metabolic Panel")
"""Categorías de OSDR con valores clínicos (hemograma y panel metabólico)."""

MAX_FILE_BYTES = 5_000_000
"""No se descargan archivos grandes (genómica, proteómica)."""

_SAFE_NAME = re.compile(r"^[A-Za-z0-9._-]+$")


@dataclass
class OsdrFile:
    """Archivo publicado en un estudio de OSDR."""

    dataset: str
    name: str
    size: int
    category: str
    url: str


@dataclass
class FileSummary:
    """Resumen real de un archivo descargado."""

    path: Path
    dataset: str | None
    rows: int
    columns: list[str]
    analytes: list[str] = field(default_factory=list)
    timepoints: list[str] = field(default_factory=list)
    subjects: list[str] = field(default_factory=list)
    layout: str = "desconocido"


# ---------------------------------------------------------------------------
# Descarga
# ---------------------------------------------------------------------------


async def list_files(
    dataset: str, client: httpx.AsyncClient, settings: Settings | None = None
) -> list[OsdrFile]:
    """Lista los archivos de un estudio (`OSD-569` o `569`)."""
    settings = settings or get_settings()
    number = dataset.upper().removeprefix("OSD-")
    response = await client.get(f"{settings.osdr_base_url}/osdr/data/osd/files/{number}")
    response.raise_for_status()
    studies: dict[str, Any] = response.json().get("studies", {})

    files: list[OsdrFile] = []
    for study_id, study in studies.items():
        for item in study.get("study_files", []):
            remote = item.get("remote_url") or ""
            files.append(
                OsdrFile(
                    dataset=study_id,
                    name=item.get("file_name", ""),
                    size=int(item.get("file_size") or 0),
                    category=(item.get("category") or "").strip(),
                    url=remote if remote.startswith("http") else f"{settings.osdr_base_url}{remote}",
                )
            )
    return files


def is_clinical(file: OsdrFile) -> bool:
    """Un CSV pequeño de hemograma o panel metabólico."""
    return (
        file.category in CLINICAL_CATEGORIES
        and file.name.lower().endswith(".csv")
        and 0 < file.size <= MAX_FILE_BYTES
    )


async def download_clinical(
    datasets: tuple[str, ...] = DATASETS,
    dest: Path = OSDR_RAW_DIR,
    overwrite: bool = False,
    settings: Settings | None = None,
) -> list[Path]:
    """Descarga los CSV clínicos de los estudios a `dest` (en paralelo)."""
    settings = settings or get_settings()
    dest.mkdir(parents=True, exist_ok=True)
    timeout = httpx.Timeout(
        settings.http_timeout_seconds * 4, connect=settings.http_connect_timeout_seconds
    )

    async with httpx.AsyncClient(timeout=timeout, follow_redirects=True) as client:
        listings = await asyncio.gather(*(list_files(d, client, settings) for d in datasets))
        wanted = [f for files in listings for f in files if is_clinical(f)]

        async def fetch(file: OsdrFile) -> Path | None:
            if not _SAFE_NAME.match(file.name):
                logger.warning("Nombre de archivo raro, se omite: %r", file.name)
                return None
            target = dest / f"{file.dataset}__{file.name}"
            if target.exists() and not overwrite:
                return target
            response = await client.get(file.url)
            response.raise_for_status()
            target.write_bytes(response.content)
            return target

        paths = await asyncio.gather(*(fetch(f) for f in wanted))
    return [p for p in paths if p is not None]


# ---------------------------------------------------------------------------
# Lectura e inspección (solo archivos locales)
# ---------------------------------------------------------------------------


def raw_files(folder: Path = OSDR_RAW_DIR) -> list[Path]:
    """CSV presentes en `data/osdr/raw/` (descargados o puestos a mano)."""
    return sorted(p for p in folder.glob("*.csv") if p.is_file())


def dataset_of(path: Path) -> str | None:
    """Estudio de origen, leído del nombre del archivo."""
    match = re.search(r"OSD-\d+", path.name)
    return match.group(0) if match else None


def load_csv(path: Path) -> pd.DataFrame:
    """Lee un CSV de OSDR tal cual viene."""
    return pd.read_csv(path, encoding_errors="replace", low_memory=False)


def _find_column(columns: list[str], *keywords: str) -> str | None:
    """Primera columna cuyo nombre contiene alguna de las palabras dadas."""
    for column in columns:
        lowered = column.lower()
        if any(k in lowered for k in keywords):
            return column
    return None


def inspect_file(path: Path) -> FileSummary:
    """Detecta la forma real del archivo: columnas, analitos, tiempos y sujetos."""
    df = load_csv(path)
    columns = [str(c) for c in df.columns]
    summary = FileSummary(path=path, dataset=dataset_of(path), rows=len(df), columns=columns)

    analyte_col = _find_column(columns, "analyte", "test_name", "parameter")
    # En el CBC (OSD-569) los puntos de tiempo vienen en TEST_DATE (L-92, R+1…).
    time_col = _find_column(columns, "timepoint", "time_point", "time point", "test_date")
    subject_col = _find_column(columns, "subject", "astronaut", "crew", "participant")

    if analyte_col:
        summary.layout = "largo (una fila por analito)"
        summary.analytes = sorted(df[analyte_col].dropna().astype(str).unique())
    else:
        numeric = [c for c in columns if pd.api.types.is_numeric_dtype(df[c])]
        if numeric:
            summary.layout = "ancho (una columna por analito)"
            summary.analytes = numeric

    if time_col:
        summary.timepoints = sorted(
            df[time_col].dropna().astype(str).str.strip().unique(), key=_timepoint_day
        )
    if subject_col:
        summary.subjects = sorted(df[subject_col].dropna().astype(str).unique())
    return summary


# ---------------------------------------------------------------------------
# Formato normalizado (base de la calibración)
# ---------------------------------------------------------------------------

_TIMEPOINT_RE = re.compile(r"^([LR])\s*([+-])\s*(\d+)$", re.IGNORECASE)
_SEX_SUFFIX_RE = re.compile(r"\s*\((FEMALE|MALE)\)\s*$", re.IGNORECASE)

TIDY_COLUMNS = [
    "dataset", "analyte", "value", "unit", "subject", "sex", "timepoint", "day", "phase",
    "range_min", "range_max",
]


def _timepoint_day(timepoint: str) -> int:
    """Día relativo al vuelo: `L-92` → -92 (antes del lanzamiento), `R+45` → 45 (tras el regreso)."""
    match = _TIMEPOINT_RE.match(timepoint.strip())
    if not match:
        return 0
    _, sign, number = match.groups()
    return -int(number) if sign == "-" else int(number)


def _phase(timepoint: str) -> str:
    """`preflight` (L-) o `postflight` (R+); `unknown` si no se reconoce."""
    match = _TIMEPOINT_RE.match(timepoint.strip())
    if not match:
        return "unknown"
    return "preflight" if match.group(1).upper() == "L" else "postflight"


def normalize_analyte(name: str) -> str:
    """`HEMOGLOBIN (FEMALE)` → `HEMOGLOBIN`: el sufijo es solo el rango por sexo."""
    return _SEX_SUFFIX_RE.sub("", str(name)).strip().upper()


def load_long(path: Path) -> pd.DataFrame:
    """Lee un CSV largo de OSDR (una fila por analito) en un formato limpio y común.

    Limpieza aplicada (sin inventar valores):
    - nombres de columna detectados, no supuestos;
    - espacios sobrantes en los puntos de tiempo;
    - sufijos `(FEMALE)`/`(MALE)` unificados con el analito base;
    - valores no numéricos descartados;
    - filas duplicadas exactas eliminadas.
    """
    df = load_csv(path)
    columns = [str(c) for c in df.columns]
    found = {
        "analyte": _find_column(columns, "analyte"),
        "value": next((c for c in columns if c.strip().lower() == "value"), None),
        "unit": _find_column(columns, "unit"),
        "range_min": _find_column(columns, "range_min"),
        "range_max": _find_column(columns, "range_max"),
        "subject": _find_column(columns, "subject"),
        "sex": next((c for c in columns if c.strip().lower() == "sex"), None),
        "timepoint": _find_column(columns, "timepoint", "time_point", "test_date"),
    }
    missing = [k for k in ("analyte", "value", "subject", "timepoint") if found[k] is None]
    if missing:
        raise ValueError(f"{path.name}: no tiene formato largo (faltan {missing})")

    tidy = pd.DataFrame(
        {
            key: df[col] if col is not None else pd.NA
            for key, col in found.items()
        }
    )
    tidy["dataset"] = dataset_of(path)
    tidy["range_min"] = pd.to_numeric(tidy["range_min"], errors="coerce")
    tidy["range_max"] = pd.to_numeric(tidy["range_max"], errors="coerce")
    tidy["analyte"] = tidy["analyte"].map(normalize_analyte)
    tidy["value"] = pd.to_numeric(tidy["value"], errors="coerce")
    tidy["timepoint"] = tidy["timepoint"].astype(str).str.replace(r"\s+", "", regex=True)
    tidy["subject"] = tidy["subject"].astype(str).str.strip()
    tidy["day"] = tidy["timepoint"].map(_timepoint_day)
    tidy["phase"] = tidy["timepoint"].map(_phase)
    tidy = tidy.dropna(subset=["value"]).drop_duplicates()
    return tidy[TIDY_COLUMNS].reset_index(drop=True)


def load_all_long(folder: Path = OSDR_RAW_DIR) -> pd.DataFrame:
    """Une todos los CSV largos de `data/osdr/raw/` (los `TRANSFORMED` repiten los datos)."""
    frames = []
    for path in raw_files(folder):
        try:
            frames.append(load_long(path))
        except ValueError:
            continue  # formato ancho (TRANSFORMED): mismos datos, se omite
    if not frames:
        return pd.DataFrame(columns=TIDY_COLUMNS)
    return pd.concat(frames, ignore_index=True)
