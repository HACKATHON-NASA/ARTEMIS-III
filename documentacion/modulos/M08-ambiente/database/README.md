# M08 · Ambiente · Base de datos

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · [Frontend](../frontend/README.md) · **Base de datos**

Tablas **MySQL** que este módulo posee: es el único que las migra y las escribe ([reglas de comunicación](../../README.md#4-reglas-de-comunicación-entre-módulos)). Siguen las [convenciones de base de datos](../../README.md#7-convenciones-de-base-de-datos).

---

## 1. Tablas

M08 **no tiene tablas propias**: sus series son observaciones ambientales de M03 (`modulo_nave_id` no nulo) y los módulos de la nave están en M02 (`modulos_nave`). Los promedios se calculan al consultar (1.440 lecturas de pCO₂ por día y módulo).

## 2. Migraciones y seeders

```
(Sin migraciones propias)
```

## 3. Dónde están sus datos

Resumen según la [propiedad de las tablas](../../README.md#6-propiedad-de-las-tablas):

| Dato | Dónde vive | Dueño |
|---|---|---|
| Lecturas de pCO₂, VOC, partículas y ruido | `observaciones` con `modulo_nave_id` no nulo | M03 |
| Módulos de la nave | `modulos_nave` | M02 |
| Reglas `CO2_CEFALEA` y `AMB_*` | `umbrales` | M16 |
| Hallazgos y alertas | `hallazgos`, `alertas` | M12, M13 |

## 4. Consultas principales

| Consulta | Índice que usa |
|---|---|
| Promedio horario de un módulo en 24 h | `observaciones (modulo_nave_id, codigo, medido_en)` |
| Promedio diario de 30 días (F3) | El mismo índice, agrupado por día (en caché 5 min, igual que el promedio de 7 días de F2) |

Consultas exactas (y su variante para SQLite) en las [historias de base de datos](HU/README.md).

## 5. Volumen

pCO₂: 3 módulos × 1.440 lecturas al día = 4.320. VOC, partículas y ruido: 3 módulos × 3 variables × 288 = 2.592 al día.
