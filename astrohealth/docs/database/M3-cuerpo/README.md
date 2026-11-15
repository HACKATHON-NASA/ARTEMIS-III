# 🧍 M3 · Cuerpo · Base de datos

> [← Base de datos](../README.md) · Código: `astrohealth/app/Modules/Cuerpo/Database/` · Qué construir: [ficha de M3](../../../../documentacion/modulos/M3-cuerpo/README.md#3-datos)

| Estado   | Fase | Responsable |
| -------- | :--: | ----------- |
| ✅ Hecho |  F4  | Backend     |

Este documento cuenta **cómo quedó hecho**. **Qué** hay que construir está en la ficha: aquí no se copia, se enlaza.

## Avance

- [x] body_parts (9 zonas)
- [x] clinical_notes (nueva frente al plan)
- [x] BodyPartSeeder · DemoClinicalNotesSeeder (solo fuera de producción)

## Tablas creadas

| Tabla            | Migración                                       | Índices y llaves                                                                                                                    | Notas                                                                                                                     |
| ---------------- | ----------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------- |
| `body_parts`     | `2026_11_16_000800_create_body_parts_table`     | `code` único                                                                                                                        | `function_text`, `space_risk_text` (NASA HRP), `hrp_hazard`, `sort_order`                                                 |
| `clinical_notes` | `2026_11_16_000900_create_clinical_notes_table` | `astronaut_id` → `astronauts` (cascada) · `author_id` y `resolved_by_id` → `users` (set null) · índice `(astronaut_id, created_at)` | `author_name` y `resolved_by_name` guardados · `kind` (`evolution`, `impression`, `plan`, `follow_up`) · sin `updated_at` |

## Seeders y datos iniciales

`BodyPartSeeder` carga las 9 zonas (las 7 del plan más metabolismo y todo el cuerpo) con los mismos textos que tenía el frontend. `DemoClinicalNotesSeeder` carga las 4 notas de Ares-1 solo si no hay ninguna.

## Decisiones técnicas

- Los indicadores de cada zona **no se guardan aquí**: salen del catálogo de M1 (`indicators.body_part`).
- Las notas guardan los **nombres** además de los ids: el expediente no cambia si el usuario cambia.

> 📝 **Documentar siempre:** este documento se actualiza en el mismo _pull request_ que cambia el código.
