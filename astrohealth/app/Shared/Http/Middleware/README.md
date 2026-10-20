# app/Shared/Http/Middleware

| Archivo | Qué hace |
|---|---|
| `AsignarTrazaId.php` | Da un UUID a cada petición, lo pone en el contexto del *log* y lo devuelve en `X-Traza-Id` y `meta.traza_id` ([API y errores](../../../../../documentacion/modulos/00-arquitectura/04-api-y-errores.md#5-cómo-encontrar-un-error-en-el-log)) |

El middleware de **un** módulo va en su propia carpeta `Http/Middleware`.

> 📝 **Documentar siempre:** si cambia lo que hace esta carpeta o añade un tipo de archivo nuevo, actualice este README y el CHANGELOG en el mismo *pull request*.
