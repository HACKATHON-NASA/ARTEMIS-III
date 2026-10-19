# M05 · Psicología y cognición · Base de datos

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · [Frontend](../frontend/README.md) · **Base de datos**

Tablas **MySQL** que este módulo posee: es el único que las migra y las escribe ([reglas de comunicación](../../README.md#4-reglas-de-comunicación-entre-módulos)). Siguen las [convenciones de base de datos](../../README.md#7-convenciones-de-base-de-datos).

---

## 1. Tablas

### `sesiones_test`

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | Generado en el cliente |
| `tripulante_id` | `uuid` | |
| `tipo` | `varchar(12)` | `pvt`, `aritmetica` o `memoria` |
| `inicio` | `datetime(3)` | |
| `duracion_s` | `smallint` | |
| `completa` | `boolean` | PVT de 3 min completo, 20 ítems respondidos o test de memoria terminado por la regla de parada |
| `origen` | `varchar(10)` | `app` o `simulador` |
| `metricas` | `json` | Calculadas en el servidor (tabla siguiente) |
| `completada_en` | `datetime(3)` | |

### `respuestas_item`

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | |
| `sesion_id` | `uuid` | |
| `orden` | `smallint` | |
| `estimulo` | `varchar(20)` | PVT: `luz`; aritmética: la operación, p. ej. `47+28`; memoria: la secuencia, p. ej. `58291` |
| `respuesta` | `varchar(20)` nulo | Aritmética: el número escrito; memoria: la secuencia escrita |
| `es_correcta` | `boolean` nulo | Aritmética y memoria |
| `tiempo_ms` | `int` nulo | Desde la presentación hasta la respuesta, medido con `performance.now()`; `null` en un toque antes del estímulo o sin respuesta |
| `es_falsa_salida` | `boolean` | Solo PVT |
| `sin_respuesta` | `boolean` | Solo PVT: estímulo sin respuesta en `pvt_espera_max_s` (cuenta como lapso) |
| `presentado_en` | `datetime(3)` | |

### `respuestas_escala`

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | Una fila por escala respondida |
| `tripulante_id` | `uuid` | |
| `escala` | `varchar(15)` | `animo`, `estres`, `fatiga` (diarias, 0–10) · `carga_trabajo`, `cohesion` (semanales, 1–5) |
| `valor` | `tinyint` | |
| `origen` | `varchar(10)` | `app` o `simulador` |
| `respondida_en` | `datetime(3)` | |

Orientación: `animo` 0 = muy mal y 10 = muy bien; `estres` y `fatiga` 0 = nada y 10 = máximo.

### `programacion_tests`

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | |
| `tripulante_id` | `uuid` | |
| `tipo` | `varchar(17)` | `pvt`, `aritmetica`, `memoria`, `escalas_diarias`, `escalas_semanales` (17 caracteres; `varchar(15)` lo cortaba) |
| `hora_utc` | `time` | Hora similar cada día, para no confundir deterioro con ritmo circadiano |
| `dia_semana` | `tinyint` nulo | Solo para `escalas_semanales` (1 = lunes) |
| `activa` | `boolean` | |

### Métricas por sesión

| Test | Métrica (clave en `metricas`) | Código para la línea base | Unidad | Peor si… | σ mínima |
|---|---|---|---|:---:|---|
| PVT | `mediana_tr_ms` | `AH-PVT-MEDIANA` | `ms` | sube | 10 ms y 10 % de μ |
| PVT | `lapsos` (TR > 500 ms) | `AH-PVT-LAPSOS` | `{#}` | sube | 1 |
| PVT | `falsas_salidas` (antes del estímulo o TR < 100 ms) | `AH-PVT-FALSAS` | `{#}` | sube | 1 |
| PVT | `desviacion_tr_ms` | `AH-PVT-DE` | `ms` | sube | 10 ms |
| Aritmética | `tiempo_medio_item_s` | `AH-ARIT-TIEMPO` | `s` | sube | 0,2 s y 10 % de μ |
| Aritmética | `precision_pct` | `AH-ARIT-PRECISION` | `%` | baja | **7,5 puntos** (1,5 ítems; ver [backend §3.2](../backend/README.md#32-detección-de-deterioro-regladeteriorocognitivo)) |
| Memoria | `longitud_max` (secuencia más larga repetida sin error) | `AH-MEM-SPAN` | `{#}` | baja | 1 elemento † |

Las σ mínimas son las de [04 §5.2](../../../docs/propuesta/04-manejo-de-datos.md#52-línea-base) y se configuran en `sigma_min` de M16 (la de memoria es un parámetro de diseño †, [04 §5.1](../../../docs/propuesta/04-manejo-de-datos.md#51-captura)); la mediana del PVT excluye las falsas salidas.

## 2. Migraciones y seeders

```
├── Database/
│   ├── Migrations/{create_sesiones_test,create_respuestas_item,create_respuestas_escala,create_programacion_tests}.php
│   └── Seeders/ProgramacionDemoSeeder.php   # PVT, aritmética y memoria diarios a las 08:00 UTC
```

## 3. Relaciones

| Desde | Hacia | Tipo |
|---|---|---|
| `respuestas_item.sesion_id` | `sesiones_test.id` | `FOREIGN KEY` (`ON DELETE RESTRICT`) |
| `sesiones_test.tripulante_id`, `respuestas_escala.tripulante_id`, `programacion_tests.tripulante_id` | `tripulantes.id` (M02) | Lógica |

Única: `respuestas_item (sesion_id, orden)`.

> El número de sesión y si es de familiarización **no se guardan**: se calculan al consultar con `ROW_NUMBER() OVER (PARTITION BY tripulante_id, tipo ORDER BY inicio)` sobre las sesiones completas (MySQL 8). Así una sesión hecha sin conexión que llega tarde queda en su lugar.

## 4. Consultas principales

| Consulta | Índice |
|---|---|
| Últimas N sesiones completas de un tipo (rachas de la regla) y su número de orden | `(tripulante_id, tipo, completa, inicio)` |
| Escalas de un periodo (contexto de las alertas) | `respuestas_escala (tripulante_id, escala, respondida_en)` |
| Programación del día | `programacion_tests (tripulante_id, activa)` |

## 5. Volumen

Por tripulante y día: 3 sesiones (PVT ~30 ítems, aritmética 20, memoria ~10) ≈ 60 filas de `respuestas_item` y 3–5 escalas. Unas 7.000 filas por mes para 4 tripulantes.

Migraciones, columnas exactas y criterios: [historias de base de datos](HU/README.md).

## 6. Seeders

`ProgramacionDemoSeeder`: para cada tripulante, `pvt`, `aritmetica`, `memoria` y `escalas_diarias` a las 08:00 UTC; `escalas_semanales` los lunes (`dia_semana = 1`) a las 08:00 UTC.
