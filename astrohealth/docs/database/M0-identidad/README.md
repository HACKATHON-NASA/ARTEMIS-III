# 🔐 M0 · Identidad y acceso · Base de datos

> [← Base de datos](../README.md) · Código: `astrohealth/app/Modules/Identidad/Database/` · Diseño: [M0 · backend §5](../../backend/M0-identidad/README.md#5-datos)

| Estado         | Fase  | Responsable |
| -------------- | :---: | ----------- |
| ✅ Terminado (2026-11-14) | **1** | Backend |

Este documento cuenta **cómo quedó hecho**. El diseño está en el documento del backend de M0.

## Avance

**Tablas**

- [x] users: + role, + active, + astronaut_id (único, nullable → astronauts)
- [x] devices (client_id único, secret_hash, mission_id, astronaut_id nullable)
- [x] refresh_tokens (token_hash único, índice family_id)

**Seeders**

- [x] UserSeeder (admin, médico en Tierra, 3 astronautas, médico de la tripulación) — después de MissionSeeder (M6)
- [x] DeviceSeeder (simulador, con SIMULADOR_CLIENT_ID y SIMULADOR_CLIENT_SECRET del .env)

## Tablas creadas (MySQL · MariaDB 10.4)

| Tabla | Migración | Índices y llaves | Notas |
|---|---|---|---|
| `users` (+ columnas) | `2026_11_15_000300_add_access_columns_to_users_table` | `role` índice · `astronaut_id` único → `astronauts` (`ON DELETE SET NULL`) | `role VARCHAR(30) NULL` · `active TINYINT(1) DEFAULT 1` · `last_login_at DATETIME NULL` |
| `devices` | `2026_11_15_000400_create_devices_table` | `client_id` único · `mission_id` → `missions` (cascada) · `astronaut_id` → `astronauts` (`SET NULL`) | `secret_hash` (bcrypt) · `last_seen_at DATETIME` |
| `refresh_tokens` | `2026_11_15_000500_create_refresh_tokens_table` | `token_hash CHAR(64)` único · `family_id CHAR(36)` índice · `user_id` → `users` (cascada) | Fechas `DATETIME` (no `TIMESTAMP`): ver decisiones |

## Seeders y datos iniciales

`php artisan migrate:fresh --seed` deja: la misión Ares-1 con 4 astronautas (M6), 6 usuarios (uno por rol, contraseña `password`, **solo fuera de producción**) y el dispositivo `sim-ares-1` del simulador (si `SIMULADOR_CLIENT_SECRET` está en `.env`; si no, avisa y no lo crea). Se pueden correr varias veces sin duplicar.

## Decisiones técnicas

- `DATETIME` y no `TIMESTAMP` en las fechas que no son `created_at`/`updated_at`: con `explicit_defaults_for_timestamp=OFF` (MariaDB 10.4), la primera `TIMESTAMP NOT NULL` recibiría `ON UPDATE CURRENT_TIMESTAMP`. `UsersTableTest` lo vigila.
- `role` *nullable* para no romper el registro de Fortify; sin rol no se entra a la API.
- Todas las migraciones tienen `down()` y se probó el *rollback* de las 5.

## Diferencias con el diseño

Ninguna en las columnas. Se agregó `last_login_at`.

> 📝 **Documentar siempre:** este documento se actualiza en el mismo _pull request_ que cambia el código.
