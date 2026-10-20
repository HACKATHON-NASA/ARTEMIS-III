# Http (= API) · M01 Identidad y acceso

> `astrohealth/app/Modules/Identidad/Http/` · [Ficha de backend de M01](../../../../../documentacion/modulos/M01-identidad/backend/README.md) · [Norma](../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#11-reglas-httpapi)

La **entrada y salida HTTP** del módulo: controladores delgados, validación de entrada y forma de las respuestas. Las rutas están en `routes.php` del módulo.

| Va aquí | No va aquí |
|---|---|
| Las subcarpetas `Controllers/Web`, `Controllers/Api`, `Requests`, `Resources` y `Middleware`. | Reglas de negocio, `DB::`, repositorios o modelos. |

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
