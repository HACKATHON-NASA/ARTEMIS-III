# 🔐 M0 · Identidad y acceso · Base de datos

> [← Base de datos](../README.md) · Código: `astrohealth/app/Modules/Identidad/Database/` · Diseño: [M0 · backend §5](../../backend/M0-identidad/README.md#5-datos)

| Estado | Fase | Responsable |
|---|:---:|---|
| ⬜ Sin empezar | **1** | Backend |

Este documento cuenta **cómo quedó hecho**. El diseño está en el documento del backend de M0.

## Avance

**Tablas**
- [ ] users: + role, + active, + astronaut_id (único, nullable → astronauts)
- [ ] devices (client_id único, secret_hash, mission_id, astronaut_id nullable)
- [ ] refresh_tokens (token_hash único, índice family_id)

**Seeders**
- [ ] UserSeeder (admin, médico de vuelo, 4 astronautas) — después de MissionSeeder (M6)
- [ ] DeviceSeeder (simulador, con SIMULADOR_CLIENT_ID y SIMULADOR_CLIENT_SECRET del .env)

## Tablas creadas

| Tabla | Migración | Índices y llaves | Notas |
|---|---|---|---|
| | | | |

## Seeders y datos iniciales

_Pendiente._

## Decisiones técnicas

_Pendiente._

## Diferencias con el diseño

_Pendiente._

> 📝 **Documentar siempre:** este documento se actualiza en el mismo *pull request* que cambia el código.
