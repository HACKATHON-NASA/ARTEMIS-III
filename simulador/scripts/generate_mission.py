"""Genera una misión completa y la guarda en data/output/ (JSON + CSV).

Uso:
    python scripts/generate_mission.py --env mars --days 30 --seed 42
    python scripts/generate_mission.py --env iss --days 60 --no-real-weather
"""

import argparse
import sys
import time
from datetime import date
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parents[1] / "src"))

from simulador.engine.score import assess  # noqa: E402
from simulador.exporters.json_csv import export_csv, export_json  # noqa: E402
from simulador.generator.seed import generate_mission_sync  # noqa: E402
from simulador.models import Environment, MissionRequest, Status  # noqa: E402

ICON = {Status.GREEN: "🟢", Status.YELLOW: "🟡", Status.RED: "🔴", None: "⚪"}


def main() -> int:
    """Genera, guarda e imprime un resumen."""
    p = argparse.ArgumentParser(description="Generador de misión AstroHealth")
    p.add_argument("--name", default="Ares-1")
    p.add_argument("--env", default="mars", choices=[e.value for e in Environment])
    p.add_argument("--days", type=int, default=30)
    p.add_argument("--seed", type=int, default=42)
    p.add_argument("--crew", type=int, default=4)
    p.add_argument("--start", type=date.fromisoformat, help="yyyy-mm-dd (por defecto termina hoy)")
    p.add_argument("--no-real-weather", action="store_true", help="no consultar DONKI")
    p.add_argument("--no-demo-events", action="store_true", help="sin eventos de la demo")
    p.add_argument("--export", default="json", choices=["json", "none"])
    args = p.parse_args()

    request = MissionRequest(
        mission_name=args.name, environment=args.env, days=args.days, start_date=args.start,
        crew_size=args.crew, seed=args.seed, use_real_space_weather=not args.no_real_weather,
        inject_demo_events=not args.no_demo_events,
    )

    t0 = time.perf_counter()
    result = generate_mission_sync(request)
    elapsed = time.perf_counter() - t0

    m = result.mission
    print(f"\n🚀 Misión {m.name} · {m.environment.value} · {m.days} días desde {m.start_date}")
    print(f"   {len(result.crew)} astronautas · {len(result.measurements)} mediciones · "
          f"{len(result.alerts)} alertas · {elapsed * 1000:.0f} ms")
    print(f"   calibración: {result.meta.calibration_file or '⚠️ sin calibración (referencia)'}")

    sources: dict[str, int] = {}
    for x in result.space_weather:
        sources[x.source.value] = sources.get(x.source.value, 0) + 1
    seps = [x for x in result.space_weather if x.sep_event]
    print(f"   clima espacial: {sources}")
    for x in seps:
        print(f"   ☢️  SEP el {x.date} (día {(x.date - m.start_date).days + 1}) · {x.source.value}")

    print("\n👩‍🚀 Estado final de cada astronauta (último día):")
    last_day = result.space_weather[-1].date
    for member in result.crew:
        mine = [x for x in result.measurements if x.astronaut_id == member.id]
        final = assess([x for x in mine if x.measured_at.date() == last_day], m.environment)
        counts = {s: sum(x.status == s for x in mine) for s in Status}
        print(f"   {member.id}. {member.name:<16} {member.role:<22} puntaje {final.score:>3}/100 · "
              f"🟢{counts[Status.GREEN]} 🟡{counts[Status.YELLOW]} 🔴{counts[Status.RED]}")

    print("\n🚨 Alertas:")
    names = {c.id: c.name.split()[0] for c in result.crew}
    for a in result.alerts:
        day = (a.measured_at.date() - m.start_date).days + 1
        kind = "📈 tendencia" if a.trend_alert else "🔴 rojo     "
        ground = f" → Tierra en {a.ground_delay_seconds / 60:.0f} min" if a.notify_ground else ""
        print(f"   día {day:>2} · {names[a.astronaut_id]:<7} {kind} {a.message[:90]}{ground}")

    if args.export == "json":
        paths = [export_json(result), *export_csv(result)]
        print("\n💾 Guardado:")
        for path in paths:
            print(f"   {path}")
    print()
    return 0


if __name__ == "__main__":
    sys.exit(main())
