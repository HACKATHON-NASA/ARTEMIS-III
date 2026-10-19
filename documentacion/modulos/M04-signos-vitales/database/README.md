# M04 · Signos vitales · Base de datos

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · [Frontend](../frontend/README.md) · **Base de datos**

Tablas **MySQL** que este módulo posee: es el único que las migra y las escribe ([reglas de comunicación](../../README.md#4-reglas-de-comunicación-entre-módulos)). Siguen las [convenciones de base de datos](../../README.md#7-convenciones-de-base-de-datos).

---

## 1. Tablas

### `agregados_vitales`

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | |
| `tripulante_id` | `uuid` | |
| `codigo` | `varchar(20)` | Códigos de M04 del [catálogo](../../README.md#8-catálogo-de-códigos-de-variables) |
| `periodo` | `varchar(4)` | `hora` o `dia` |
| `contexto` | `varchar(7)` | `todos` (todas las lecturas) o `reposo` (solo reposo) |
| `inicio` | `datetime(3)` | Inicio del periodo (UTC) |
| `n` | `int` | Lecturas válidas (sin artefactos) |
| `minimo`, `maximo`, `media`, `p50` | `decimal(12,4)` | |
| `calculado_en` | `datetime(3)` | Último recálculo |

El `id` es un UUID v5 de la clave, igual en los dos nodos. Única: `(tripulante_id, codigo, periodo, contexto, inicio)`. Se recalcula el periodo afectado cada vez que llega un `LoteIngerido`.

### `pruebas_ortostaticas`

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | Generado en el cliente |
| `tripulante_id` | `uuid` | |
| `realizada_en` | `datetime(3)` | |
| `modalidad` | `varchar(6)` | `de_pie` (en gravedad: antes del vuelo o tras aterrizar) o `lbnp` (en vuelo, con presión negativa en la parte inferior del cuerpo) |
| `fc_basal`, `fc_estimulo` | `smallint` | lpm (basal = acostado; estímulo = de pie o con LBNP) |
| `pas_basal`, `pad_basal`, `pas_estimulo`, `pad_estimulo` | `smallint` | mmHg |
| `caida_sistolica`, `caida_diastolica` | `smallint` | Calculadas: basal − estímulo (negativa si la presión sube) |
| `umbral_sistolica`, `umbral_diastolica` | `smallint` | Umbrales de M16 (`PA_ORTO_SIS`, `PA_ORTO_DIA`) con que se calculó el resultado |
| `resultado` | `varchar(10)` | `normal` o `positiva` |
| `registrada_por` | `bigint` | Usuario (CMO) |
| `created_at` | `datetime(3)` | |

### `ventanas_ecg`

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | Generado en el cliente |
| `tripulante_id` | `uuid` | |
| `dispositivo_id` | `uuid` | Dispositivo nombrado (el `wearable`) |
| `inicio` | `datetime(3)` | |
| `duracion_s` | `decimal(4,1)` | ≤ 60,0 |
| `frecuencia_muestreo_hz` | `smallint` | 250 en el simulador |
| `ruta_archivo` | `varchar(255)` | `ecg/<tripulante_id>/<id>.json` en el disco privado `local`: la señal **y los picos R** (la página los necesita) |
| `contexto` | `varchar(10)` | |
| `n_rr` | `smallint` | Número de intervalos RR |
| `fc_media` | `decimal(5,1)` nulo | 60.000 / media(RR en ms); `null` si `n_rr < 10` |
| `pausa_max_s` | `decimal(4,2)` nulo | max(RR) / 1.000; `null` si `n_rr < 10` |
| `cv_rr` | `decimal(5,4)` nulo | desviación(RR) / media(RR); `null` si `n_rr < 30` |
| `qtc_ms` | `smallint` nulo | Enviado por el dispositivo o el simulador |
| `created_at` | `datetime(3)` | |

## 2. Migraciones y seeders

```
├── Database/Migrations/{create_agregados_vitales,create_pruebas_ortostaticas,create_ventanas_ecg}.php
```

## 3. Relaciones

| Desde | Hacia | Tipo |
|---|---|---|
| `agregados_vitales.tripulante_id`, `pruebas_ortostaticas.tripulante_id`, `ventanas_ecg.tripulante_id` | `tripulantes.id` (M02) | Lógica |
| `pruebas_ortostaticas.registrada_por` | `users.id` (M01) | Lógica |

No hay claves foráneas entre las tres tablas: son independientes.

## 4. Consultas principales

| Consulta | Índice |
|---|---|
| Serie horaria o diaria para las gráficas (`todos`) o para las tendencias en reposo (`reposo`) | Única `(tripulante_id, codigo, periodo, contexto, inicio)` |
| Pruebas ortostáticas de un tripulante | `(tripulante_id, realizada_en)` |
| Ventanas de ECG de un tripulante | `(tripulante_id, inicio)` |

## 5. Volumen

- `agregados_vitales`: 8 códigos × 4 tripulantes × (24 horas + 1 día) × 2 contextos ≈ 1.600 filas al día. El recálculo es un *upsert* del periodo afectado.
- `ventanas_ecg`: una al día por tripulante en E0 (más las de los escenarios). La señal **no** está en la base de datos: va en el archivo JSON del disco privado (60 s × 250 Hz = 15.000 muestras, ~120 KB). Detalle en las [historias de base de datos](HU/README.md).

## 6. Seeders

Ninguno.
