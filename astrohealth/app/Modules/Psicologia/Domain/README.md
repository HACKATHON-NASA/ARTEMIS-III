# Domain (núcleo del negocio) · M05 Psicología y cognición

> `astrohealth/app/Modules/Psicologia/Domain/` · [Ficha de backend de M05](../../../../../documentacion/modulos/M05-psicologia/backend/README.md) · [Norma](../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#10-reglas-de-domain)

Las **reglas del negocio puras**: lo que el módulo *es*, sin base de datos ni HTTP. Se prueba con pruebas unitarias sin arrancar Laravel.

| Va aquí | No va aquí |
|---|---|
| Las subcarpetas `Enums`, `Reglas`, `Eventos`, `ValueObjects` y `Excepciones`. | SQL, `DB::`, Eloquent, `Request`, respuestas, Inertia, valores clínicos fijos (vienen de M16). |

**Archivos previstos para M05 en esta carpeta:** los lista la ficha, en [§6 Archivos](../../../../../documentacion/modulos/M05-psicologia/backend/README.md#6-archivos).

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
