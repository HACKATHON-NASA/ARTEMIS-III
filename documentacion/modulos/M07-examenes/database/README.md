# M07 · Exámenes médicos · Base de datos

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · [Frontend](../frontend/README.md) · **Base de datos**

Tablas **MySQL** que este módulo posee: es el único que las migra y las escribe ([reglas de comunicación](../../README.md#4-reglas-de-comunicación-entre-módulos)). Siguen las [convenciones de base de datos](../../README.md#7-convenciones-de-base-de-datos).

---

## 1. Tablas

### `resultados_examen` (solo inserción)

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | Generado en el cliente |
| `tripulante_id` | `uuid` | |
| `prueba_codigo` | `varchar(20)` | Código de `catalogo_pruebas` (M16) |
| `fecha_muestra` | `date` | Define el orden; **no** la fecha de carga |
| `valor` | `decimal(12,4)` | |
| `unidad` | `varchar(20)` | Debe ser igual a la del catálogo |
| `interpretacion` | `varchar(10)` | Al cargar: `bajo`, `normal`, `alto`, `critico` |
| `delta_superado` | `boolean` | |
| `tendencia_desfavorable` | `boolean` | |
| `cargado_por` | `bigint` nulo | Usuario; `null` si lo cargó el simulador |
| `origen` | `varchar(10)` | `formulario`, `csv`, `simulador` |
| `anula_id` | `uuid` nulo | Corrección de un resultado anterior (otro resultado de la misma prueba y la misma `fecha_muestra`) |
| `created_at` | `datetime(3)` | |

Índice: `(tripulante_id, prueba_codigo, fecha_muestra)`. "Un resultado vigente por prueba y fecha de muestra" lo garantiza el caso de uso (MySQL no tiene índices únicos parciales).

### `examenes_programados`

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | |
| `tripulante_id` | `uuid` | |
| `prueba_codigo` | `varchar(20)` | |
| `frecuencia_dias` | `smallint` | |
| `proxima_fecha` | `date` | Se recalcula al cargar un resultado de esa prueba |

Siembra de la demostración (parámetros de diseño): Hb y Hto cada 7 días, los lunes; vitamina D, OCT y Doppler cada 30 días.

### `documentos_examen` (HU-07.05)

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | |
| `tripulante_id` | `uuid` | |
| `fecha_muestra` | `date` | Agrupa el PDF con los resultados de esa fecha |
| `nombre_original` | `varchar(150)` | |
| `ruta` | `varchar(255)` | `examenes/<tripulante_id>/<id>.pdf` en el disco privado `local` (en Laravel 11 su raíz es `storage/app/private`) |
| `bytes` | `int` | ≤ `adjunto_pdf_max_kb` de M16 (2048) |
| `sha256` | `char(64)` | Para no guardar dos veces el mismo archivo |
| `cargado_por` | `bigint` | |
| `anula_id` | `uuid` nulo | Documento al que **reemplaza** (la fila vieja no se modifica) |
| `created_at` | `datetime(3)` | |

Volver a cargar el PDF de una fecha crea una fila nueva con `anula_id` apuntando a la anterior, que se conserva sin cambios; el vigente de cada fecha es el que ninguna otra fila anula. Columnas exactas en las [historias de base de datos](HU/README.md).

## 2. Migraciones y seeders

```
├── Database/
│   ├── Migrations/{create_resultados_examen,create_examenes_programados,create_documentos_examen}.php
│   └── Seeders/ExamenesProgramadosSeeder.php
```

## 3. Relaciones

| Desde | Hacia | Tipo |
|---|---|---|
| `resultados_examen.anula_id` | `resultados_examen.id` | `FOREIGN KEY` (nula) |
| `documentos_examen.anula_id` | `documentos_examen.id` | `FOREIGN KEY` (nula) |
| `*.tripulante_id` | `tripulantes.id` (M02) | Lógica |
| `resultados_examen.prueba_codigo`, `examenes_programados.prueba_codigo` | `catalogo_pruebas.codigo` (M16) | Lógica |
| `resultados_examen.cargado_por`, `documentos_examen.cargado_por` | `users.id` (M01) | Lógica |

Única: `examenes_programados (tripulante_id, prueba_codigo)`.

## 4. Consultas principales

| Consulta | Índice |
|---|---|
| Historial de una prueba (gráfica, *delta*, tendencia) | `resultados_examen (tripulante_id, prueba_codigo, fecha_muestra)` |
| Resultados y PDF de una fecha | `resultados_examen (tripulante_id, fecha_muestra)`, `documentos_examen (tripulante_id, fecha_muestra)` |
| Recordatorios | `examenes_programados (tripulante_id, proxima_fecha)` |

## 5. Volumen

Decenas de resultados por tripulante y mes. Los PDF están en el disco privado (`examenes/<tripulante_id>/`, ≤ `adjunto_pdf_max_kb` cada uno, hoy 2 MB), no en la base de datos.

## 6. Seeders

`ExamenesProgramadosSeeder`: para cada tripulante, Hb (`718-7`) y Hto (`4544-3`) cada 7 días; vitamina D (`62292-8`), OCT (`AH-OCT-DTRT`) y Doppler (`AH-DOPPLER-IJV`) cada 30 días. La primera fecha de Hb y Hto es el **primer lunes** de la misión y la de las demás, el día 1: así coincide con la agenda del simulador (Hb y Hto los lunes; vitamina D, OCT y Doppler los días 1 y 31) y los recordatorios no aparecen como "vencidos" por un desfase de días.
