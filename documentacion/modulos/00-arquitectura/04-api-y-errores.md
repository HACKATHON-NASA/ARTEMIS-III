# 4 · API, JSON y errores

> [← Guía](README.md) · Anterior: [Backend con Laravel](03-backend-laravel.md) · Siguiente: [Frontend con Vue e Inertia](05-frontend-vue.md)

Manual práctico de la norma de [ARCHITECTURE §11–13](ARCHITECTURE.md#12-estándar-json). Aplica a **todas** las rutas `/api/v1/...`, las que usan los sensores, el simulador, la cola offline de la PWA y el otro nodo. Las pantallas usan la puerta web de Inertia, sin sobre JSON ([arquitectura](02-arquitectura.md#dos-formas-de-hablar-con-el-backend)), pero comparten el `traza_id`.

Si la ficha de un módulo fija algo concreto para una ruta (un código HTTP, un texto, el contenido de `data`), manda la ficha.

## 1. Rutas

| Regla | Ejemplo |
|---|---|
| Prefijo con versión y módulo: `/api/v1/<modulo>/<recurso>` | `/api/v1/ingesta/lotes`, `/api/v1/psicologia/sesiones` |
| Recursos en español, minúscula y plural | `sintomas`, `resultados`, `paquetes` |
| Subrecurso con su padre | `/api/v1/sintomas/{id}/foto` |
| Sin verbos en la URL: el verbo es el método HTTP | `POST /api/v1/sintomas`, no `/api/v1/registrar-sintoma` |

| Método | Uso |
|---|---|
| `GET` | Consultar. Nunca cambia nada |
| `POST` | Crear. **Idempotente por el `id` UUID que genera el cliente**: si llega dos veces, la segunda no duplica |
| `PUT` | Reemplazar los campos editables de un recurso |
| `DELETE` | Anular o revocar |

## 2. Formato de los datos

| Tipo de dato | Regla | Ejemplo |
|---|---|---|
| Nombres de campos | `snake_case`, en español | `"inicio_en"`, `"tripulante_id"` |
| Identificadores | UUID en texto, generado en el cliente ([plan §9.2](../../scrum/plan-de-sprints.md#92-reglas-de-datos)) | `"0d6b2f4e-1c3a-4e8b-9f21-6a7c5d3e2b10"` |
| Fechas y horas | ISO 8601 en **UTC** con `Z` (con milisegundos cuando la ficha lo pide) | `"2026-10-22T14:05:07.123Z"` |
| Solo fecha | `AAAA-MM-DD` | `"2026-10-22"` |
| Números | Número JSON con punto decimal. La coma decimal solo aparece en la pantalla | `36.8`, no `"36,8"` |
| Valores enumerados | Exactamente los del [contrato §9.1](../../scrum/plan-de-sprints.md#91-valores-enumerados) | `"critico"`, `"reposo"` |
| Unidades | UCUM, como en el catálogo | `"/min"`, `"mm[Hg]"` |
| Campos sin valor | `null`, no se omiten | `"notas": null` |
| Booleanos | `true` / `false`, nunca `1` / `0` | `"reciente": true` |

## 3. El sobre de todas las respuestas

Toda respuesta de la API tiene la misma forma:

```json
{
  "success": true,
  "code": "SINTOMAS_REGISTRADO",
  "message": "Síntoma registrado.",
  "data": { "id": "0d6b2f4e-…", "tipo": "cefalea", "intensidad": 6, "inicio_en": "2026-10-22T14:05:00.000Z" },
  "meta": { "traza_id": "5b0e9c1a-7f42-4d2e-9a1b-3c6d8e2f4a70" }
}
```

**Lo que las fichas llaman "cuerpo de la respuesta" es el contenido de `data`.** Por ejemplo, el resumen de un lote de M03 (`{ lote_id, guardadas, … }`) viaja en `data`.

| Caso | HTTP | `code` | `data` |
|---|:---:|---|---|
| Se creó | `201` | `<MODULO>_<COSA>_<PARTICIPIO>` (`SINTOMAS_REGISTRADO`) | El recurso creado |
| Ya existía ese `id` (reenvío) | `200` | `<…>_YA_<PARTICIPIO>` (`SINTOMAS_YA_REGISTRADO`) | El recurso guardado, igual que la primera vez |
| Consulta de un recurso | `200` | `OK` | El recurso |
| Lista paginada | `200` | `OK` | Arreglo; la paginación va en `meta.paginacion` |
| Borrado o revocación | `204` | — | Sin cuerpo |

El controlador responde con `RespuestaApi` y el `Resource` del recurso; nunca arma el arreglo a mano:

```php
return RespuestaApi::creado(CodigoSintomas::Registrado, 'Síntoma registrado.', SintomaResource::make($sintoma));
```

## 4. Errores

```json
{
  "success": false,
  "code": "SINTOMAS_CON_OTRA_FOTO",
  "message": "Este síntoma ya tiene otra foto.",
  "data": null,
  "errors": {},
  "meta": { "traza_id": "5b0e9c1a-…", "modulo": "M11" }
}
```

| Campo | Qué dice |
|---|---|
| `message` | **Qué pasó**, para una persona. Es el texto exacto de la historia cuando lo define |
| `code` | **Qué pasó**, para una máquina. El simulador y la cola offline deciden con él, nunca con el texto |
| `errors` | Errores por campo en un `422` de validación (`{ "tipo": ["Ese síntoma no está en el catálogo."] }`); `{}` si el error no es de un campo. M03 pone aquí códigos, como dice [su ficha](../M03-ingesta/backend/HU/README.md) |
| `meta.modulo` | **Dónde se generó**: el módulo dueño de la excepción o de la ruta (`M01`…`M16`), o `nucleo` si fue antes de llegar a un módulo |
| `meta.traza_id` | Identificador de **esta** petición: es el mismo del encabezado `X-Traza-Id` y de las líneas del *log* |
| `meta.debug` | **Solo en local** (`APP_DEBUG=true`): `{ "excepcion", "archivo", "linea" }`, el lugar exacto donde se lanzó. Nunca en cPanel |

| HTTP | `code` general | Cuándo |
|:---:|---|---|
| `400` | `PETICION_INVALIDA` | JSON mal formado |
| `401` | `NO_AUTENTICADO` | Falta el token o no es válido. M01 usa además `IDENTIDAD_TOKEN_REQUERIDO` (escribir con sesión) e `IDENTIDAD_USUARIO_DESACTIVADO` |
| `403` | `SIN_PERMISO` | Autenticado, sin permiso |
| `404` | `NO_ENCONTRADO` | No existe o no es visible para quien pregunta |
| `409` | `CONFLICTO` | La acción choca con el estado actual, si la ficha no dice otra cosa |
| `413` | `DEMASIADO_GRANDE` | Cuerpo o archivo por encima del límite |
| `422` | `VALIDACION_FALLIDA` o `<MODULO>_<REGLA>` | Datos mal formados, o una regla de negocio lo impide |
| `429` | `DEMASIADAS_PETICIONES` | Límite de frecuencia |
| `500` | `ERROR_INTERNO` | Error no previsto: siempre "Ocurrió un error inesperado." |
| `503` | `MANTENIMIENTO` | Mantenimiento |

### Errores de negocio

El servicio **lanza** una excepción del módulo; nunca arma la respuesta:

```php
// app/Modules/Sintomas/Domain/Excepciones/SintomaConOtraFoto.php
final class SintomaConOtraFoto extends ErrorDeNegocio
{
    public function __construct()
    {
        parent::__construct(CodigoSintomas::ConOtraFoto, 'Este síntoma ya tiene otra foto.', status: 422);
    }
}
```

```php
// app/Modules/Sintomas/Domain/Enums/CodigoSintomas.php
enum CodigoSintomas: string
{
    case Registrado = 'SINTOMAS_REGISTRADO';
    case YaRegistrado = 'SINTOMAS_YA_REGISTRADO';
    case ConOtraFoto = 'SINTOMAS_CON_OTRA_FOTO';
}
```

El manejador común (`bootstrap/app.php` → `withExceptions`) convierte la excepción en el sobre de error. En las **pantallas** (rutas web de Inertia) la misma excepción llega como error de validación del campo que declara (`useForm` lo muestra bajo el campo) o, si no declara campo, como `flash.error`. El servicio no sabe ni le importa desde dónde lo llamaron. Cada módulo documenta sus códigos en una tabla "Códigos de respuesta" de su `backend/README.md`; un código publicado **no cambia**.

## 5. Cómo encontrar un error en el log

1. El middleware global `AsignarTrazaId` da un UUID a cada petición (o usa el del encabezado `X-Traza-Id`, si el cliente lo manda), lo añade a **todas** las líneas de *log* de esa petición (`Log::withContext`) y lo devuelve en `meta.traza_id` y en el encabezado.
2. Todo `500` se registra en el *log* diario (`storage/logs/laravel-AAAA-MM-DD.log`) con `traza_id`, módulo, ruta, actor y la traza de la excepción.
3. Las **pantallas** de error (`403`, `404`, `500`, `503`) muestran "Código de seguimiento: <traza_id>".
4. Para investigar: se copia el `traza_id` y se busca en el *log* del nodo (`grep 5b0e9c1a storage/logs/*.log`).

**Nunca** se escriben en el *log* contraseñas, tokens, contenido de archivos ni valores de salud.

## 6. Documentar una ruta de API

Cada ruta se documenta en el `backend/README.md` de su módulo (sección 1) y en su historia:

```markdown
### `POST /api/v1/<modulo>/<recurso>`
Autenticación: token `<tipo>` (`TokenDeTipo:<tipo>`). Idempotente por `id`.

| Campo | Regla | Mensaje de error |
|---|---|---|

| HTTP | `code` | `data` |
|---|---|---|
| `201` | `<MODULO>_…` | … |
| `422` | `<MODULO>_…` | `null` |
```

Además, en local, **Scramble** genera la documentación OpenAPI a partir de los `Request` y los `Resource` en `/docs/api` ([HU-00.01](../../scrum/fundaciones/HU-00.01-repositorio-y-estructura-de-modulos.md)).
