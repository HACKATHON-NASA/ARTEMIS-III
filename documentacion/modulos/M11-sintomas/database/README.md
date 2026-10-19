# M11 · Síntomas · Base de datos

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · [Frontend](../frontend/README.md) · **Base de datos**

Tablas **MySQL** que este módulo posee: es el único que las migra y las escribe ([reglas de comunicación](../../README.md#4-reglas-de-comunicación-entre-módulos)). Siguen las [convenciones de base de datos](../../README.md#7-convenciones-de-base-de-datos).

---

## 1. Tablas

### `sintomas_registrados`

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | Generado en el cliente |
| `tripulante_id` | `uuid` | |
| `tipo` | `varchar(30)` | Código de `catalogo_sintomas` (M16), p. ej. `cefalea` |
| `intensidad` | `tinyint` | 0 – 10 |
| `inicio_en` | `datetime(3)` | Cuándo empezó |
| `notas` | `varchar(500)` nulo | |
| `origen` | `varchar(10)` | `manual`, `simulador` |
| `created_at` | `datetime(3)` | |

Índice: `(tripulante_id, tipo, inicio_en)`.

### `fotos_sintoma` (HU-11.03)

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | Generado en el cliente |
| `sintoma_id` | `uuid` | Único: una foto por síntoma |
| `ruta` | `varchar(255)` | `sintomas/<tripulante_id>/<id>.jpg` en el disco privado `local` (en Laravel 11 su raíz es `storage/app/private`) |
| `bytes` | `int` | ≤ `adjunto_foto_max_kb` de M16 (500) |
| `ancho`, `alto` | `smallint` | Lado mayor ≤ 1.280 px |
| `created_at` | `datetime(3)` | |

### `respuestas_mareo`

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | Generado en el cliente |
| `tripulante_id` | `uuid` | |
| `dia_mision` | `tinyint` | 1, 2 o 3; lo calcula el servidor a partir de `respondida_en` |
| `nausea`, `vomito`, `mareo`, `cefalea`, `malestar` | `tinyint` | 0 – 10 cada uno |
| `total` | `tinyint` | Suma (0 – 50) |
| `origen` | `varchar(10)` | `manual` o `simulador` |
| `respondida_en` | `datetime(3)` | |
| `created_at` | `datetime(3)` | |

Única: `(tripulante_id, dia_mision)`. El cuestionario es una **estructura propia** con la forma de los cuestionarios de mareo; un instrumento validado lo elegirían profesionales.

### Catálogo de síntomas (sembrado en M16)

`cefalea`, `nausea`, `vomito`, `mareo`, `dolor_espalda`, `congestion_nasal`, `lesion_cutanea`, `prurito`, `fatiga`, `insomnio`, `dolor_otro`. Cada uno se publica con el código `AH-SINTOMA-<TIPO>` en mayúsculas (p. ej., `AH-SINTOMA-CEFALEA`). Esos códigos **no** se guardan en `catalogo_variables` (`AH-SINTOMA-CONGESTION_NASAL` tiene 27 caracteres y la columna, 20); solo viajan en los eventos. Columnas exactas en las [historias de base de datos](HU/README.md).

## 2. Migraciones y seeders

```
├── Database/Migrations/{create_sintomas_registrados,create_respuestas_mareo,create_fotos_sintoma}.php
```

## 3. Relaciones

| Desde | Hacia | Tipo |
|---|---|---|
| `fotos_sintoma.sintoma_id` | `sintomas_registrados.id` | `FOREIGN KEY`, única |
| `sintomas_registrados.tripulante_id`, `respuestas_mareo.tripulante_id` | `tripulantes.id` (M02) | Lógica |
| `sintomas_registrados.tipo` | `catalogo_sintomas.codigo` (M16) | Lógica |

## 4. Consultas principales

| Consulta | Índice |
|---|---|
| Síntomas de un tipo en una ventana (correlación CO₂–cefalea) | `(tripulante_id, tipo, inicio_en)` |
| ¿Respondió el cuestionario de hoy? | Única `respuestas_mareo (tripulante_id, dia_mision)` |

## 5. Volumen

Pocas filas (los síntomas son eventos). Las fotos están en el disco privado, `sintomas/<tripulante_id>/` (≤ `adjunto_foto_max_kb` cada una, hoy 500 KB).

## 6. Seeders

Ninguno (el catálogo de síntomas lo siembra M16).
