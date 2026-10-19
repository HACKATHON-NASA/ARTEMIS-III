# M02 · Misión y tripulación · Historias de base de datos

> [← Ficha del módulo](../../README.md) · [Ficha de base de datos](../README.md) · [Historias de backend](../../backend/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **MySQL** (migraciones, *seeders* y restricciones) de cada historia de M02. Lo hace la responsable de backend (**Julieth**). Los puntos de cada historia son los del [plan de sprints](../../../../scrum/plan-de-sprints.md) y cubren las tres capas.

## Reglas comunes

- Se siguen las [convenciones de base de datos](../../../README.md#7-convenciones-de-base-de-datos). Las migraciones de M02 viven en `app/Modules/Mision/Database/Migrations/` y **una migración fusionada no se edita**.
- Las tablas de M02 son de **configuración** (no de mediciones): se pueden actualizar y llevan `created_at` y `updated_at` con milisegundos (`$table->datetimes(3);`). Cada modelo declara `protected $dateFormat = 'Y-m-d H:i:s.v';`.
- **Nunca se borra** una misión, un tripulante, un módulo ni un dispositivo: las mediciones de otros módulos los referencian. Las claves foráneas usan `->restrictOnDelete()`.
- Las pruebas corren con la base de `phpunit.xml` (SQLite en memoria en el *starter kit*). Todo SQL escrito a mano tiene variante para `mysql` y para `sqlite`, o se omite en `sqlite` si es solo una protección extra.
- **Claves iguales en los dos nodos:** los registros del *seeder* usan UUID v5. Los que se crean después en la nave reciben un UUID del servidor (`HasUuids`) y llegan a la Tierra con ese mismo `id` (parte B de HU-02.01 y HU-02.02).

**Espacio de nombres de los UUID v5** (constante `IdEstable::ESPACIO` de M02; **no se cambia nunca**): `cd4c6b77-240f-448d-9f61-6abeb34ce7cb`

| Registro | Nombre que se pasa a UUID v5 | Ejemplo |
|---|---|---|
| Misión de demostración | `mision:demo` | — |
| Tripulante | `tripulante:demo:<codigo>` | `tripulante:demo:T2` |
| Módulo de la nave | `modulo:demo:<codigo>` | `modulo:demo:lab` |
| Dispositivo | `dispositivo:<identificador>` | `dispositivo:astroskin-T2` |

Se calcula con `Ramsey\Uuid\Uuid::uuid5(IdEstable::ESPACIO, $nombre)->toString()` (Laravel ya incluye `ramsey/uuid`). El código del tripulante y del módulo van **con el prefijo de la misión** para que dos misiones con un `T2` no compartan `id`.


## Historias

| Historia | Archivo |
|---|---|
| HU-02.01 · Crear una misión con su perfil de enlace | [HU-02.01-crear-una-mision-con-su-perfil-de-enlace.md](HU-02.01-crear-una-mision-con-su-perfil-de-enlace.md) |
| HU-02.02 · Registrar tripulantes y dispositivos | [HU-02.02-registrar-tripulantes-y-dispositivos.md](HU-02.02-registrar-tripulantes-y-dispositivos.md) |
| HU-02.03 · Registrar la línea base previa al vuelo | [HU-02.03-registrar-la-linea-base-previa-al-vuelo.md](HU-02.03-registrar-la-linea-base-previa-al-vuelo.md) |
