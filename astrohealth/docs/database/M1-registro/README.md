# 🩺 M1 · Registro de indicadores · Base de datos

> [← Base de datos](../README.md) · Código: `astrohealth/app/Modules/Registro/Database/` · Qué construir: [ficha de M1](../../../../documentacion/modulos/M1-registro/README.md#3-datos)

| Estado   | Fase | Responsable |
| -------- | :--: | ----------- |
| ✅ Hecho |  F3  | Backend     |

Este documento cuenta **cómo quedó hecho**. **Qué** hay que construir está en la ficha: aquí no se copia, se enlaza.

## Avance

**Tablas**

- [x] indicators
- [x] measurements (índice astronaut_id, indicator_id, measured_at · reading_id único)
- [x] ingest_batches (batch_id único: idempotencia de la ingesta)

**Seeders**

- [x] IndicatorSeeder (10 indicadores)

## Tablas creadas

| Tabla            | Migración                                       | Índices y llaves                                                                                                                                                     | Notas                                                                         |
| ---------------- | ----------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------- |
| `indicators`     | `2026_11_16_000500_create_indicators_table`     | `code` único                                                                                                                                                         | Rango posible `DECIMAL(10,3)` · `help_how` y `help_why` (el "¿Cómo me mido?") |
| `ingest_batches` | `2026_11_16_000600_create_ingest_batches_table` | `batch_id` único · `device_id` → `devices` (cascada)                                                                                                                 | `rejected JSON` · solo `created_at`                                           |
| `measurements`   | `2026_11_16_000700_create_measurements_table`   | `reading_id` único · índice `(astronaut_id, indicator_id, measured_at)` · `indicator_id` → `indicators` (restrict) · `ingest_batch_id` → `ingest_batches` (set null) | `value DECIMAL(10,3)` · `status` lo pone M2 · `source` siempre presente (P12) |

## Seeders y datos iniciales

_Pendiente._

## Decisiones técnicas

_Pendiente._

## Diferencias con la ficha

_Pendiente._

> 📝 **Documentar siempre:** este documento se actualiza en el mismo _pull request_ que cambia el código.
