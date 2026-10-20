# app/Shared/Http

| Archivo | Qué hace |
|---|---|
| `RespuestaApi.php` | Construye el sobre JSON de la API (`success`, `code`, `message`, `data`, `meta`) con `ok()`, `creado()` y `paginado()` ([ARCHITECTURE §12](../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#12-estándar-json)) |
| `CodigoGeneral.php` | *Enum* con los códigos funcionales comunes: `OK`, `VALIDACION_FALLIDA`, `NO_AUTENTICADO`, `SIN_PERMISO`… |
| [`Middleware/`](Middleware/README.md) | Middleware global |

> 📝 **Documentar siempre:** si cambia lo que hace esta carpeta o añade un tipo de archivo nuevo, actualice este README y el CHANGELOG en el mismo *pull request*.
