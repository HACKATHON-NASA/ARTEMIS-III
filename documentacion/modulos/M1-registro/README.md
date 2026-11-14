# 🩺 M1 · Registro de indicadores

> [← Módulos](../README.md) · [Plan §3 M1](../../PLAN_ASTROHEALTH.md#-m1--registro-de-indicadores) · Siguiente: [M2 Evaluación](../M2-evaluacion/README.md)

| Backend | Frontend | Fase | Responsable | Depende de |
|---|---|:---:|:---:|---|
| `app/Modules/Registro/` | `resources/js/modules/Registro/` | 2 | P1 (API) · P4 (UI) | [M6](../M6-entorno/README.md) `CrewQuery`, `MissionQuery` · [M2](../M2-evaluacion/README.md) `Evaluator` · [M0](../../../astrohealth/docs/backend/M0-identidad/README.md) `CurrentActor` |

## 1. Para qué sirve

El astronauta registra sus indicadores del día. Cada valor se valida, se evalúa (verde, amarillo o rojo) y queda **guardado con su color**. Además, este módulo importa los 30 días simulados que genera Python.

## 2. En la demo

> *"Registra sus datos; los valores vuelan al cuerpo."* (20 s)

1. Pantalla **Registrar**: un campo por indicador con su unidad, y el ánimo con 5 caritas 😞 😕 😐 🙂 😄.
2. Junto a cada campo, **"¿Cómo me mido?"** abre un diálogo: cómo medirlo y por qué importa en el espacio.
3. **Guardar** → cada valor **vuela** hacia su órgano (GSAP) y aparece con su color.

## 3. Datos

Es dueño de **`indicators`** y **`measurements`** ([columnas](../../arquitectura/02-base-de-datos.md#m1--indicators)).

| `code` | Nombre | Unidad | Rango posible | Zona |
|---|---|---|---|---|
| `hr` | Frecuencia cardíaca | bpm | 20–250 | `heart` |
| `bp_sys` | Presión sistólica | mmHg | 50–260 | `heart` |
| `bp_dia` | Presión diastólica | mmHg | 30–160 | `heart` |
| `glucose` | Glucosa (ayunas) | mg/dL | 20–600 | `metabolism` |
| `temp` | Temperatura | °C | 30–45 | `immune` |
| `spo2` | Saturación de oxígeno | % | 50–100 | `lungs` |
| `sleep` | Sueño | h | 0–24 | `brain` |
| `mood` | Estado de ánimo | 1–5 | 1–5 | `brain` |
| `radiation` | Radiación diaria | mSv | 0–100 | `whole_body` |
| `exercise_min` | Ejercicio | min | 0–600 | `bones` |

> Los rangos posibles solo rechazan errores de digitación (una FC de 900). **No son umbrales clínicos:** esos están en M2.

## 4. API

| Método | Ruta | Qué hace | Respuesta |
|---|---|---|---|
| GET | `/api/v1/indicators` | Lista los indicadores con su unidad, su rango posible, su zona y su `help_text` | 200 |
| POST | `/api/v1/measurements` | Guarda una o varias mediciones y devuelve cada una con su `status` | 201 |

**`POST /api/v1/measurements`**

```json
{
  "measured_at": "2026-11-14T08:00:00Z",
  "measurements": [
    { "indicator": "bp_sys", "value": 148 },
    { "indicator": "bp_dia", "value": 94 },
    { "indicator": "mood", "value": 4 }
  ]
}
```

```json
{
  "data": [
    { "id": 901, "indicator": "bp_sys", "value": 148, "unit": "mmHg", "status": "red", "body_part": "heart", "measured_at": "2026-11-14T08:00:00Z", "source": "manual" },
    { "id": 902, "indicator": "bp_dia", "value": 94, "unit": "mmHg", "status": "red", "body_part": "heart", "measured_at": "2026-11-14T08:00:00Z", "source": "manual" },
    { "id": 903, "indicator": "mood", "value": 4, "unit": "1-5", "status": "green", "body_part": "brain", "measured_at": "2026-11-14T08:00:00Z", "source": "manual" }
  ],
  "message": "ok"
}
```

`measured_at` es opcional: si no viene, se usa la hora actual. El astronauta sale **del token**: nadie registra por otro.

**Ingesta de las máquinas:** `POST /api/v1/ingest/readings` recibe los lotes del simulador (JWT de dispositivo, idempotente por `batch_id`). Contrato completo en [M1 · backend](../../../astrohealth/docs/backend/M1-registro/README.md#1-la-ingesta-de-las-máquinas).

## 5. Backend por capas

| Capa | Archivo | Qué hace |
|---|---|---|
| — | `RegistroServiceProvider.php` · `routes.php` | Registra las rutas, las migraciones y los *bindings* de sus contratos |
| Domain | `Domain/Enums/IndicatorCode.php` | Los 10 códigos |
| Domain | `Domain/Enums/ErrorCode.php` | Los códigos de error ([sección 8](#8-códigos-de-error)) |
| Domain | `Domain/Eventos/MeasurementsRecorded.php` | `astronautId` y la lista de `{measurementId, indicator, value, status, bodyPart, measuredAt}` |
| Application | `Application/RecordMeasurements.php` | Comprueba el astronauta (M6) → pide el entorno (M6) → evalúa cada valor (`Evaluator` de M2) → guarda en una transacción → actualiza el puntaje (`Evaluator::assess`) → publica `MeasurementsRecorded` |
| Application | `Application/IngestReadings.php` | Recibe los lotes del simulador: valida cada lectura, evalúa en lote, guarda sin duplicar y publica el evento |
| Application | `Application/ImportMissionFile.php` | Plan B sin red: lee el JSON del simulador |
| Application | `Application/ReevaluateLatest.php` | Cuando cambia el entorno: vuelve a evaluar la **última** medición de cada indicador de cada astronauta, actualiza su `status` y recalcula el puntaje. El historial no se toca |
| Application | `Application/DTOs/MeasurementData.php` · `RecordResult.php` | Entrada y salida |
| Infrastructure | `Infrastructure/Listeners/OnMissionEnvironmentChanged.php` | Escucha a M6 y llama a `ReevaluateLatest` |
| Infrastructure | `Infrastructure/Models/Indicator.php` · `Measurement.php` | Modelos Eloquent |
| Infrastructure | `Infrastructure/Repositorios/IndicatorRepository.php` | Implementa `IndicatorCatalog` |
| Infrastructure | `Infrastructure/Repositorios/MeasurementRepository.php` | `save()`, `saveMany()`, `latestPerIndicator()`, `series()`. Implementa `MeasurementQuery` |
| Http | `Http/Requests/StoreMeasurementsRequest.php` | El astronauta existe, el indicador es uno de los 10 y el valor está en el rango posible (lee el catálogo) |
| Http | `Http/Controllers/Api/IndicatorController.php` · `MeasurementController.php` | Delgados |
| Http | `Http/Resources/IndicatorResource.php` · `MeasurementResource.php` | Forma de `data` |
| Console | `Console/ImportMissionFileCommand.php` | `registro:import-file <archivo.json>` |
| Database | `Migrations/` · `Seeders/IndicatorSeeder.php` · `Factories/MeasurementFactory.php` | |

## 6. Frontend

| Archivo | Qué hace |
|---|---|
| `Components/MeasurementForm.vue` | Un `MeasurementField` por indicador, ordenados por `sort_order`. Valida los campos vacíos y envía con el *store* |
| `Components/MeasurementField.vue` | Etiqueta, campo numérico con su unidad y el botón "¿Cómo me mido?" |
| `Components/MoodPicker.vue` | 5 caritas clicables, accesibles con el teclado |
| `Components/HowToMeasureDialog.vue` | Diálogo con el `help_text` |
| `Components/FlyingValue.vue` | La animación del valor "volando" hacia el órgano (GSAP). Con `prefers-reduced-motion`, solo un *fade* |
| `Stores/useMeasurementsStore.ts` | `indicators`, `loadIndicators()`, `record()`, `lastRecorded` |
| `Types/measurement.ts` | `Indicator`, `Measurement`, `RecordPayload` |
| Página `pages/Record.vue` | Compone el formulario y el resumen de lo que se acaba de guardar |

## 7. Contratos y eventos

| Tipo | Nombre | Detalle |
|---|---|---|
| Ofrece | `Contratos/IndicatorCatalog` | `all()`, `byCode(string)`: el código, la unidad, la zona y el rango posible |
| Ofrece | `Contratos/MeasurementQuery` | `latestPerIndicator(int $astronautId)`, `series(int $astronautId, string $indicator, CarbonImmutable $from)`, `daysWithAllIndicators(int $astronautId)` |
| Publica | `MeasurementsRecorded` | Lo escuchan M5 (alertas) y M7 (puntos) |
| Escucha | M6 `MissionEnvironmentChanged` | Vuelve a evaluar las últimas mediciones |
| Usa | M6 `CrewQuery`, `MissionQuery` | ¿Existe el astronauta? ¿En qué entorno está? |
| Usa | M2 `Evaluator` | El color y el puntaje |
| Usa | M0 `CurrentActor` | Quién llama: astronauta o dispositivo |

## 8. Códigos de error

| Código | HTTP | Cuándo |
|---|:---:|---|
| `VALIDATION_FAILED` | 422 | Un indicador desconocido o un valor fuera del rango posible |
| `MEASUREMENTS_ASTRONAUT_NOT_FOUND` | 404 | El astronauta no existe |
| `MEASUREMENTS_IMPORT_FAILED` | — | (Solo en el comando) Python no respondió y no se pasó `--from` |

## 9. Criterios de aceptación

| # | Dado | Cuando | Entonces |
|:---:|---|---|---|
| 1 | Un astronauta en Marte | Registra una FC de 72 | Se guarda con `status: green` y `source: manual`, y responde 201 |
| 2 | Un astronauta | Registra una presión de 148/94 | Las dos mediciones quedan en rojo y se publica `MeasurementsRecorded` |
| 3 | Cualquier usuario | Envía una FC de 900 | 422 `VALIDATION_FAILED` con el error en `measurements.0.value`, y no se guarda nada |
| 4 | Python apagado | Registra una FC de 130 | Se guarda en rojo usando el respaldo local, sin error para el usuario |
| 5 | El simulador | Envía el mismo lote dos veces | La segunda vez responde 200 con el mismo resumen y no guarda nada |
| 7 | Un token de astronauta | Llama a `/ingest/readings` | 403 `FORBIDDEN` |
| 6 | Un formulario con 3 de 10 campos llenos | Pulsa "Guardar" | Solo se envían los 3 llenos |

## 10. Listo cuando

- [ ] Se registra una medición y queda guardada con su color.
- [ ] El "¿Cómo me mido?" funciona en los 10 indicadores.
- [ ] El valor "vuela" al cuerpo.
- [ ] El simulador envía los 30 días y quedan guardados con su `source`.
