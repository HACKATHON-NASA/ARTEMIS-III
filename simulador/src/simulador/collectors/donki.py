"""Colector de NASA DONKI: clima espacial real → riesgo de radiación por día.

Diseño para que sea rápido:
- Lectura en vivo, sin archivos ni base de datos.
- Los 5 tipos de evento se piden en paralelo (`asyncio.gather`).
- Las respuestas se guardan en memoria durante `CACHE_TTL_MINUTES`, para no
  agotar el límite de la API key y responder al instante las consultas repetidas.
- Si la NASA falla: se reintenta, luego se usa el servidor de respaldo (sin key)
  y, como último recurso, lo último que llegó, marcado `stale`.

La API key nunca se escribe en los logs ni en los mensajes de error.
"""

import asyncio
import logging
import re
import time
from dataclasses import dataclass, field
from datetime import date, datetime, timedelta, timezone
from typing import Any

import httpx

from simulador.models import RiskLevel, Source, SpaceWeatherDay
from simulador.settings import Settings, get_settings

logger = logging.getLogger(__name__)
# httpx escribe la URL completa (con api_key) en nivel INFO: se silencia.
logging.getLogger("httpx").setLevel(logging.WARNING)

EVENT_TYPES: tuple[str, ...] = ("FLR", "SEP", "GST", "CME", "notifications")
"""Tipos de evento de DONKI que usa el simulador."""

_ID_FIELDS: dict[str, str] = {
    "FLR": "flrID",
    "SEP": "sepID",
    "GST": "gstID",
    "CME": "activityID",
    "notifications": "messageID",
}
_TIME_FIELDS: dict[str, tuple[str, ...]] = {
    "FLR": ("peakTime", "beginTime"),
    "SEP": ("eventTime",),
    "GST": ("startTime",),
    "CME": ("startTime",),
    "notifications": ("messageIssueTime",),
}
_FLARE_ORDER: dict[str, int] = {"A": 0, "B": 1, "C": 2, "M": 3, "X": 4}
_FLARE_CLASS_RE = re.compile(r"\b([CMX]\d+(?:\.\d+)?)\b")
_MAX_RETRIES = 3


class DonkiUnavailableError(Exception):
    """DONKI no respondió y no hay datos previos en memoria."""


@dataclass
class DonkiResult:
    """Eventos crudos de DONKI por tipo, con su estado de frescura."""

    events: dict[str, list[dict[str, Any]]]
    stale: bool = False
    stale_types: list[str] = field(default_factory=list)
    derived: dict[str, str] = field(default_factory=dict)
    """Tipos reconstruidos desde otro endpoint de DONKI, p. ej. `{"FLR": "notifications"}`."""


@dataclass
class _MemoryEntry:
    events: list[dict[str, Any]]
    fetched_at: float


class DonkiClient:
    """Cliente de DONKI con memoria de corta duración."""

    def __init__(self, settings: Settings | None = None) -> None:
        """Crea el cliente con la configuración dada (o la del `.env`)."""
        self.settings = settings or get_settings()
        self._memory: dict[tuple[str, date, date], _MemoryEntry] = {}
        self._results: dict[tuple[date, date], tuple[DonkiResult, float]] = {}
        self.last_updated: datetime | None = None
        """Última vez que DONKI respondió con datos frescos (UTC)."""
        # Hasta cuándo no se insiste con algo que falló (evita esperas repetidas).
        self._failed_until: dict[str, float] = {}

    def clear_memory(self) -> None:
        """Borra lo guardado en memoria (útil en tests)."""
        self._memory.clear()
        self._results.clear()
        self._failed_until.clear()

    def _timeout(self) -> httpx.Timeout:
        return httpx.Timeout(
            self.settings.http_timeout_seconds,
            connect=self.settings.http_connect_timeout_seconds,
        )

    def _is_down(self, name: str) -> bool:
        return time.monotonic() < self._failed_until.get(name, 0.0)

    def _mark_down(self, name: str) -> None:
        self._failed_until[name] = time.monotonic() + self.settings.cache_ttl_minutes * 60

    # ------------------------------------------------------------------ HTTP

    async def _get_json(
        self,
        client: httpx.AsyncClient,
        url: str,
        params: dict[str, str],
        retries: int = _MAX_RETRIES,
    ) -> list[dict[str, Any]]:
        """GET con reintentos. Devuelve `[]` si DONKI responde vacío."""
        last_error = "sin respuesta"
        for attempt in range(1, retries + 1):
            try:
                response = await client.get(url, params=params)
                if response.status_code == 429:
                    # Límite de la key agotado: reintentar no sirve.
                    raise DonkiUnavailableError("límite de la API key (429)")
                response.raise_for_status()
                body = response.text.strip()
                if not body:
                    return []
                data = response.json()
                return data if isinstance(data, list) else []
            except DonkiUnavailableError:
                raise
            except httpx.HTTPStatusError as exc:
                last_error = f"HTTP {exc.response.status_code}"
                if exc.response.status_code < 500:
                    break  # error del cliente: no se reintenta
            except httpx.ConnectError as exc:
                last_error = type(exc).__name__
                break  # servidor inalcanzable: reintentar solo hace esperar
            except httpx.ConnectTimeout as exc:
                last_error = type(exc).__name__
                break
            except (httpx.TransportError, ValueError) as exc:
                last_error = type(exc).__name__  # sin la URL (lleva la key)
            if attempt < retries:
                await asyncio.sleep(0.3 * attempt)
        raise DonkiUnavailableError(last_error)

    async def _fetch_remote(
        self, client: httpx.AsyncClient, event_type: str, start: date, end: date
    ) -> list[dict[str, Any]]:
        """Pide un tipo de evento a DONKI; si falla, al servidor de respaldo."""
        params = {"startDate": start.isoformat(), "endDate": end.isoformat()}
        if event_type == "notifications":
            params["type"] = "all"

        primary = f"primary:{event_type}"
        if not self._is_down(primary):
            try:
                return await self._get_json(
                    client,
                    f"{self.settings.donki_base_url}/{event_type}",
                    {**params, "api_key": self.settings.nasa_api_key.get_secret_value()},
                )
            except DonkiUnavailableError as exc:
                self._mark_down(primary)
                if not self.settings.donki_fallback_url:
                    raise
                logger.warning("DONKI %s falló (%s); probando el respaldo", event_type, exc)

        if not self.settings.donki_fallback_url:
            raise DonkiUnavailableError("DONKI no disponible (falló hace poco)")
        if self._is_down("fallback"):
            raise DonkiUnavailableError("respaldo no disponible (falló hace poco)")
        try:
            # Un solo intento: si el respaldo está caído no vale la pena esperar.
            return await self._get_json(
                client, f"{self.settings.donki_fallback_url}/{event_type}", params, retries=1
            )
        except DonkiUnavailableError as exc:
            if "Connect" in str(exc):
                self._mark_down("fallback")
            raise

    # ------------------------------------------------------------- Públicas

    async def fetch_events(
        self,
        event_type: str,
        start: date,
        end: date,
        client: httpx.AsyncClient | None = None,
    ) -> tuple[list[dict[str, Any]], bool]:
        """Trae los eventos de un tipo. Devuelve `(eventos, stale)`."""
        if event_type not in EVENT_TYPES:
            raise ValueError(f"Tipo de evento DONKI desconocido: {event_type}")

        key = (event_type, start, end)
        entry = self._memory.get(key)
        ttl = self.settings.cache_ttl_minutes * 60
        if entry and time.monotonic() - entry.fetched_at < ttl:
            return entry.events, False

        own_client = client is None
        client = client or httpx.AsyncClient(timeout=self._timeout())
        try:
            events = await self._fetch_remote(client, event_type, start, end)
        except DonkiUnavailableError:
            if entry:
                logger.warning("DONKI %s no disponible; se usa el último dato", event_type)
                return entry.events, True
            raise
        finally:
            if own_client:
                await client.aclose()

        self._memory[key] = _MemoryEntry(events=events, fetched_at=time.monotonic())
        return events, False

    async def fetch_all(self, start: date, end: date) -> DonkiResult:
        """Trae los 5 tipos de evento en paralelo.

        Si un tipo falla y no hay nada en memoria, ese tipo queda vacío y
        marcado como `stale` (no tumba todo lo demás).
        La respuesta completa queda en memoria: repetirla no abre conexiones.
        """
        cached = self._results.get((start, end))
        if cached and time.monotonic() < cached[1]:
            return cached[0]

        result = await self._fetch_all_remote(start, end)
        if not result.stale:
            self.last_updated = datetime.now(timezone.utc)
        # Un resultado incompleto se guarda poco tiempo para reintentar pronto.
        ttl = 60 if result.stale else self.settings.cache_ttl_minutes * 60
        self._results[(start, end)] = (result, time.monotonic() + ttl)
        return result

    async def _fetch_all_remote(self, start: date, end: date) -> DonkiResult:
        """Consulta DONKI (los tipos que no estén en memoria) en paralelo."""
        async with httpx.AsyncClient(timeout=self._timeout()) as client:
            results = await asyncio.gather(
                *(self.fetch_events(t, start, end, client) for t in EVENT_TYPES),
                return_exceptions=True,
            )

        events: dict[str, list[dict[str, Any]]] = {}
        stale_types: list[str] = []
        for event_type, result in zip(EVENT_TYPES, results, strict=True):
            if isinstance(result, BaseException):
                logger.error("DONKI %s sin datos: %s", event_type, result)
                events[event_type] = []
                stale_types.append(event_type)
            else:
                events[event_type], stale = result
                if stale:
                    stale_types.append(event_type)

        if len(stale_types) == len(EVENT_TYPES) and not any(events.values()):
            raise DonkiUnavailableError("DONKI y su respaldo no respondieron")

        derived: dict[str, str] = {}
        failed_flr = "FLR" in stale_types and not events["FLR"]
        if failed_flr and "notifications" not in stale_types:
            # El endpoint FLR de api.nasa.gov responde 404 (nov. 2026): las
            # fulguraciones se leen de las notificaciones FLR de DONKI.
            events["FLR"] = flares_from_notifications(events["notifications"])
            stale_types.remove("FLR")
            derived["FLR"] = "notifications"

        return DonkiResult(
            events=events, stale=bool(stale_types), stale_types=stale_types, derived=derived
        )


# ---------------------------------------------------------------------------
# Transformación a riesgo diario
# ---------------------------------------------------------------------------


def flares_from_notifications(notifications: list[dict[str, Any]]) -> list[dict[str, Any]]:
    """Reconstruye eventos FLR a partir de las notificaciones FLR de DONKI.

    Las notificaciones dicen, p. ej., "Flare intensity has crossed the
    threshold of M5.0": se toma la clase más alta mencionada en el mensaje.
    """
    flares: list[dict[str, Any]] = []
    for note in notifications:
        if note.get("messageType") != "FLR":
            continue
        classes = _FLARE_CLASS_RE.findall(note.get("messageBody") or "")
        if not classes:
            continue
        flares.append(
            {
                "flrID": note.get("messageID"),
                "classType": max(classes, key=_flare_rank),
                "peakTime": note.get("messageIssueTime"),
                "derivedFrom": "notifications",
            }
        )
    return flares


def _event_day(event: dict[str, Any], event_type: str) -> date | None:
    """Fecha (UTC) del evento según su campo de tiempo."""
    for name in _TIME_FIELDS[event_type]:
        value = event.get(name)
        if isinstance(value, str) and len(value) >= 10:
            try:
                return date.fromisoformat(value[:10])
            except ValueError:
                continue
    return None


def _flare_rank(flare_class: str | None) -> tuple[int, float]:
    """Orden de una clase de fulguración (`X1.2` > `M9.0` > `C3.1`)."""
    if not flare_class:
        return (-1, 0.0)
    letter = flare_class[0].upper()
    try:
        magnitude = float(flare_class[1:])
    except ValueError:
        magnitude = 0.0
    return (_FLARE_ORDER.get(letter, -1), magnitude)


def classify_risk(
    sep_event: bool, flare_max_class: str | None, kp_max: float | None
) -> tuple[RiskLevel, float]:
    """Reglas de SIMULADOR.md §4 → `(risk_level, radiation_multiplier)`."""
    letter = flare_max_class[0].upper() if flare_max_class else ""
    if sep_event:
        return RiskLevel.HIGH, 3.0
    if letter == "X":
        return RiskLevel.HIGH, 2.0
    if letter == "M" or (kp_max is not None and kp_max >= 6):
        return RiskLevel.MEDIUM, 1.5
    return RiskLevel.LOW, 1.0


def daily_space_weather(
    events: dict[str, list[dict[str, Any]]],
    start: date,
    end: date,
    stale: bool = False,
) -> list[SpaceWeatherDay]:
    """Convierte los eventos de DONKI en un `SpaceWeatherDay` por cada día del rango."""
    days = (end - start).days + 1
    flares: dict[date, str] = {}
    seps: set[date] = set()
    kp: dict[date, float] = {}
    ids: dict[date, list[str]] = {}

    def add_id(day: date, event: dict[str, Any], event_type: str) -> None:
        event_id = event.get(_ID_FIELDS[event_type])
        if event_id and event_id not in ids.setdefault(day, []):
            ids[day].append(str(event_id))

    for event in events.get("FLR", []):
        day = _event_day(event, "FLR")
        if day is None:
            continue
        flare_class = event.get("classType")
        if _flare_rank(flare_class) > _flare_rank(flares.get(day)):
            flares[day] = flare_class
        add_id(day, event, "FLR")

    for event in events.get("SEP", []):
        day = _event_day(event, "SEP")
        if day is None:
            continue
        seps.add(day)
        add_id(day, event, "SEP")

    for event in events.get("GST", []):
        for reading in event.get("allKpIndex") or []:
            value, observed = reading.get("kpIndex"), reading.get("observedTime")
            if value is None or not isinstance(observed, str):
                continue
            day = date.fromisoformat(observed[:10])
            kp[day] = max(kp.get(day, 0.0), float(value))
            add_id(day, event, "GST")

    for event in events.get("CME", []):
        day = _event_day(event, "CME")
        if day is not None:
            add_id(day, event, "CME")

    result: list[SpaceWeatherDay] = []
    for offset in range(days):
        day = start + timedelta(days=offset)
        risk, multiplier = classify_risk(day in seps, flares.get(day), kp.get(day))
        result.append(
            SpaceWeatherDay(
                date=day,
                flare_max_class=flares.get(day),
                sep_event=day in seps,
                geomagnetic_kp_max=kp.get(day),
                risk_level=risk,
                radiation_multiplier=multiplier,
                events=ids.get(day, []),
                source=Source.NASA_DONKI,
                stale=stale,
            )
        )
    return result


def latest_notifications(
    events: dict[str, list[dict[str, Any]]], limit: int = 10
) -> list[dict[str, Any]]:
    """Las notificaciones más recientes, resumidas para el feed de la app."""
    items = sorted(
        events.get("notifications", []),
        key=lambda n: n.get("messageIssueTime") or "",
        reverse=True,
    )
    return [
        {
            "id": n.get("messageID"),
            "type": n.get("messageType"),
            "issued_at": n.get("messageIssueTime"),
            "url": n.get("messageURL"),
            "source": Source.NASA_DONKI.value,
        }
        for n in items[:limit]
    ]


_default_client: DonkiClient | None = None


def get_donki_client() -> DonkiClient:
    """Cliente compartido del proceso (su memoria sirve a toda la API)."""
    global _default_client
    if _default_client is None:
        _default_client = DonkiClient()
    return _default_client


def parse_iso_datetime(value: str) -> datetime | None:
    """Lee las fechas de DONKI (`2026-11-10T04:00Z`)."""
    try:
        return datetime.fromisoformat(value.replace("Z", "+00:00"))
    except (AttributeError, ValueError):
        return None
