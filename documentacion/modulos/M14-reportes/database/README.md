# M14 · Reportes · Base de datos

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · [Frontend](../frontend/README.md) · **Base de datos**

Tablas **MySQL** que este módulo posee: es el único que las migra y las escribe ([reglas de comunicación](../../README.md#4-reglas-de-comunicación-entre-módulos)). Siguen las [convenciones de base de datos](../../README.md#7-convenciones-de-base-de-datos).

---

## 1. Tablas

### `reportes_generados`

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | |
| `tipo` | `varchar(15)` | `diario`, `mision` o `exportacion` |
| `tripulante_id` | `uuid` nulo | En `diario` y `mision` |
| `fecha` | `date` nulo | Día del reporte diario; en `mision`, último día incluido |
| `audiencia` | `varchar(6)` nulo | `medico` o `cmo` en los diarios y de misión; `null` en las exportaciones |
| `nodo_origen` | `varchar(6)` | `nave` o `tierra` (el reporte automático llega de la nave) |
| `estado` | `varchar(10)` | `pendiente`, `listo` o `error` (las exportaciones se generan en cola) |
| `error` | `varchar(300)` nulo | |
| `parametros` | `json` nulo | Filtros de la exportación |
| `ruta_archivo` | `varchar(255)` nulo | `reportes/<tipo>/<id>.<pdf|csv>` en el disco privado; `null` mientras está pendiente |
| `bytes` | `int` nulo | Tamaño del archivo |
| `generado_por` | `bigint` nulo | `null` si lo generó el programador de tareas |
| `generado_en` | `datetime(3)` | |

Única: `(tipo, tripulante_id, fecha, audiencia, nodo_origen)` para los diarios y los de misión (regenerar reemplaza el archivo y conserva la fila). Así el reporte que llega de la nave y uno generado a demanda en la Tierra el mismo día no chocan.

## 2. Migraciones y seeders

```
├── Database/Migrations/create_reportes_generados.php
```

## 3. Relaciones

| Desde | Hacia | Tipo |
|---|---|---|
| `reportes_generados.tripulante_id` | `tripulantes.id` (M02) | Lógica |
| `reportes_generados.generado_por` | `users.id` (M01) | Lógica |

## 4. Consultas principales

| Consulta | Índice |
|---|---|
| Lista por tipo, tripulante y fecha | Única `(tipo, tripulante_id, fecha, audiencia, nodo_origen)` |
| Exportaciones de un investigador | `(tipo, generado_por, generado_en)` |

## 5. Volumen

Un reporte diario por tripulante y día (~120 al mes), más los de misión y las exportaciones. Los archivos están en el disco privado, en `reportes/`. Columnas exactas en las [historias de base de datos](HU/README.md).

## 6. Seeders

Ninguno.
