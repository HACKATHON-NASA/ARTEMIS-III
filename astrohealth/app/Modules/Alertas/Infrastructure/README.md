# Infrastructure (= Repository) · M13 Alertas y casos clínicos

> `astrohealth/app/Modules/Alertas/Infrastructure/` · [Ficha de backend de M13](../../../../../documentacion/modulos/M13-alertas/backend/README.md) · [Norma](../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#8-reglas-de-repository)

La **única** parte del módulo que toca la base de datos, y los adaptadores hacia afuera (*listeners*, importador de sincronización).

| Va aquí | No va aquí |
|---|---|
| Las subcarpetas `Repositorios`, `Models`, `Listeners` y `Sincronizacion`. | Reglas de negocio ni respuestas HTTP. |

**Archivos previstos para M13 en esta carpeta:** los lista la ficha, en [§6 Archivos](../../../../../documentacion/modulos/M13-alertas/backend/README.md#6-archivos).

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
