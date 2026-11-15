"""Trae el clima espacial real de DONKI y lo muestra por día.

Uso:
    python scripts/fetch_donki.py --days 30
    python scripts/fetch_donki.py --start 2026-10-01 --end 2026-10-31
"""

import argparse
import asyncio
import sys
import time
from datetime import date, timedelta
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parents[1] / "src"))

from simulador.collectors.donki import (  # noqa: E402
    EVENT_TYPES,
    DonkiClient,
    DonkiUnavailableError,
    daily_space_weather,
    latest_notifications,
)

ICONS = {"low": "🟢", "medium": "🟡", "high": "🔴"}


def _args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="Clima espacial real (NASA DONKI)")
    parser.add_argument("--days", type=int, default=30, help="días hacia atrás desde hoy")
    parser.add_argument("--start", type=date.fromisoformat, help="fecha inicial yyyy-mm-dd")
    parser.add_argument("--end", type=date.fromisoformat, help="fecha final yyyy-mm-dd")
    parser.add_argument("--all", action="store_true", help="mostrar también los días tranquilos")
    return parser.parse_args()


async def main() -> int:
    """Consulta DONKI, imprime el resumen y mide la velocidad."""
    args = _args()
    end = args.end or date.today()
    start = args.start or end - timedelta(days=args.days - 1)
    client = DonkiClient()

    print(f"\n🛰️  DONKI · {start} → {end}")
    print(f"   key: {'DEMO_KEY (límite bajo)' if client.settings.usa_demo_key else 'propia ✅'}")

    t0 = time.perf_counter()
    try:
        result = await client.fetch_all(start, end)
    except DonkiUnavailableError as exc:
        print(f"\n❌ DONKI no disponible: {exc}")
        return 1
    first = time.perf_counter() - t0

    t0 = time.perf_counter()
    await client.fetch_all(start, end)
    second = time.perf_counter() - t0

    print("\n📦 Eventos recibidos:")
    for event_type in EVENT_TYPES:
        mark = " ⚠️ sin datos frescos" if event_type in result.stale_types else ""
        if event_type in result.derived:
            mark = f" (leído de {result.derived[event_type]}: el endpoint de la NASA no responde)"
        print(f"   {event_type:<14} {len(result.events[event_type]):>4}{mark}")

    days = daily_space_weather(result.events, start, end, result.stale)
    shown = days if args.all else [d for d in days if d.risk_level != "low" or d.events]
    print(f"\n☀️  Días con actividad ({len(shown)} de {len(days)}):")
    print(f"   {'fecha':<11} {'riesgo':<9} {'×rad':>4}  {'fulg.':<6} {'SEP':<4} {'Kp':>4}  eventos")
    for day in shown:
        kp = f"{day.geomagnetic_kp_max:.1f}" if day.geomagnetic_kp_max is not None else "-"
        print(
            f"   {day.date}  {ICONS[day.risk_level]} {day.risk_level:<6} {day.radiation_multiplier:>4}"
            f"  {day.flare_max_class or '-':<6} {'sí' if day.sep_event else '-':<4} {kp:>4}  {len(day.events)}"
        )

    counts = {level: sum(d.risk_level == level for d in days) for level in ICONS}
    print(f"\n📊 Resumen: 🔴 {counts['high']} · 🟡 {counts['medium']} · 🟢 {counts['low']} días")

    notes = latest_notifications(result.events, limit=5)
    if notes:
        print("\n📰 Últimas notificaciones:")
        for note in notes:
            print(f"   {note['issued_at']}  {note['type']}")

    print(f"\n⚡ Velocidad: NASA en vivo {first:.2f} s · desde memoria {second * 1000:.2f} ms\n")
    return 0


if __name__ == "__main__":
    sys.exit(asyncio.run(main()))
