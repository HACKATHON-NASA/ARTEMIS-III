# 7 · Forma de trabajo

> [← Guía](README.md) · Anterior: [Base de datos y migraciones](06-base-de-datos.md) · Siguiente: [Preparar el entorno local](08-entorno-local.md) · [Plan de sprints](../../scrum/plan-de-sprints.md)

Cómo pasa una historia de "por hacer" a "hecha": Jira para saber quién hace qué, Git para el código, *pull requests* para revisar y pruebas cruzadas para no dejar errores. El ritmo (reunión diaria, revisión de fase) está en el [plan](../../scrum/plan-de-sprints.md#ritmo-de-trabajo-fases-cortas).

## 1. Jira

| En Jira | Corresponde a |
|---|---|
| Épica | Un módulo o épica del backlog (`EP-11 · Síntomas`) |
| Historia | Una HU (`HU-11.01 · Registrar un síntoma con su intensidad`), con el enlace a su ficha en `modulos/` |
| Subtareas | Una por capa con trabajo: **BD**, **Backend** y **Frontend**, cada una asignada a su responsable del [plan](../../scrum/plan-de-sprints.md) |
| Error (*bug*) | Lo que falla en pruebas, enlazado a su historia |

**Tablero:**

```
Por hacer → En progreso → En revisión → En pruebas → Hecho
```

| Columna | Entra cuando… | Sale cuando… |
|---|---|---|
| Por hacer | La historia está en la fase actual | Alguien la empieza |
| En progreso | Se creó la rama | Se abre el *pull request* con la CI en verde |
| En revisión | Hay *pull request* hacia `development` | Otra persona lo aprueba y se fusiona en `development` |
| En pruebas | Está fusionada | Quien prueba confirma **todos** sus criterios de aceptación. Si algo falla, vuelve a "En progreso" con un *bug* enlazado |
| Hecho | Pasó las pruebas | — |

## 2. Git

Todo el flujo (ramas `development` → `testing` → `main`, cómo crear la rama de una historia, *commits*, *pull requests*, cómo evitar y resolver conflictos, y los comandos de rescate) está en **[Flujo de trabajo con Git](09-flujo-de-git.md)**.

En corto: se trabaja **siempre desde `development` actualizado**, cada historia en su rama (`feature/HU-xx.yy-backend` o `-frontend`), y vuelve a `development` solo por *pull request* revisado por otra persona.

## 3. Backend y frontend de la misma historia, en paralelo

El frontend **no espera** a que termine el backend: las *props* y las rutas ya están escritas en la ficha del módulo.

1. Backend y frontend leen la misma historia y la misma tabla de *props* (`frontend/README.md` §3 del módulo).
2. Mientras no existe el controlador, el frontend prueba su página con *props* de ejemplo (en Vitest o en una ruta temporal de su rama).
3. Si al construir aparece que una *prop* o una ruta tiene que cambiar, **primero se cambia la ficha** (con su línea en el [CHANGELOG](../../CHANGELOG.md)) y se avisa en el chat. El código nunca contradice la ficha.

## 4. Cuándo una historia está lista

**Lista para empezar** (*ready*): está en la fase actual, sus dependencias están en "Hecho" (columna *Depende de* del plan) y tiene ficha en `modulos/`.

**Terminada** (*done*):

- [ ] Todos los criterios de aceptación tienen su prueba y pasan.
- [ ] Cumple [ARCHITECTURE.md](ARCHITECTURE.md): capas, sobre JSON, códigos funcionales documentados y ninguna [práctica prohibida](ARCHITECTURE.md#25-prácticas-prohibidas). La prueba de arquitectura está en verde.
- [ ] Ningún valor clínico u operativo escrito en el código (sale de M16).
- [ ] Textos exactos de la historia; pantalla usable con teclado y sin desplazamiento horizontal a 375 px.
- [ ] Revisada y fusionada en `development`; probada por otra persona en "En pruebas".
- [ ] Si cambió algo de la ficha, la ficha y el CHANGELOG están actualizados.

## 5. Pruebas cruzadas

Todos probamos todo, todo el tiempo:

| Quién prueba | Qué |
|---|---|
| Quien programa | Sus pruebas automáticas, antes del *pull request* |
| Quien revisa el *pull request* | Lee el código y ejecuta las pruebas |
| Otra persona del equipo, en "En pruebas" | Recorre los criterios de aceptación en la aplicación real (local o nodo de pruebas), como lo haría un usuario |
| Todo el equipo, al cerrar cada fase | Los escenarios E0–E8 que ya deben pasar, no solo los nuevos |

Un error encontrado se registra en Jira con: pasos para reproducirlo, qué se esperaba, qué pasó y, si salió en la API, su `traza_id` ([errores](04-api-y-errores.md#5-cómo-encontrar-un-error-en-el-log)).

## 6. Trabajar con IA

Se puede (y conviene) usar IA para programar, con las reglas del [plan §10](../../scrum/plan-de-sprints.md#10-cómo-trabajar-con-ia-sin-introducir-errores). En corto: se le da la historia, la ficha del módulo y esta guía; primero las pruebas; los valores clínicos se copian de la documentación, nunca de la IA; y una persona revisa todo el *diff*.

## 7. Comunicación

- **Dudas que bloquean:** al chat del equipo, el mismo día. Si nadie responde en una hora, a la reunión diaria.
- **Cambios en la documentación:** con su línea en el [CHANGELOG](../../CHANGELOG.md).
- **Decisiones:** las toma el *Product Owner* y quedan escritas en la ficha que corresponde.
