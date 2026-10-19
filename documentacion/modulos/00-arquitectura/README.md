# Guía del proyecto y de la arquitectura

> [← Índice de módulos](../README.md) · [Inicio](../../README.md) · [Plan de sprints](../../scrum/plan-de-sprints.md)

**Empiece aquí.** Esta carpeta explica de qué trata AstroHealth, cómo está construido y cómo trabajamos. Todos hablamos el mismo idioma: dónde va cada cosa, cómo se llama, cómo responde una API, cómo se reporta un error y cómo se sube el código. Quien la lea completa puede tomar cualquier historia de cualquier módulo y saber qué hacer.

## La norma

> ⚠️ **[ARCHITECTURE.md](ARCHITECTURE.md) es la norma arquitectónica obligatoria del proyecto.** Todo desarrollador o agente de IA la lee antes de crear o modificar código. Los documentos 2 a 7 son manuales prácticos de esa norma; si alguno la contradice, manda la norma.

## Orden de lectura

| # | Documento | Qué responde | Quién lo lee |
|:---:|---|---|---|
| 1 | [El proyecto](01-el-proyecto.md) | Qué vamos a construir, para quién, para cuándo y qué es "ganar" | Todo el equipo |
| ★ | [ARCHITECTURE.md](ARCHITECTURE.md) | **Las reglas obligatorias:** capas, dependencias, repositorios, estándar JSON, códigos funcionales, seguridad, frontend modular, nombres, límites, pruebas, prácticas prohibidas y ejemplos de flujo | Todo el equipo y todo agente de IA |
| 2 | [Arquitectura](02-arquitectura.md) | Cómo está armado el sistema: módulos, capas, nodos y cómo viaja una petición | Todo el equipo |
| 3 | [Backend con Laravel](03-backend-laravel.md) | Para qué sirve cada capa (controlador, servicio, repositorio…) y dónde se programa cada cosa, con un ejemplo completo | Backend, y frontend para entender lo que recibe |
| 4 | [API, JSON y errores](04-api-y-errores.md) | Cómo se nombran las rutas, cómo es el JSON, qué código HTTP se usa y cómo un error dice qué pasó y dónde | Todo el equipo |
| 5 | [Frontend con Vue e Inertia](05-frontend-vue.md) | Estructura de carpetas, cómo se hace una página, componentes, formularios y pruebas | Frontend, y backend para entender las *props* |
| 6 | [Base de datos y migraciones](06-base-de-datos.md) | Cómo se crea y se cambia una tabla sin romper nada | Backend |
| 7 | [Forma de trabajo](07-forma-de-trabajo.md) | Git, Jira, ramas, *pull requests*, pruebas y cuándo una historia está terminada | Todo el equipo |
| 8 | [Preparar el entorno local](08-entorno-local.md) | Instalar PHP 8.4, Node 22, MySQL, clonar el repositorio y arrancar la aplicación, con la solución a los errores frecuentes | **Todo el equipo, primero que todo** |

## Cómo se usa junto con las fichas de los módulos

| Pregunta | Dónde está |
|---|---|
| ¿Cómo se hace **en general**? (capas, nombres, formato de errores, Git) | Esta carpeta |
| ¿Qué hace **este módulo**? (reglas, tablas, rutas, pantallas) | La ficha del módulo: `modulos/Mxx-…/README.md` y sus carpetas `backend/`, `frontend/` y `database/` |
| ¿Qué tengo que hacer **en esta historia**? | `modulos/Mxx-…/<capa>/HU/HU-xx.yy-….md` |
| ¿En qué orden y para cuándo? | [Plan de sprints](../../scrum/plan-de-sprints.md) |

Si una ficha de módulo contradice esta guía, **manda la ficha** (es más específica), y se avisa para corregir una de las dos.
