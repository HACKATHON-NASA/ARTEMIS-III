"""Calcula `data/osdr/processed/calibration.json` con los datos reales de Inspiration4.

Uso:
    python scripts/build_calibration.py
"""

import sys
import time
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parents[1] / "src"))

from simulador.calibration.osdr_calibration import CALIBRATION_FILE, build_calibration  # noqa: E402

HIGHLIGHT = {"glucose", "hemoglobin", "hematocrit", "calcium", "white_blood_cell_count",
             "platelet_count", "creatinine"}


def main() -> int:
    """Calibra e imprime el resultado."""
    t0 = time.perf_counter()
    try:
        report = build_calibration()
    except FileNotFoundError as exc:
        print(f"❌ {exc}")
        return 1
    elapsed = (time.perf_counter() - t0) * 1000

    print(f"\n🧪 Calibración OSDR · {len(report.analytes)} analitos · {elapsed:.0f} ms")
    print(f"   → {CALIBRATION_FILE}\n")
    print(f"   {'analito':<28} {'unidad':<12} {'media pre':>10} {'± sd':>8} {'post':>9} {'Δ %':>7}  n  punto")
    for key, cal in report.analytes.items():
        star = "⭐" if key in HIGHLIGHT else "  "
        print(
            f"{star} {key[:28]:<28} {cal.unit[:12]:<12} {cal.baseline_mean:>10.2f}"
            f" {cal.baseline_sd:>8.2f} {cal.postflight_mean:>9.2f} {cal.delta_pct:>+7.1f}"
            f"  {cal.n_subjects}  {cal.postflight_timepoint}"
        )
    for note in report.excluded_samples:
        print(f"   🚫 muestra excluida por error de captura: {note}")
    for key, reason in report.skipped.items():
        print(f"   ⚠️ omitido {key}: {reason}")
    print("\n⭐ = los que usará el generador (glucosa y, para mostrar, sangre)\n")
    return 0


if __name__ == "__main__":
    sys.exit(main())
