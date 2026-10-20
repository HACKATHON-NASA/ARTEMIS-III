# Console · M13 Alertas y casos clínicos

> `astrohealth/app/Modules/Alertas/Console/` · [Ficha de backend de M13](../../../../../documentacion/modulos/M13-alertas/backend/README.md) · [Norma](../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#17-convenciones-de-nombres)

Los **comandos de Artisan** del módulo (`<modulo>:<accion>`). Los programados se registran en el *ServiceProvider*. Opcional.

| Va aquí | No va aquí |
|---|---|
| Clases `<Accion>Command` que llaman a un caso de uso. | Lógica dentro del comando. |

**Archivos previstos para M13 en esta carpeta:** los lista la ficha, en [§6 Archivos](../../../../../documentacion/modulos/M13-alertas/backend/README.md#6-archivos).

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
