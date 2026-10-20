# Domain (núcleo del negocio) · M01 Identidad y acceso

> `astrohealth/app/Modules/Identidad/Domain/` · [Ficha de backend de M01](../../../../../documentacion/modulos/M01-identidad/backend/README.md) · [Norma](../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#10-reglas-de-domain)

Las **reglas del negocio puras**: lo que el módulo *es*, sin base de datos ni HTTP. Se prueba con pruebas unitarias sin arrancar Laravel.

| Va aquí | No va aquí |
|---|---|
| Las subcarpetas `Enums`, `Reglas`, `Eventos`, `ValueObjects` y `Excepciones`. | SQL, `DB::`, Eloquent, `Request`, respuestas, Inertia, valores clínicos fijos (vienen de M16). |

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
