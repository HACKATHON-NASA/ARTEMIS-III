# M05 · Psicología y cognición · Historias de frontend

> [← Ficha del módulo](../../README.md) · [Ficha de frontend](../README.md) · [Historias de base de datos](../../database/HU/README.md) · [Historias de backend](../../backend/HU/README.md)

Trabajo de **Inertia + Vue 3 + Tailwind CSS v4** de M05: qué páginas hay, **cómo deben verse**, qué textos llevan y cómo se comportan. Lo hace **Gabriela**. HU-05.04 no tiene pantalla propia (la alerta la muestra M13). Las *props* vienen de las [historias de backend](../../backend/HU/README.md).

## Reglas comunes

- Se siguen la [guía visual provisional](../../../M01-identidad/frontend/HU/README.md#guía-visual-provisional) y la [estructura común de las aplicaciones](../../../M01-identidad/frontend/HU/README.md#estructura-común-de-las-cuatro-aplicaciones) de M01. Textos para el astronauta **de tú**.
- **Los tests funcionan sin conexión:** los parámetros vienen en las *props* y se guardan en `instantaneas` (M15). Al terminar, la sesión se arma completa (con `crypto.randomUUID()`) y se envía con `encolar()` de la cola offline.
- **Las métricas no se muestran al astronauta.** La pantalla final dice "Hecho": el resultado oficial lo calcula el servidor, y ver la puntuación tras cada test puede cambiar cómo se hace el siguiente.
- **Los motores de los tests son TypeScript puro** (`resources/js/tests-cognitivos/`), sin Vue, para probarlos con Vitest. Los tiempos se miden con `performance.now()`.
- **Una alarma crítica interrumpe cualquier test.** Si llega `AlarmaCritica` (M13) durante un test, el test se cancela (sesión incompleta, enviada con lo que haya) y se muestra la alarma. La ficha anterior pedía "bloquear notificaciones" durante el PVT, lo que habría tapado, por ejemplo, la orden de ir al refugio en un evento solar.


## Historias

| Historia | Archivo |
|---|---|
| HU-05.01 · Realizar un test de tiempo de reacción (PVT) | [HU-05.01-realizar-un-test-de-tiempo-de-reaccion-pvt.md](HU-05.01-realizar-un-test-de-tiempo-de-reaccion-pvt.md) |
| HU-05.02 · Realizar un test de aritmética | [HU-05.02-realizar-un-test-de-aritmetica.md](HU-05.02-realizar-un-test-de-aritmetica.md) |
| HU-05.07 · Realizar un test de memoria de secuencias | [HU-05.07-realizar-un-test-de-memoria-de-secuencias.md](HU-05.07-realizar-un-test-de-memoria-de-secuencias.md) |
| HU-05.03 · Registrar ánimo, estrés y fatiga | [HU-05.03-registrar-animo-estres-y-fatiga.md](HU-05.03-registrar-animo-estres-y-fatiga.md) |
| HU-05.05 · Recordatorios de los tests programados | [HU-05.05-recordatorios-de-los-tests-programados.md](HU-05.05-recordatorios-de-los-tests-programados.md) |
| HU-05.06 · Ver la evolución psicológica de cada tripulante | [HU-05.06-ver-la-evolucion-psicologica-de-cada-tripulante.md](HU-05.06-ver-la-evolucion-psicologica-de-cada-tripulante.md) |
