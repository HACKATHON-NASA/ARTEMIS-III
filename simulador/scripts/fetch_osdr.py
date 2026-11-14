"""Descarga los CSV clínicos de Inspiration4 (OSDR) y muestra su contenido real.

Uso:
    python scripts/fetch_osdr.py              # descarga (si falta) e inspecciona
    python scripts/fetch_osdr.py --offline    # solo inspecciona data/osdr/raw/
    python scripts/fetch_osdr.py --overwrite  # vuelve a descargar
"""

import argparse
import asyncio
import sys
import time
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parents[1] / "src"))

from simulador.collectors.osdr import (  # noqa: E402
    download_clinical,
    inspect_file,
    load_all_long,
    raw_files,
)
from simulador.settings import OSDR_RAW_DIR  # noqa: E402


def _short(items: list[str], limit: int = 40) -> str:
    text = ", ".join(items[:limit])
    return text + (f" … (+{len(items) - limit})" if len(items) > limit else "")


async def main() -> int:
    """Descarga e inspecciona."""
    parser = argparse.ArgumentParser(description="Datos clínicos de Inspiration4 (NASA OSDR)")
    parser.add_argument("--offline", action="store_true", help="no descargar, solo inspeccionar")
    parser.add_argument("--overwrite", action="store_true", help="volver a descargar")
    args = parser.parse_args()

    if not args.offline:
        t0 = time.perf_counter()
        try:
            paths = await download_clinical(overwrite=args.overwrite)
        except Exception as exc:  # noqa: BLE001 - se informa y se sigue con lo local
            print(f"⚠️  No se pudo descargar de OSDR ({type(exc).__name__}). Se usa lo local.")
        else:
            print(f"⬇️  OSDR: {len(paths)} archivos clínicos en {time.perf_counter() - t0:.1f} s")

    files = raw_files()
    if not files:
        print(f"❌ No hay CSV en {OSDR_RAW_DIR}. Póngalos a mano o corra sin --offline.")
        return 1

    for path in files:
        s = inspect_file(path)
        print(f"\n📄 {path.name}  [{s.dataset}]  {s.rows} filas · formato {s.layout}")
        if s.layout.startswith("ancho"):
            print("   (mismos datos que el CSV largo, en columnas: no se usa para calibrar)")
            continue
        print(f"   columnas ({len(s.columns)}): {_short(s.columns, 25)}")
        print(f"   sujetos ({len(s.subjects)}): {_short(s.subjects)}")
        print(f"   tiempos ({len(s.timepoints)}): {_short(s.timepoints)}")
        print(f"   analitos ({len(s.analytes)}): {_short(s.analytes)}")

    tidy = load_all_long()
    print(f"\n🧪 Datos limpios: {len(tidy)} mediciones de {tidy['subject'].nunique()} astronautas")
    print(f"   {'analito':<32} {'unidad':<12} {'estudio':<8} {'pre':>4} {'post':>4} {'suj':>4}")
    for (analyte, dataset), group in tidy.groupby(["analyte", "dataset"]):
        units = ", ".join(sorted(group["unit"].dropna().astype(str).unique()))
        pre = (group["phase"] == "preflight").sum()
        post = (group["phase"] == "postflight").sum()
        print(
            f"   {analyte[:32]:<32} {units[:12]:<12} {dataset:<8} {pre:>4} {post:>4}"
            f" {group['subject'].nunique():>4}"
        )
    return 0


if __name__ == "__main__":
    sys.exit(asyncio.run(main()))
