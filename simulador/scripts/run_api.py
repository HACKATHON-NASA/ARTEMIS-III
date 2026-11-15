"""Arranca la API del simulador.

Uso:
    python scripts/run_api.py            # http://localhost:8001/docs
    python scripts/run_api.py --reload   # recarga al guardar (desarrollo)
"""

import argparse
import sys
from pathlib import Path

SRC = Path(__file__).resolve().parents[1] / "src"
sys.path.insert(0, str(SRC))

import uvicorn  # noqa: E402

from simulador.settings import get_settings  # noqa: E402


def main() -> None:
    """Lee host y puerto del `.env` y levanta uvicorn."""
    parser = argparse.ArgumentParser(description="API del simulador AstroHealth")
    parser.add_argument("--reload", action="store_true", help="recargar al cambiar el código")
    args = parser.parse_args()

    settings = get_settings()
    print(f"🛰️  API en http://localhost:{settings.api_port}/docs")
    uvicorn.run(
        "simulador.api.main:app",
        host=settings.api_host,
        port=settings.api_port,
        reload=args.reload,
        reload_dirs=[str(SRC)] if args.reload else None,
        app_dir=str(SRC),
    )


if __name__ == "__main__":
    main()
