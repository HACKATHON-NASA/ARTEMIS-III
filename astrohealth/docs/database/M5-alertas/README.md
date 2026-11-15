# 🚨 M5 · Alertas · Base de datos

> [← Base de datos](../README.md) · Código: `astrohealth/app/Modules/Alertas/Database/` · Qué construir: [ficha de M5](../../../../documentacion/modulos/M5-alertas/README.md#3-datos)

| Estado   | Fase | Responsable |
| -------- | :--: | ----------- |
| ✅ Hecho |  F5  | Backend     |

Este documento cuenta **cómo quedó hecho**. **Qué** hay que construir está en la ficha: aquí no se copia, se enlaza.

## Avance

- [x] recommendations (indicador + estado, único)
- [x] alerts (ampliada: tipo, nota, motivo, origen, retraso y respuesta)
- [x] advice (nueva frente al plan)
- [x] RecommendationSeeder (20) · DemoAlertsSeeder (solo fuera de producción)

## Tablas creadas

| Tabla             | Migración                                        | Índices y llaves                                                                                                                | Notas                                                                                                                                                                                                                         |
| ----------------- | ------------------------------------------------ | ------------------------------------------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `recommendations` | `2026_11_16_001000_create_recommendations_table` | único `(indicator_code, status)`                                                                                                | Se usa el código del indicador, como en `thresholds`                                                                                                                                                                          |
| `alerts`          | `2026_11_16_001100_create_alerts_table`          | `astronaut_id` → `astronauts` (cascada) · `measurement_id` único → `measurements` (set null) · índice `(astronaut_id, sent_at)` | `kind` (`vital`, `sos`, `help`, `report`) · `level` (`red`, `yellow`, `info`) · `source` (`device`, `manual`, `astronaut`) · `delay_seconds`, `sent_at`, `ground_eta_at` · `acknowledged_at`, `acknowledged_by_name`, `reply` |
| `advice`          | `2026_11_16_001200_create_advice_table`          | `astronaut_id` → `astronauts` (cascada) · `author_id` → `users` (set null) · índice `(astronaut_id, sent_at)`                   | `category`, `title`, `body`, `important`, `valid_days` · `delay_seconds`, `sent_at`, `arrives_at`, `read_at` · `author_name` guardado                                                                                         |

## Diferencias con la ficha

- `alerts` ya no tiene `sent_to_ground`: **toda alerta sale hacia Tierra al crearse**; se guarda cuándo llega (`ground_eta_at`) y la respuesta.
- Se agregaron los mensajes del tripulante (SOS, ayuda, novedad) y las indicaciones del médico (`advice`).

> 📝 **Documentar siempre:** este documento se actualiza en el mismo _pull request_ que cambia el código.
