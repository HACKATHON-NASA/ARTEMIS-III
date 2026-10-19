# M05 · Psicología y cognición · Historias de base de datos

> [← Ficha del módulo](../../README.md) · [Ficha de base de datos](../README.md) · [Historias de backend](../../backend/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **MySQL** de cada historia de M05. Lo hace **Julieth** (responsable del backend). Los puntos son los del [plan de sprints](../../../../scrum/plan-de-sprints.md) y cubren todas las capas.

## Reglas comunes

- Se siguen las [convenciones de base de datos](../../../README.md#7-convenciones-de-base-de-datos). Migraciones en `app/Modules/Psicologia/Database/Migrations/`; **una migración fusionada no se edita**.
- Sesiones, respuestas y escalas son **inmutables** (sin `updated_at`): una sesión repetida es otra sesión, nunca una edición.
- Los datos de este módulo son **confidenciales** ([ficha de backend §3.3](../../backend/README.md#33-confidencialidad)): ninguna consulta de otro módulo los lee directamente; solo a través de los contratos `ConsultaSesiones` y `ConsultaEscalas`.
- Toda columna `datetime(3)` exige `protected $dateFormat = 'Y-m-d H:i:s.v';` en su modelo.


## Historias

| Historia | Archivo |
|---|---|
| HU-05.01 · Realizar un test de tiempo de reacción (PVT) | [HU-05.01-realizar-un-test-de-tiempo-de-reaccion-pvt.md](HU-05.01-realizar-un-test-de-tiempo-de-reaccion-pvt.md) |
| HU-05.02 · Realizar un test de aritmética | [HU-05.02-realizar-un-test-de-aritmetica.md](HU-05.02-realizar-un-test-de-aritmetica.md) |
| HU-05.07 · Realizar un test de memoria de secuencias | [HU-05.07-realizar-un-test-de-memoria-de-secuencias.md](HU-05.07-realizar-un-test-de-memoria-de-secuencias.md) |
| HU-05.03 · Registrar ánimo, estrés y fatiga | [HU-05.03-registrar-animo-estres-y-fatiga.md](HU-05.03-registrar-animo-estres-y-fatiga.md) |
| HU-05.05 · Recordatorios de los tests programados | [HU-05.05-recordatorios-de-los-tests-programados.md](HU-05.05-recordatorios-de-los-tests-programados.md) |
| HU-05.04 · Detectar un deterioro cognitivo sostenido | [HU-05.04-detectar-un-deterioro-cognitivo-sostenido.md](HU-05.04-detectar-un-deterioro-cognitivo-sostenido.md) |
| HU-05.06 · Ver la evolución psicológica de cada tripulante | [HU-05.06-ver-la-evolucion-psicologica-de-cada-tripulante.md](HU-05.06-ver-la-evolucion-psicologica-de-cada-tripulante.md) |
