# 🌍 M6 · Entorno de misión · Base de datos

> [← Base de datos](../README.md) · Código: `astrohealth/app/Modules/Entorno/Database/` · Qué construir: [ficha de M6](../../../../documentacion/modulos/M6-entorno/README.md#3-datos)

| Estado   | Fase | Responsable |
| -------- | :--: | ----------- |
| ✅ Hecho |  F1  | Backend     |

Este documento cuenta **cómo quedó hecho**. **Qué** hay que construir está en la ficha: aquí no se copia, se enlaza.

## Avance

**Tablas**

- [x] missions (+ `objective`, nombre único)
- [x] astronauts

**Seeders**

- [x] MissionSeeder (Ares-1 con 4 astronautas, Órbita-7 con 2, Selene-2 sin tripulación)

## Tablas creadas

| Tabla        | Migración                                           | Índices y llaves                    | Notas                                                                |
| ------------ | --------------------------------------------------- | ----------------------------------- | -------------------------------------------------------------------- |
| `missions`   | `2026_11_15_000100_create_missions_table`           | `environment` índice                | `environment VARCHAR(20)` (`Environment` en PHP) · `start_date DATE` |
| `missions`   | `2026_11_16_000200_add_objective_to_missions_table` | `name` único                        | `objective VARCHAR(255) NULL`                                        |
| `astronauts` | `2026_11_15_000200_create_astronauts_table`         | `mission_id` → `missions` (cascada) | `crew_role VARCHAR(60)` · `avatar` _nullable_                        |

## Seeders y datos iniciales

`MissionSeeder` deja una misión por estado, con fechas relativas a hoy: **Ares-1** (Marte, empezó hace 23 días, 30 días → hoy día 24), **Órbita-7** (ISS, terminó) y **Selene-2** (Luna, empieza en 21 días). Ares-1 va primero, así su tripulación toma los ids 1 a 4 que usa `UserSeeder`. Se puede correr varias veces: no cambia fechas ya creadas, solo completa el objetivo si faltaba.

## Decisiones técnicas

- **El estado no se guarda:** sale de `start_date` y `day_count` con la regla `MissionCalendar` (en UTC). Una columna `status` quedaría desactualizada al pasar los días.
- **Los números de los entornos no están en tablas:** viven en `config/environments.php` (D14).

## Diferencias con la ficha

- Se agregó `objective` (lo pide la pantalla Misiones) y el nombre único.
- `status` se calcula, no se guarda.

> 📝 **Documentar siempre:** este documento se actualiza en el mismo _pull request_ que cambia el código.
