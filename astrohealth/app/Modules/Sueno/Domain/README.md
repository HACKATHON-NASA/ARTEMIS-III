# Domain (núcleo del negocio) · M06 Sueño

> `astrohealth/app/Modules/Sueno/Domain/` · [Ficha de backend de M06](../../../../../documentacion/modulos/M06-sueno/backend/README.md) · [Norma](../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#10-reglas-de-domain)

Las **reglas del negocio puras**: lo que el módulo *es*, sin base de datos ni HTTP. Se prueba con pruebas unitarias sin arrancar Laravel.

| Va aquí | No va aquí |
|---|---|
| Las subcarpetas `Enums`, `Reglas`, `Eventos`, `ValueObjects` y `Excepciones`. | SQL, `DB::`, Eloquent, `Request`, respuestas, Inertia, valores clínicos fijos (vienen de M16). |

**Archivos previstos para M06 en esta carpeta:** los lista la ficha, en [§6 Archivos](../../../../../documentacion/modulos/M06-sueno/backend/README.md#6-archivos).

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
