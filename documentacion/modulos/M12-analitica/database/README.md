# M12 · Analítica y línea base · Base de datos

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · [Frontend](../frontend/README.md) · **Base de datos**

Tablas **MySQL** que este módulo posee: es el único que las migra y las escribe ([reglas de comunicación](../../README.md#4-reglas-de-comunicación-entre-módulos)). Siguen las [convenciones de base de datos](../../README.md#7-convenciones-de-base-de-datos).

---

## 1. Tablas

### `lineas_base`

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | |
| `tripulante_id` | `uuid` | |
| `codigo` | `varchar(20)` | Variable o métrica (p. ej., `8867-4`, `AH-PVT-MEDIANA`) |
| `contexto` | `varchar(10)` nulo | `reposo` para los vitales; `null` para métricas sin contexto |
| `media` | `decimal(12,4)` | |
| `desviacion` | `decimal(12,4)` | |
| `n` | `int` | Valores usados |
| `fuente` | `varchar(25)` | `previa_vuelo`, `ventana_inicial`, `recalculo_autorizado` |
| `ventana_desde`, `ventana_hasta` | `datetime(3)` nulo | |
| `autorizada_por` | `bigint` nulo | Médico de vuelo (solo `recalculo_autorizado`) |
| `vigente` | `boolean` | Una sola vigente por `(tripulante_id, codigo, contexto)` |
| `created_at` | `datetime(3)` | |

### `hallazgos`

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | |
| `tripulante_id` | `uuid` nulo | `null` = toda la tripulación |
| `regla_codigo` | `varchar(30)` | |
| `familia` | `varchar(30)` | Agrupa reglas de la misma variable para que M13 deduplique y escale (p. ej., `SPO2` agrupa `SPO2_AVISO` y `SPO2_CRITICO`) |
| `sistema` | `varchar(10)` | `corazon`, `sueno`, `mente`, `radiacion`, `cuerpo`, `ambiente` |
| `nivel` | `varchar(8)` | `info`, `aviso`, `critico` |
| `explicacion` | `json` | Formato de [backend §3.5](../backend/README.md#35-explicación-hu-1203) |
| `ventana_desde`, `ventana_hasta` | `datetime(3)` | Datos evaluados (`ventana_hasta` avanza mientras el episodio está abierto) |
| `modulo_nave_id` | `uuid` nulo | Módulo de un hallazgo ambiental global |
| `valor_extremo` | `decimal(12,4)` nulo | Peor valor del episodio |
| `ocurrencias` | `smallint` | Ventanas de persistencia completadas en el episodio |
| `abierto` | `boolean` | `false` al cerrarse el episodio |
| `detectado_en`, `actualizado_en` | `datetime(3)` | |

Índices: `(tripulante_id, regla_codigo, abierto)`, `(tripulante_id, detectado_en)` y `(modulo_nave_id, regla_codigo, abierto)`.

### `solicitudes_recalculo`

Solicitud de recálculo de una línea base (Tierra → nave): `id`, `tripulante_id`, `codigo`, `contexto`, `ventana_desde`, `ventana_hasta`, `motivo`, `solicitado_por`, `estado` (`pendiente`, `completada`, `rechazada`), `linea_base_id`, `motivo_rechazo`. Columnas exactas en las [historias de base de datos](HU/README.md).

## 2. Migraciones y seeders

```
├── Database/Migrations/{create_lineas_base,create_hallazgos,create_solicitudes_recalculo}.php
```

## 3. Relaciones

| Desde | Hacia | Tipo |
|---|---|---|
| `lineas_base.tripulante_id`, `hallazgos.tripulante_id` | `tripulantes.id` (M02) | Lógica |
| `lineas_base.codigo` | `catalogo_variables.codigo` (M16) | Lógica |
| `lineas_base.autorizada_por` | `users.id` (M01) | Lógica |
| `hallazgos.regla_codigo` | `umbrales.regla_codigo` (M16) o regla especializada | Lógica |

"Una línea base vigente por `(tripulante_id, codigo, contexto)`" la garantiza el caso de uso dentro de una transacción.

## 4. Consultas principales

| Consulta | Índice |
|---|---|
| Línea base vigente | `lineas_base (tripulante_id, codigo, contexto, vigente)` |
| Hallazgos de un día (reporte diario) | `hallazgos (tripulante_id, regla_codigo, detectado_en)` y `(tripulante_id, detectado_en)` |

## 5. Volumen

`lineas_base`: unas 15 filas vigentes por tripulante. `hallazgos`: en E0 solo `info` (pocas al día); los escenarios añaden decenas.

## 6. Orden de creación

`lineas_base` se migra en el **Sprint 1** (HU-02.03) aunque pertenezca a M12; `hallazgos` en el Sprint 3 (HU-12.02).
