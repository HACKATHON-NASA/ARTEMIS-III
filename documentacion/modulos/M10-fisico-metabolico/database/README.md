# M10 · Físico y metabólico · Base de datos

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · [Frontend](../frontend/README.md) · **Base de datos**

Tablas **MySQL** que este módulo posee: es el único que las migra y las escribe ([reglas de comunicación](../../README.md#4-reglas-de-comunicación-entre-módulos)). Siguen las [convenciones de base de datos](../../README.md#7-convenciones-de-base-de-datos).

---

## 1. Tablas

### `sesiones_ejercicio`

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | Generado en el cliente |
| `tripulante_id` | `uuid` | |
| `equipo` | `varchar(15)` | `ared`, `cinta`, `cicloergometro` |
| `inicio` | `datetime(3)` | |
| `duracion_min` | `smallint` | 1 – 240 |
| `fin` | `datetime(3)` | `inicio + duracion_min`, calculada en el servidor (comprobación de solapamiento) |
| `carga_kg` | `decimal(6,1)` nulo | Solo ARED |
| `fc_media`, `fc_max` | `smallint` nulo | Las enviadas; si faltan, se calculan **al mostrar** con las lecturas de FC del intervalo (no se guardan) |
| `origen` | `varchar(10)` | `manual`, `simulador` |
| `created_at` | `datetime(3)` | |

### `registros_nutricion` (HU-10.03)

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | Generado en el cliente |
| `tripulante_id` | `uuid` | |
| `tipo` | `varchar(8)` | `comida` o `liquido` |
| `descripcion` | `varchar(100)` nulo | |
| `kcal` | `smallint` nulo | Solo `comida`; 0 – 3000 |
| `proteina_g` | `smallint` nulo | Solo `comida`; 0 – 300 |
| `volumen_ml` | `smallint` nulo | Solo `liquido`; 1 – 3000 |
| `consumido_en` | `datetime(3)` | |
| `origen` | `varchar(10)` | `manual`, `simulador` |
| `created_at` | `datetime(3)` | |

### `pruebas_fuerza` (HU-10.04)

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | Generado en el cliente |
| `tripulante_id` | `uuid` | |
| `mano` | `varchar(9)` | `izquierda` o `derecha` |
| `valor_kg` | `decimal(5,1)` | 1 – 100 |
| `medido_en` | `datetime(3)` | |
| `origen` | `varchar(10)` | `manual`, `simulador` |
| `created_at` | `datetime(3)` | |

La masa corporal y la glucosa no tienen tabla propia: son las observaciones `29463-7` y `AH-GLUCOSA` de M03 (desde `masa-T<n>` y `cgm-T<n>`). Las tres tablas de M10 son de solo inserción y se corrigen con `anula_id`: columnas exactas en las [historias de base de datos](HU/README.md).

## 2. Migraciones y seeders

```
├── Database/Migrations/{create_sesiones_ejercicio,create_registros_nutricion,create_pruebas_fuerza}.php
```

## 3. Relaciones

| Desde | Hacia | Tipo |
|---|---|---|
| `sesiones_ejercicio.tripulante_id`, `registros_nutricion.tripulante_id`, `pruebas_fuerza.tripulante_id` | `tripulantes.id` (M02) | Lógica |

Restricciones de `registros_nutricion` (validadas en el *Form Request* y con `CHECK` si la versión de MySQL lo permite): `tipo = 'comida'` → `volumen_ml` nulo; `tipo = 'liquido'` → `kcal` y `proteina_g` nulos.

## 4. Consultas principales

| Consulta | Índice |
|---|---|
| Sesiones de la semana y solapamiento | `sesiones_ejercicio (tripulante_id, inicio)` |
| Totales del día | `registros_nutricion (tripulante_id, consumido_en)` |
| Historial de fuerza | `pruebas_fuerza (tripulante_id, medido_en)` |
| Masa y glucosa | `observaciones (tripulante_id, codigo, medido_en)` de M03, vía contrato |

## 5. Volumen

Por tripulante y día: 2 sesiones de ejercicio y ~7 registros de nutrición; 2 pruebas de fuerza por semana. La glucosa (288 lecturas al día por tripulante) vive en `observaciones`.

## 6. Seeders

Ninguno.
