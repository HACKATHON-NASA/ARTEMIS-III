# M16 · Configuración · Base de datos

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · [Frontend](../frontend/README.md) · **Base de datos**

Tablas **MySQL** que este módulo posee: es el único que las migra y las escribe ([reglas de comunicación](../../README.md#4-reglas-de-comunicación-entre-módulos)). Siguen las [convenciones de base de datos](../../README.md#7-convenciones-de-base-de-datos).

---

## 1. Tablas

### `umbrales` (versionada: un cambio crea una fila nueva)

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | |
| `mision_id` | `uuid` | Cada misión tiene sus umbrales ([backend §3.6](../backend/README.md#36-configuración-por-misión)) |
| `regla_codigo` | `varchar(30)` | Ver [backend §3.2](../backend/README.md#32-umbrales-sembrados) |
| `version` | `int` | |
| `familia` | `varchar(30)` | Para deduplicar y escalar en M13 |
| `variable_codigo` | `varchar(20)` nulo | Del catálogo de variables (nulo solo en reglas especializadas) |
| `evaluador` | `varchar(13)` | `generico` (motor de M12) o `especializada` (solo guarda valores que lee una regla de un módulo) |
| `contextos` | `json` nulo | `["reposo","sueno"]`; `null` = cualquiera |
| `operador` | `varchar(20)` | `mayor`, `menor`, `fuera_de`, `cambio_pct_mayor`, `perdida_pct_mayor` |
| `valor`, `valor2` | `decimal(12,4)` nulo | `valor2` solo para `fuera_de` y para `SUENO_MAL` (su segundo umbral, la eficiencia); `valor` solo puede ser nulo si `activo = false` |
| `nivel` | `varchar(8)` | `aviso` o `critico` |
| `persistencia_valor` | `smallint` nulo | |
| `persistencia_unidad` | `varchar(15)` | `min`, `consecutivos`, `media_7d`, `inmediato` |
| `sistema` | `varchar(10)` | `corazon`, `sueno`, `mente`, `radiacion`, `cuerpo`, `ambiente` |
| `activo` | `boolean` | Solo `true` si hay `valor` y `fuente` |
| `fuente` | `varchar(200)` nulo | Documento de origen del valor (p. ej., "04 §8") |
| `vigente` | `boolean` | Una sola fila vigente por `(mision_id, regla_codigo, origen)` |
| `origen` | `varchar(16)` | `tierra` o `ajuste_temporal` |
| `estado_revision` | `varchar(10)` nulo | Solo ajustes: `pendiente`, `confirmado`, `revertido` |
| `motivo` | `varchar(300)` | Obligatorio en cada cambio |
| `modificado_por` | `bigint` nulo | Nulo solo en las filas sembradas |
| `modificado_en` | `datetime(3)` | |

### `catalogo_variables`

| Columna | Tipo | Notas |
|---|---|---|
| `codigo` | `varchar(20)` | Clave primaria |
| `nombre` | `varchar(80)` | |
| `unidad` | `varchar(20)` | UCUM |
| `minimo_posible`, `maximo_posible` | `decimal(12,4)` | Validación técnica de M03 |
| `modulo` | `char(3)` | `M04`, `M05`… |
| `es_loinc` | `boolean` | `false` para los códigos `AH-*` |
| `origen` | `varchar(10)` | `sensor` o `derivado` |

### `catalogo_pruebas`

| Columna | Tipo | Notas |
|---|---|---|
| `codigo` | `varchar(20)` | Clave primaria |
| `nombre` | `varchar(80)` | |
| `unidad` | `varchar(20)` | UCUM |
| `tipo` | `varchar(12)` | `laboratorio` o `imagen` |
| `ref_min_h`, `ref_max_h`, `ref_min_m`, `ref_max_m` | `decimal(12,4)` nulo | Rango de referencia por sexo |
| `critico_min`, `critico_max` | `decimal(12,4)` nulo | |
| `delta_umbral` | `decimal(12,4)` nulo | |
| `direccion_desfavorable` | `varchar(6)` | `baja`, `sube` o `ambas` |
| `alerta_fuera_rango` | `boolean` | `true` en las pruebas de imagen |
| `activo` | `boolean` | Una prueba se desactiva, no se borra |
| `sistema` | `varchar(10)` | |

### `catalogo_sintomas`

| Columna | Tipo | Notas |
|---|---|---|
| `codigo` | `varchar(30)` | Clave primaria (p. ej., `cefalea`) |
| `nombre` | `varchar(60)` | |
| `activo` | `boolean` | |

### `parametros_mision`

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | v5 de `parametro:<mision_id>:<clave>` |
| `mision_id` | `uuid` | Cada misión tiene sus parámetros; única `(mision_id, clave)` |
| `clave` | `varchar(40)` | |
| `categoria` | `varchar(9)` | `clinico` (médico de vuelo) u `operativo` (admin) |
| `valor` | `json` | |
| `descripcion` | `varchar(200)` | |
| `fuente` | `varchar(200)` | Documento de origen o "parámetro de diseño" |

## 2. Migraciones y seeders

```
├── Database/
│   ├── Migrations/{create_umbrales,create_catalogo_variables,create_catalogo_pruebas,create_catalogo_sintomas,create_parametros_mision}.php
│   └── Seeders/{UmbralesSeeder,CatalogoVariablesSeeder,CatalogoPruebasSeeder,CatalogoSintomasSeeder,ParametrosMisionSeeder}.php
```

## 3. Relaciones

| Desde | Hacia | Tipo |
|---|---|---|
| `umbrales.variable_codigo` | `catalogo_variables.codigo` | Lógica (validada en el *Form Request*; reglas como `DOSIS_PROYECCION` usan un valor calculado que no está en el catálogo) |
| `umbrales.modificado_por` | `users.id` (M01) | Lógica |

"Una fila vigente por `(mision_id, regla_codigo, origen)`" la garantiza el caso de uso dentro de una transacción.

## 4. Consultas principales

| Consulta | Índice |
|---|---|
| Umbrales vigentes de una variable (M12, en caché) | `umbrales (mision_id, variable_codigo, evaluador, vigente, activo)` |
| Historial de una regla | `umbrales (mision_id, regla_codigo, version, origen)` única: las versiones de la Tierra y las de los ajustes se numeran aparte |
| Ajustes pendientes | `umbrales (mision_id, origen, estado_revision)` |
| Catálogos y parámetros | Claves primarias |

## 5. Volumen

Tablas pequeñas (decenas de filas). M12 guarda los umbrales en caché y la invalida con `UmbralModificado`.

## 6. Datos sembrados

Los valores sembrados (umbrales, catálogos de variables y pruebas, parámetros de misión) están en el [backend §3.2 a §3.5](../backend/README.md#32-umbrales-sembrados), junto con sus fuentes.
