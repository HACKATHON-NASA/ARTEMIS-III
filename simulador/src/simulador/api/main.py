"""API REST del simulador (FastAPI) — SIMULADOR.md §8.

Base: `http://localhost:8001/api/v1` · documentación automática en `/docs`.

Respuestas: `{"data": ..., "message": "ok"}`.
Errores:    `{"data": null, "message": "...", "error_code": "..."}`.

Al arrancar se precarga el clima espacial de los últimos 30 días en memoria,
para que la primera simulación ya responda rápido.
"""

import asyncio
import logging
from collections.abc import AsyncIterator
from contextlib import asynccontextmanager
from datetime import date, timedelta
from typing import Any

from fastapi import APIRouter, FastAPI, Query, Request
from fastapi.exceptions import RequestValidationError
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import JSONResponse
from starlette.exceptions import HTTPException as StarletteHTTPException

from simulador import AVISO_MEDICO, __version__
from simulador.calibration.osdr_calibration import load_calibration
from simulador.collectors.donki import (
    DonkiUnavailableError,
    daily_space_weather,
    get_donki_client,
    latest_notifications,
)
from simulador.config import all_environments
from simulador.generator.seed import generate_mission
from simulador.models import (
    AnalyteCalibration,
    ApiError,
    ApiResponse,
    MissionRequest,
    MissionResult,
    SpaceWeatherDay,
)
from simulador.settings import get_settings

logger = logging.getLogger(__name__)

MAX_RANGE_DAYS = 366
PREWARM_DAYS = 30


class ApiProblem(Exception):
    """Error controlado de la API, con su código."""

    def __init__(self, status_code: int, error_code: str, message: str) -> None:
        super().__init__(message)
        self.status_code = status_code
        self.error_code = error_code
        self.message = message


def ok(data: Any) -> dict[str, Any]:
    """Sobre de respuesta correcta."""
    return {"data": data, "message": "ok"}


def _error(status_code: int, error_code: str, message: str) -> JSONResponse:
    body = ApiError(message=message, error_code=error_code).model_dump()
    return JSONResponse(status_code=status_code, content=body)


def _date_range(start: date | None, end: date | None) -> tuple[date, date]:
    end = end or date.today()
    start = start or end - timedelta(days=PREWARM_DAYS - 1)
    if start > end:
        raise ApiProblem(422, "INVALID_DATE_RANGE", "start debe ser anterior o igual a end")
    if (end - start).days + 1 > MAX_RANGE_DAYS:
        raise ApiProblem(422, "INVALID_DATE_RANGE", f"El rango máximo es de {MAX_RANGE_DAYS} días")
    return start, end


# ---------------------------------------------------------------------------
# Rutas
# ---------------------------------------------------------------------------

router = APIRouter(prefix="/api/v1")


@router.get("/health", summary="Estado del servicio")
async def health() -> ApiResponse[dict[str, Any]]:
    """Estado, última actualización de DONKI y si hay calibración OSDR cargada."""
    donki = get_donki_client()
    try:
        calibration = load_calibration()
    except FileNotFoundError:
        calibration = {}
    return ApiResponse(
        data={
            "status": "ok",
            "version": __version__,
            "disclaimer": AVISO_MEDICO,
            "donki_last_update": donki.last_updated.isoformat() if donki.last_updated else None,
            "calibration_loaded": bool(calibration),
            "calibration_analytes": len(calibration),
            "using_demo_key": get_settings().usa_demo_key,
        }
    )


@router.get("/space-weather", summary="Clima espacial real por día (DONKI)")
async def space_weather(
    start: date | None = Query(None, description="yyyy-mm-dd (por defecto: hace 30 días)"),
    end: date | None = Query(None, description="yyyy-mm-dd (por defecto: hoy)"),
) -> ApiResponse[list[SpaceWeatherDay]]:
    """Un día por fila: riesgo, multiplicador de radiación y eventos de la NASA."""
    start, end = _date_range(start, end)
    result = await get_donki_client().fetch_all(start, end)
    return ApiResponse(data=daily_space_weather(result.events, start, end, result.stale))


@router.get("/space-weather/now", summary="Riesgo de hoy (banner de la app)")
async def space_weather_now() -> ApiResponse[dict[str, Any]]:
    """Riesgo de hoy, eventos activos y últimas notificaciones de la NASA."""
    end = date.today()
    start = end - timedelta(days=PREWARM_DAYS - 1)  # misma ventana que la precarga
    result = await get_donki_client().fetch_all(start, end)
    days = daily_space_weather(result.events, start, end, result.stale)
    today = days[-1]
    return ApiResponse(
        data={
            "today": today.model_dump(mode="json"),
            "active_events": today.events,
            "last_7_days_high_risk": [
                d.date.isoformat() for d in days[-7:] if d.risk_level != "low"
            ],
            "notifications": latest_notifications(result.events, limit=5),
            "flares_from": result.derived.get("FLR", "FLR"),
            "stale": result.stale,
        }
    )


@router.get("/calibration", summary="Calibración con datos reales de la NASA (OSDR)")
async def calibration() -> ApiResponse[dict[str, AnalyteCalibration]]:
    """Media y desviación pre-vuelo, y cambio al regresar, de cada analito de Inspiration4."""
    try:
        return ApiResponse(data=load_calibration())
    except FileNotFoundError as exc:
        raise ApiProblem(404, "CALIBRATION_NOT_FOUND", str(exc)) from exc


@router.post("/simulate", summary="Genera una misión completa")
async def simulate(request: MissionRequest) -> ApiResponse[MissionResult]:
    """Tripulación, clima espacial y valores crudos de los aparatos. Reproducible con `seed`."""
    return ApiResponse(data=await generate_mission(request))


@router.get("/environments", summary="Los 4 entornos de misión")
async def environments() -> ApiResponse[list[dict[str, Any]]]:
    """ISS, tránsito, Luna y Marte: gravedad, dosis base y retraso de comunicación."""
    return ApiResponse(data=[{"id": key, **value} for key, value in all_environments().items()])


# ---------------------------------------------------------------------------
# Aplicación
# ---------------------------------------------------------------------------


async def _prewarm() -> None:
    end = date.today()
    try:
        await get_donki_client().fetch_all(end - timedelta(days=PREWARM_DAYS - 1), end)
        logger.info("DONKI precargado en memoria")
    except Exception as exc:  # noqa: BLE001 - la API debe arrancar igual
        logger.warning("No se pudo precargar DONKI: %s", type(exc).__name__)


def create_app(prewarm: bool = True) -> FastAPI:
    """Crea la aplicación. `prewarm=False` en los tests (sin internet)."""

    @asynccontextmanager
    async def lifespan(_: FastAPI) -> AsyncIterator[None]:
        task = asyncio.create_task(_prewarm()) if prewarm else None
        yield
        if task and not task.done():
            task.cancel()

    app = FastAPI(
        title="AstroHealth · Simulador",
        version=__version__,
        description=(
            "Datos reales de la NASA (DONKI y OSDR) y misión simulada calibrada: "
            f"valores crudos de los aparatos, sin evaluar. **{AVISO_MEDICO}**"
        ),
        lifespan=lifespan,
    )
    app.add_middleware(
        CORSMiddleware,
        allow_origins=get_settings().cors_origins,
        allow_methods=["*"],
        allow_headers=["*"],
    )
    app.include_router(router)

    @app.get("/", include_in_schema=False)
    async def root() -> dict[str, Any]:
        return ok({"docs": "/docs", "api": "/api/v1", "disclaimer": AVISO_MEDICO})

    @app.exception_handler(ApiProblem)
    async def _problem(_: Request, exc: ApiProblem) -> JSONResponse:
        return _error(exc.status_code, exc.error_code, exc.message)

    @app.exception_handler(DonkiUnavailableError)
    async def _donki(_: Request, exc: DonkiUnavailableError) -> JSONResponse:
        return _error(503, "DONKI_UNAVAILABLE", f"NASA DONKI no disponible: {exc}")

    @app.exception_handler(RequestValidationError)
    async def _validation(_: Request, exc: RequestValidationError) -> JSONResponse:
        first = exc.errors()[0] if exc.errors() else {}
        where = ".".join(str(p) for p in first.get("loc", []) if p != "body")
        return _error(422, "VALIDATION_ERROR", f"{where}: {first.get('msg', 'dato inválido')}")

    @app.exception_handler(StarletteHTTPException)
    async def _http(_: Request, exc: StarletteHTTPException) -> JSONResponse:
        code = "NOT_FOUND" if exc.status_code == 404 else f"HTTP_{exc.status_code}"
        return _error(exc.status_code, code, str(exc.detail))

    @app.exception_handler(Exception)
    async def _unexpected(_: Request, exc: Exception) -> JSONResponse:
        logger.exception("Error inesperado")
        return _error(500, "INTERNAL_ERROR", "Error interno del simulador")

    return app


app = create_app()
