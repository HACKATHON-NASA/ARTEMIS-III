"""Tripulación ficticia (`config/crew.yaml`)."""

from simulador.config import load_yaml
from simulador.models import CrewMember


def load_crew(crew_size: int) -> list[CrewMember]:
    """Los primeros `crew_size` astronautas de `crew.yaml`."""
    members = load_yaml("crew")["crew"]
    if crew_size > len(members):
        raise ValueError(f"crew.yaml solo tiene {len(members)} astronautas")
    return [CrewMember.model_validate(m) for m in members[:crew_size]]
