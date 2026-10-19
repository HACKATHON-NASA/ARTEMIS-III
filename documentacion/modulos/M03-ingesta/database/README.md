# M03 · Ingesta de datos · Base de datos

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · [Frontend](../frontend/README.md) · **Base de datos**

Tablas **MySQL** que este módulo posee: es el único que las migra y las escribe ([reglas de comunicación](../../README.md#4-reglas-de-comunicación-entre-módulos)). Siguen las [convenciones de base de datos](../../README.md#7-convenciones-de-base-de-datos).

---

## 1. Tablas

### `lotes_ingesta`

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | Es el `lote_id` enviado por el cliente (clave de idempotencia) |
| `dispositivo_id` | `uuid` | Dispositivo del lote (con el token del simulador, el dispositivo simulado) |
| `tripulante_id` | `uuid` nulo | Lotes personales |
| `modulo_nave_id` | `uuid` nulo | Lotes ambientales |
| `modo` | `varchar(10)` | `vivo` o `historico` |
| `origen` | `varchar(10)` | `sensor` o `simulador`, según el dispositivo **dueño del token** |
| `enviado_en` | `datetime(3)` | Del cliente |
| `recibido_en` | `datetime(3)` | Del servidor |
| `medido_desde`, `medido_hasta` | `datetime(3)` nulo | Primer y último `medido_en` guardado (payload de `LoteIngerido` y consulta `/periodo` del simulador) |
| `n_lecturas`, `n_guardadas`, `n_artefactos`, `n_rechazadas`, `n_duplicadas` | `int` | Resumen devuelto al cliente |
| `detalle_rechazos` | `json` | `[{ "id": "...", "motivo": "fuera_de_rango" }]` |

Índices: `(medido_desde)` y `(dispositivo_id, recibido_en)`.

### `observaciones` (solo inserción)

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | Generado por el cliente; **ordenado en el tiempo** (ver §5) |
| `lote_id` | `uuid` | En el MVP todas las lecturas llegan por lote |
| `dispositivo_id` | `uuid` | |
| `tripulante_id` | `uuid` nulo | Lecturas personales |
| `modulo_nave_id` | `uuid` nulo | Lecturas ambientales |
| `codigo` | `varchar(20)` | Del [catálogo de variables](../../README.md#8-catálogo-de-códigos-de-variables) |
| `valor` | `decimal(12,4)` | Ya normalizado |
| `unidad` | `varchar(20)` | UCUM |
| `medido_en` | `datetime(3)` | UTC, momento de la medición |
| `calidad` | `decimal(3,2)` nulo | 0 a 1; `null` = sin dato (no es artefacto) |
| `contexto` | `varchar(10)` nulo | `reposo`, `ejercicio`, `sueno`; obligatorio en las personales y `null` en las ambientales |
| `origen` | `varchar(10)` | `sensor` o `simulador` (`manual` queda reservado para una carga manual futura) |
| `es_artefacto` | `boolean` | `true` si `calidad < 0,5` |
| `anula_id` | `uuid` nulo | Corrección de una lectura anterior |
| `created_at` | `datetime(3)` | |

Índices: `(tripulante_id, codigo, medido_en)`, `(modulo_nave_id, codigo, medido_en)`, `(lote_id)` y `(dispositivo_id, es_artefacto, medido_en)` (aviso de colocación).

## 2. Migraciones y seeders

```
├── Database/Migrations/{create_lotes_ingesta,create_observaciones}.php
```

## 3. Relaciones

| Desde | Hacia | Tipo |
|---|---|---|
| `observaciones.lote_id` | `lotes_ingesta.id` | `FOREIGN KEY` |
| `observaciones.anula_id` | `observaciones.id` | `FOREIGN KEY` (nula) |
| `lotes_ingesta.dispositivo_id`, `observaciones.dispositivo_id` | `dispositivos.id` (M02) | Lógica |
| `observaciones.tripulante_id` | `tripulantes.id` (M02) | Lógica |
| `observaciones.modulo_nave_id` | `modulos_nave.id` (M02) | Lógica |
| `observaciones.codigo` | `catalogo_variables.codigo` (M16) | Lógica (validada al ingerir) |

## 4. Consultas principales

| Consulta | Índice |
|---|---|
| Serie de un tripulante y variable en una ventana (M04, M12) | `(tripulante_id, codigo, medido_en)` |
| Serie ambiental por módulo (M08) | `(modulo_nave_id, codigo, medido_en)` |
| Lecturas de un lote (M15, `LoteIngerido`) | `(lote_id)` |
| Idempotencia por lectura | Clave primaria `id` |
| Artefactos en vivo de un dispositivo en los últimos `colocacion_ventana_min` (5 min; HU-03.03, unida con `lotes_ingesta.modo`) | `(dispositivo_id, es_artefacto, medido_en)` |
| Lotes que se solapan con un periodo (`/periodo` del simulador) | `lotes_ingesta (medido_desde)` |

## 5. Volumen

`observaciones` es **la tabla grande** del sistema: decenas de miles de filas al día y del orden de **1 millón** en el relleno de 30 días ([03 §3.3](../../../docs/propuesta/03-propuesta-tecnologica.md#33-volumen-de-datos-y-decisión-de-muestreo)). Por eso:

- se inserta en bloques (`insert` de hasta 1.000 filas por sentencia dentro de la transacción del lote);
- las gráficas largas leen `agregados_vitales` (M04), no `observaciones`;
- no hay más índices que los de la tabla anterior (cada índice encarece la inserción);
- los `id` son **UUID ordenados en el tiempo** (versión 7: los primeros 48 bits son el milisegundo de `medido_en`). Con UUID v4 aleatorios, cada inserción cae en un lugar distinto de la clave primaria de InnoDB y la carga se vuelve más lenta a medida que crece la tabla. El simulador los genera de forma determinista a partir de `medido_en` y de su semilla, así que repetir un relleno sigue produciendo los mismos `id`;
- el volumen y los tiempos de procesamiento en cPanel se miden al cierre del Sprint 1 ([backend §3.2](../backend/README.md#32-rendimiento-en-cpanel)).

Detalle de las migraciones en las [historias de base de datos](HU/README.md).

## 6. Seeders

Ninguno: los datos los genera el simulador a través de la API.
