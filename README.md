# ArtemisIII · AstroHealth

Monitorización de la salud de astronautas en misiones espaciales · **NASA International Space Apps Challenge 2026**.

| Carpeta | Qué tiene | Empiece por |
|---|---|---|
| [`documentacion/`](documentacion/README.md) | El plan, las fichas de los 8 módulos y la guía técnica | [El plan](documentacion/PLAN_ASTROHEALTH.md) · [ARCHITECTURE.md](documentacion/arquitectura/ARCHITECTURE.md) (norma obligatoria) |
| [`astrohealth/`](astrohealth/README.md) | La aplicación: Laravel 13 + Inertia + Vue 3 + Pinia + Tailwind v4 | [Preparar el entorno local](documentacion/arquitectura/05-entorno-local.md) |
| [`simulador/`](simulador/docs/SIMULADOR.md) | Python FastAPI: datos de la NASA (DONKI y OSDR), la misión simulada y el motor de evaluación | [SIMULADOR.md](simulador/docs/SIMULADOR.md) |

> ⚠️ **Antes de escribir código**, lea [ARCHITECTURE.md](documentacion/arquitectura/ARCHITECTURE.md) y la ficha de su módulo.

Herramienta de apoyo para la demo. No reemplaza el criterio médico. Los datos de salud son simulados y van calibrados con datos reales de la NASA.
