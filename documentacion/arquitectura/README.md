# 🛠️ Guía técnica · Cómo se construye AstroHealth

> [← Documentación](../README.md) · [Plan](../PLAN_ASTROHEALTH.md) · [Módulos](../modulos/README.md)

El **plan** dice **qué** se construye. Esta guía dice **cómo**. Si se contradicen, se avisa en el chat del equipo y se corrige uno de los dos antes de programar.

| # | Documento | Léalo cuando… |
|:---:|---|---|
| ⚠️ | [**ARCHITECTURE.md**](ARCHITECTURE.md) | **Siempre**, antes de escribir código: capas, reglas, nombres y prácticas prohibidas |
| 01 | [API y formato JSON](01-api-y-json.md) | Vaya a crear o a consumir un endpoint |
| 02 | [Base de datos](02-base-de-datos.md) | Vaya a crear una migración o un *seeder* |
| 03 | [Frontend (Vue 3)](03-frontend-vue.md) | Vaya a crear una pantalla, un componente o un *store* |
| 04 | [Integración con Python](04-integracion-python.md) | Toque el motor de evaluación o los datos simulados |
| 05 | [Entorno local](05-entorno-local.md) | Sea su primera vez con el proyecto |
| 06 | [Flujo de Git](06-flujo-de-git.md) | Vaya a crear una rama o a abrir un PR |

## El stack en una tabla

| Capa | Tecnología |
|---|---|
| Frontend | Vue 3 · TypeScript · Inertia 3 · Pinia · Tailwind v4 · Chart.js · SVG + GSAP |
| Backend | Laravel 13 · API REST `/api/v1` · JWT (`firebase/php-jwt`) con roles · [guía del backend](../../astrohealth/docs/backend/README.md) |
| Base de datos | MySQL (XAMPP en local) |
| Motor y datos | Python 3.11+ · FastAPI · NASA DONKI y OSDR ([guía del simulador](../../simulador/docs/SIMULADOR.md)) |
