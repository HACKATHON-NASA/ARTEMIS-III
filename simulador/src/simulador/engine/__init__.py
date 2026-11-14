"""Motor de evaluación: semáforo, puntaje, tendencias y recomendaciones."""

from simulador.engine.evaluate import evaluate, status_of
from simulador.engine.recommend import recommend
from simulador.engine.score import assess, body_status, score, worst
from simulador.engine.trends import trend

__all__ = ["assess", "body_status", "evaluate", "recommend", "score", "status_of", "trend", "worst"]
