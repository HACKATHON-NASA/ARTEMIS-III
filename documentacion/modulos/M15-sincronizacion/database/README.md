# M15 · Sincronización · Base de datos

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · [Frontend](../frontend/README.md) · **Base de datos**

Tablas **MySQL** que este módulo posee: es el único que las migra y las escribe ([reglas de comunicación](../../README.md#4-reglas-de-comunicación-entre-módulos)). Siguen las [convenciones de base de datos](../../README.md#7-convenciones-de-base-de-datos).

---

## 1. Tablas

### `paquetes_sync` (salida, en ambos nodos)

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | Clave de idempotencia de punta a punta |
| `tipo` | `varchar(20)` | Ver [backend §3.2](../backend/README.md#32-tipos-de-paquete-y-prioridades) |
| `prioridad` | `tinyint` | `1`, `2` o `3` |
| `payload` | `longtext` (JSON) | |
| `estado` | `varchar(10)` | `pendiente`, `enviado` (en tránsito), `entregado` |
| `creado_en` | `datetime(3)` | |
| `enviado_en` | `datetime(3)` nulo | Salida por el enlace |
| `entrega_programada_en` | `datetime(3)` nulo | `enviado_en` + retardo |
| `entregado_en` | `datetime(3)` nulo | Acuse recibido |
| `intentos` | `smallint` | Reintentos de entrega cuando la Tierra no responde |
| `bytes` | `int` | Tamaño del `payload`, para el cupo por ciclo |
| `comprimido` | `boolean` | `payload` en gzip + base64 (`detalle`) |

Índice: `(estado, prioridad, creado_en)`.

### `paquetes_recibidos` (en ambos nodos)

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | El mismo `id` del paquete de origen |
| `tipo`, `prioridad`, `payload`, `comprimido` | — | Como en `paquetes_sync` |
| `estado` | `varchar(12)` | `en_transito` (solo en la nave, mientras no se cumple el retardo), `recibido` (pendiente de importar), `procesado` o `error` |
| `error`, `intentos` | `text` nulo, `smallint` | Fallo de la importación y reintentos (hasta 5) |
| `entrega_programada_en` | `datetime(3)` nulo | |
| `recibido_en`, `procesado_en` | `datetime(3)` | |

### `ventanas_enlace` (solo en la nave)

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | |
| `inicio`, `fin` | `datetime(3)` | Sin enlace entre ambas |
| `motivo` | `varchar(15)` | `conjuncion` o `manual` |

### `recomendaciones` (en ambos nodos)

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | |
| `tripulante_id` | `uuid` nulo | `null` = para toda la tripulación |
| `alerta_id` | `uuid` nulo | Alerta a la que responde |
| `autor_id` | `bigint` | Médico de vuelo (usuario de la Tierra) |
| `autor` | `varchar(150)` | Copia legible: ese usuario no existe en la nave |
| `texto` | `text` | |
| `prioridad` | `varchar(8)` | `normal` o `urgente` |
| `creada_en` | `datetime(3)` | En la Tierra |
| `recibida_en` | `datetime(3)` nulo | En la nave |
| `leida_en`, `leida_por` | — | En la nave |

### `detalle_empaquetado` (solo en la nave)

`lote_id` (PK), `paquete_id`, `empaquetado_en`: qué lotes de M03 ya viajan en un paquete `detalle` (los lotes no se pueden marcar porque `lotes_ingesta` es de solo inserción). Columnas exactas en las [historias de base de datos](HU/README.md).

## 2. Migraciones y seeders

```
├── Database/Migrations/{create_paquetes_sync,create_detalle_empaquetado,create_paquetes_recibidos,create_ventanas_enlace,create_recomendaciones}.php
```

## 3. Relaciones

| Desde | Hacia | Tipo |
|---|---|---|
| `recomendaciones.tripulante_id` | `tripulantes.id` (M02) | Lógica |
| `recomendaciones.alerta_id` | `alertas.id` (M13) | Lógica |
| `recomendaciones.autor_id`, `recomendaciones.leida_por` | `users.id` (M01) | Lógica |
| `paquetes_recibidos.id` | `paquetes_sync.id` **del otro nodo** | Lógica entre nodos (misma clave de punta a punta) |

## 4. Consultas principales

| Consulta | Índice |
|---|---|
| Siguientes `cupo` paquetes por prioridad (`sync:ciclo`) | `paquetes_sync (estado, prioridad, creado_en)` |
| Entregas vencidas | `paquetes_sync (estado, entrega_programada_en)`, `paquetes_recibidos (estado, entrega_programada_en)` |
| Importación en orden (`sync:procesar-recibidos`) | `paquetes_recibidos (estado, prioridad, recibido_en)` |
| Idempotencia de recepción | Clave primaria de `paquetes_recibidos` |
| Ventana activa | `ventanas_enlace (inicio, fin)` |

## 5. Volumen

`paquetes_sync`: un paquete `detalle` por dispositivo y hora (comprimido), uno `registros` por registro, uno `resumen` por hora y los `alerta` y `adjunto`. En 14 días sin enlace (E5) se acumulan unos **10.000** paquetes `pendiente` (~20 MB comprimidos); con el cupo en bytes se vacían en unos 5 minutos con `leo` y en unos 50 minutos con `marte` ([backend §3.4](../backend/README.md#34-ventanas-sin-enlace-e5)). Los `payload` grandes (adjuntos en base64) son `longtext`.

## 6. Retención

Los paquetes `entregado` se pueden purgar a los 30 días (comando opcional; no es parte del MVP).
