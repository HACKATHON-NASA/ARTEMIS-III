# Domain (núcleo del negocio) · M09 Radiación

> `astrohealth/app/Modules/Radiacion/Domain/` · [Ficha de backend de M09](../../../../../documentacion/modulos/M09-radiacion/backend/README.md) · [Norma](../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#10-reglas-de-domain)

Las **reglas del negocio puras**: lo que el módulo *es*, sin base de datos ni HTTP. Se prueba con pruebas unitarias sin arrancar Laravel.

| Va aquí | No va aquí |
|---|---|
| Las subcarpetas `Enums`, `Reglas`, `Eventos`, `ValueObjects` y `Excepciones`. | SQL, `DB::`, Eloquent, `Request`, respuestas, Inertia, valores clínicos fijos (vienen de M16). |

**Archivos previstos para M09 en esta carpeta:** los lista la ficha, en [§6 Archivos](../../../../../documentacion/modulos/M09-radiacion/backend/README.md#6-archivos).

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
