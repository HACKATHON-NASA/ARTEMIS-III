# M02 · Misión y tripulación · Base de datos

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · [Frontend](../frontend/README.md) · **Base de datos**

Tablas **MySQL** que este módulo posee: es el único que las migra y las escribe ([reglas de comunicación](../../README.md#4-reglas-de-comunicación-entre-módulos)). Siguen las [convenciones de base de datos](../../README.md#7-convenciones-de-base-de-datos).

---

## 1. Tablas

### `misiones`

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | Igual en los dos nodos ([backend §3.5](../backend/README.md#35-dos-nodos)) |
| `nombre` | `varchar(100)` | Ej.: "Misión de demostración AstroHealth" |
| `perfil_enlace` | `varchar(10)` | `leo` (ISS y Artemis III), `luna`, `marte` |
| `fecha_inicio` | `date` | Día de misión 1 |
| `fecha_fin` | `date` nulo | Necesaria para la proyección de dosis (M09) |
| `activa` | `boolean` | Solo una misión con `true` |

### `tripulantes`

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | UUID v5 del código; igual en los dos nodos |
| `mision_id` | `uuid` | |
| `user_id` | `bigint` nulo | Usuario con rol `astronauta` o `cmo` |
| `codigo` | `varchar(5)` | `T1`, `T2`…; único por misión |
| `nombre` | `varchar(100)` | Ficticio en todos los entornos |
| `sexo` | `char(1)` | `F` o `M` (para los rangos de referencia de M07) |
| `fecha_nacimiento` | `date` | |
| `rol_a_bordo` | `varchar(50)` | Ej.: comandante, especialista |

### `dispositivos`

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | UUID v5 del identificador; igual en los dos nodos |
| `identificador` | `varchar(60)` | Único; ej.: `astroskin-T2`, `ambiente-lab` ([convención](HU/HU-02.02-registrar-tripulantes-y-dispositivos.md)). No se cambia después de crearlo |
| `tipo` | `varchar(30)` | `wearable`, `oximetro`, `tensiometro`, `actigrafo`, `dosimetro`, `monitor_ambiental`, `cgm` (monitor continuo de glucosa), `medidor_masa` (masa corporal, M10), `tableta` (solo inventario), `simulador` |
| `tripulante_id` | `uuid` nulo | Para dispositivos personales |
| `modulo_nave_id` | `uuid` nulo | Para monitores ambientales |
| `bateria_pct` | `tinyint` nulo | La actualiza M03 si el lote trae `bateria_pct` |
| `ultimo_contacto_en` | `datetime(3)` nulo | Lo actualiza M03 con cada lote |

Restricción: exactamente uno de `tripulante_id` o `modulo_nave_id` es no nulo, salvo `tipo = simulador` (ninguno: escribe para cualquier tripulante y ubicación de la misión) y `tipo = tableta` (uno o ninguno: puede ser compartida).

### `modulos_nave`

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | UUID v5 del código; igual en los dos nodos |
| `mision_id` | `uuid` | |
| `codigo` | `varchar(30)` | Ej.: `lab`, `habitacional`, `nodo1` |
| `nombre` | `varchar(60)` | |

## 2. Migraciones y seeders

```
├── Database/
│   ├── Migrations/{create_misiones,create_tripulantes,create_modulos_nave,create_dispositivos}.php
│   └── Seeders/MisionDemoSeeder.php    # Ver §6
```

Las cuatro tablas se migran en el **Sprint 1** (HU-02.01 y HU-02.02). `modulos_nave` hace falta ese mismo sprint, porque las series de ambiente del simulador (HU-17.04) llegan con `ubicacion`.

## 3. Relaciones

| Desde | Hacia | Tipo |
|---|---|---|
| `tripulantes.mision_id` | `misiones.id` | `FOREIGN KEY` |
| `modulos_nave.mision_id` | `misiones.id` | `FOREIGN KEY` |
| `dispositivos.tripulante_id` | `tripulantes.id` | `FOREIGN KEY` (nula) |
| `dispositivos.modulo_nave_id` | `modulos_nave.id` | `FOREIGN KEY` (nula) |
| `tripulantes.user_id` | `users.id` (M01) | Lógica, única por misión |

Restricciones adicionales:

- `CHECK (tipo IN ('simulador', 'tableta') OR ((tripulante_id IS NULL) <> (modulo_nave_id IS NULL)))` en `dispositivos`, solo en MySQL (8.0.16+ o MariaDB 10.2+; en versiones anteriores no se aplica). La tabla completa por tipo la valida el *Form Request*.
- Únicas: `tripulantes (mision_id, codigo)`, `modulos_nave (mision_id, codigo)`, `dispositivos.identificador`, `tripulantes (mision_id, user_id)` (MySQL permite varios `NULL` en un índice único; el mismo usuario puede ser tripulante de otra misión).
- "Una sola misión activa" se garantiza en el caso de uso (transacción que desactiva las demás), porque MySQL no tiene índices únicos parciales.

## 4. Consultas principales

| Consulta | Índice |
|---|---|
| Validar el dispositivo de un lote (M03) | `dispositivos.identificador` único |
| Tripulante por código (lote de M03) | `tripulantes (mision_id, codigo)` único |
| Misión activa | `misiones.activa` |
| Tripulante por usuario (misión activa) | `tripulantes (mision_id, user_id)` único |

## 5. Volumen

Tablas pequeñas: 1 misión, 4–6 tripulantes, 3 módulos y unos 30 dispositivos.

## 6. Seeders

`MisionDemoSeeder` se ejecuta **igual en los dos nodos**:

| Dato | Valor |
|---|---|
| Misión | "Misión de demostración AstroHealth", perfil `leo`, activa |
| Fechas | `fecha_inicio` = `MISION_INICIO` del `.env`; si falta, **hoy (UTC) − 29 días** (solo en `local` y `testing`; fuera de ahí, `MISION_INICIO` es obligatoria y la misma en los dos nodos), para que el relleno histórico de 30 días del simulador termine hoy y no genere lecturas futuras. `fecha_fin` = `fecha_inicio` + 59 días. Si la misión ya existe, el *seeder* no cambia sus fechas |
| Tripulantes | T1 M (comandante, vinculado a `cmo@astrohealth.test`), T2 F, T3 M y T4 F (vinculados a `astronauta@…`, `astronauta3@…` y `astronauta4@…`, que crea `UsuariosDePruebaSeeder` de M01) |
| Módulos de la nave | `lab`, `habitacional` y `nodo1` |
| Dispositivos | Por tripulante: `wearable`, `oximetro`, `tensiometro`, `actigrafo`, `dosimetro`, `cgm` y `medidor_masa`; un `monitor_ambiental` por módulo; un dispositivo `simulador` (32 en total, con los identificadores de la [convención](HU/HU-02.02-registrar-tripulantes-y-dispositivos.md)) |
| Línea base previa al vuelo | La de demostración de T2 (FC en reposo 61 ± 3 lpm), cargada en los dos nodos mientras no exista la sincronización |
| Claves | UUID v5 a partir de cada código, con un espacio de nombres fijo del proyecto (valor y nombres exactos en las [historias](HU/README.md)) |

El **token** del dispositivo `simulador` no lo crea este *seeder*: los tokens son de M01 y se emiten con HU-01.03 (`Admin/Tokens`), después de crear el dispositivo. Para desarrollo local, M01 puede emitirlo con su *seeder* y escribirlo en la consola.
