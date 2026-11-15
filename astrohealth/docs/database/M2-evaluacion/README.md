# 🚦 M2 · Motor de evaluación · Base de datos

> [← Base de datos](../README.md) · Código: `astrohealth/app/Modules/Evaluacion/Database/` · Qué construir: [ficha de M2](../../../../documentacion/modulos/M2-evaluacion/README.md#3-datos)

| Estado   | Fase | Responsable |
| -------- | :--: | ----------- |
| ✅ Hecho |  F2  | Backend     |

Este documento cuenta **cómo quedó hecho**. **Qué** hay que construir está en la ficha: aquí no se copia, se enlaza.

## Avance

**Tablas**

- [x] thresholds (indicator_code + environment, único)
- [x] assessments (con body_status json)

**Seeders**

- [x] ThresholdSeeder (10 indicadores × 4 entornos)

## Tablas creadas

| Tabla         | Migración                                    | Índices y llaves                                                      | Notas                                                                          |
| ------------- | -------------------------------------------- | --------------------------------------------------------------------- | ------------------------------------------------------------------------------ |
| `thresholds`  | `2026_11_16_000300_create_thresholds_table`  | único `(indicator_code, environment)`                                 | Límites `DECIMAL(8,2)`; el entorno es texto (`iss`, `transit`, `moon`, `mars`) |
| `assessments` | `2026_11_16_000400_create_assessments_table` | `astronaut_id` → `astronauts` (cascada) · índice `(astronaut_id, id)` | `score TINYINT` 0–100 · `body_status JSON` · sin `updated_at`                  |

## Seeders y datos iniciales

`ThresholdSeeder` carga los umbrales del plan §2 (iguales en los 4 entornos) y la radiación de cada entorno con la dosis de `config/environments.php`: verde hasta la dosis, amarillo hasta el doble (Marte 0,6 / 1,2; tránsito 1,8 / 3,6). **Solo crea lo que falta**, así volver a sembrar no pisa lo que los médicos cambiaron en la Guía. Va en `DatabaseSeeder` después del dispositivo.

## Decisiones técnicas

- Se usa el **código** del indicador y no su id: M2 no depende de M1 (es M1 quien usa a M2).
- `assessments` guarda **una fila por evaluación**: queda el historial del puntaje. El estado general no se guarda: sale del puntaje con `OverallStatus`.

## Diferencias con la ficha

- Se agregó un índice `(astronaut_id, id)` para leer rápido la última evaluación.

> 📝 **Documentar siempre:** este documento se actualiza en el mismo _pull request_ que cambia el código.
