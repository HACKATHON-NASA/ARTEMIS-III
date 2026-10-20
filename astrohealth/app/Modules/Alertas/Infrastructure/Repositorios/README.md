# Infrastructure/Repositorios · M13 Alertas y casos clínicos

> `astrohealth/app/Modules/Alertas/Infrastructure/Repositorios/` · [Ficha de backend de M13](../../../../../../documentacion/modulos/M13-alertas/backend/README.md) · [Norma](../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#8-reglas-de-repository)

Las consultas y escrituras a MySQL, separadas en **lectura** (`<Agregado>LecturaRepositorio`, que implementa los `Contratos/`) y **escritura** (`<Agregado>EscrituraRepositorio`).

| Va aquí | No va aquí |
|---|---|
| Métodos con intención (`porId`, `guardar`, `marcarConfirmada`). Solo tablas de este módulo. | Un `query()` genérico hacia afuera; tablas de otros módulos. |

**Archivos previstos para M13 en esta carpeta:** los lista la ficha, en [§6 Archivos](../../../../../../documentacion/modulos/M13-alertas/backend/README.md#6-archivos).

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
