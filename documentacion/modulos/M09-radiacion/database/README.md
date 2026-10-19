# M09 · Radiación · Base de datos

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · [Frontend](../frontend/README.md) · **Base de datos**

Tablas **MySQL** que este módulo posee: es el único que las migra y las escribe ([reglas de comunicación](../../README.md#4-reglas-de-comunicación-entre-módulos)). Siguen las [convenciones de base de datos](../../README.md#7-convenciones-de-base-de-datos).

---

## 1. Tablas

### `dosis_diarias`

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | UUID v5 de `dosis:<tripulante_id>:<fecha>`, igual en los dos nodos |
| `tripulante_id` | `uuid` | |
| `fecha` | `date` | UTC |
| `dosis_msv` | `decimal(10,4)` | Dosis de ese día |
| `acumulada_mision_msv` | `decimal(10,4)` | Desde el inicio de la misión |
| `actualizada_en` | `datetime(3)` | |

Única: `(tripulante_id, fecha)`. Se recalcula el día afectado **y los siguientes** con cada `LoteIngerido` de `AH-DOSIS-TASA`.

Columna adicional: `minutos_sin_dato` (`smallint`): minutos del día (en el día de hoy, solo hasta la última lectura) sin una lectura válida en los 5 minutos anteriores (la dosis de esos minutos no se cuenta). Detalle en las [historias de base de datos](HU/README.md).

### `dosis_carrera_previa`

| Columna | Tipo | Notas |
|---|---|---|
| `tripulante_id` | `uuid` | Clave primaria |
| `dosis_previa_msv` | `decimal(10,4)` | Dosis de carrera antes de esta misión (dato ficticio de la demo) |

## 2. Migraciones y seeders

```
├── Database/
│   ├── Migrations/{create_dosis_diarias,create_dosis_carrera_previa}.php
│   └── Seeders/DosisPreviaSeeder.php
```

## 3. Relaciones

| Desde | Hacia | Tipo |
|---|---|---|
| `dosis_diarias.tripulante_id`, `dosis_carrera_previa.tripulante_id` | `tripulantes.id` (M02) | Lógica |

## 4. Consultas principales

| Consulta | Índice |
|---|---|
| Dosis acumulada y últimas 30 días | Única `(tripulante_id, fecha)` |
| Tasa reciente para la regla de evento solar | `observaciones (tripulante_id, codigo, medido_en)` de M03, vía contrato |

## 5. Volumen

`dosis_diarias`: una fila por tripulante y día (*upsert* con cada lote). `dosis_carrera_previa`: una fila por tripulante.

## 6. Seeders

`DosisPreviaSeeder`: dosis de carrera previa **ficticia** por tripulante (p. ej., T1 120 mSv, T2 0, T3 45, T4 80) para que la barra de F1 no empiece en cero.
