# M06 · Sueño · Base de datos

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · [Frontend](../frontend/README.md) · **Base de datos**

Tablas **MySQL** que este módulo posee: es el único que las migra y las escribe ([reglas de comunicación](../../README.md#4-reglas-de-comunicación-entre-módulos)). Siguen las [convenciones de base de datos](../../README.md#7-convenciones-de-base-de-datos).

---

## 1. Tablas

### `registros_sueno`

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | Generado en el cliente |
| `tripulante_id` | `uuid` | |
| `noche` | `date` | Fecha UTC de (`despertado_en` − 12 h), calculada en el servidor ([backend §3, regla 1](../backend/README.md#3-reglas-de-negocio)) |
| `acostado_en` | `datetime(3)` nulo | Nulo solo en un registro manual de medicación |
| `despertado_en` | `datetime(3)` | |
| `duracion_h` | `decimal(4,2)` nulo | Tiempo dormido (no el tiempo en cama); nulo solo en un registro manual de medicación |
| `eficiencia_pct` | `decimal(5,2)` nulo | Tiempo dormido / tiempo en cama × 100, **calculada en el servidor**; nula como la duración |
| `origen` | `varchar(10)` | `actigrafia`, `manual`, `simulador`, según el token |
| `medicacion` | `boolean` nulo | `null` = no indicada (la actigrafía no lo sabe) |
| `medicacion_nombre` | `varchar(60)` nulo | |
| `luz_percibida`, `ruido_percibido` | `tinyint` nulo | 0 (nada) a 10 (mucho) |
| `anula_id` | `uuid` nulo | Corrección de un registro anterior |
| `created_at` | `datetime(3)` | |

Hay **un solo registro vigente** (no anulado) por `(tripulante_id, noche)`. Si ya existe uno y llega otro de la misma noche, se crea un registro nuevo con `anula_id` apuntando al anterior que **fusiona** ambos: la duración, la eficiencia y los horarios salen de la actigrafía (si alguno de los dos la tiene) y la medicación, la luz y el ruido salen del registro manual.

Códigos que publica: `AH-SUENO-DURACION` (`h`) y `AH-SUENO-EFICIENCIA` (`%`).

## 2. Migraciones y seeders

```
├── Database/Migrations/create_registros_sueno.php
```

## 3. Relaciones

| Desde | Hacia | Tipo |
|---|---|---|
| `registros_sueno.anula_id` | `registros_sueno.id` | `FOREIGN KEY` (nula) |
| `registros_sueno.tripulante_id` | `tripulantes.id` (M02) | Lógica |

"Un registro vigente por noche" lo garantiza el caso de uso con un bloqueo con nombre por tripulante y noche (`Cache::lock`), porque MySQL no tiene índices únicos parciales y, si la noche aún no tiene registro, no hay ninguna fila que bloquear con `SELECT … FOR UPDATE`. Detalle en las [historias de base de datos](HU/README.md).

## 4. Consultas principales

| Consulta | Índice |
|---|---|
| Últimas N noches vigentes de un tripulante | `(tripulante_id, noche)` |
| Registro vigente de una noche (el que ningún otro anula: `NOT EXISTS … WHERE x.anula_id = r.id`) | `(tripulante_id, noche)` y el índice de la clave foránea `anula_id` |

## 5. Volumen

Una fila por tripulante y noche (más las fusiones): ~120 filas al mes para 4 tripulantes.

## 6. Seeders

Ninguno.
