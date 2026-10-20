# Infrastructure/Repositorios · M12 Analítica y línea base

> `astrohealth/app/Modules/Analitica/Infrastructure/Repositorios/` · [Ficha de backend de M12](../../../../../../documentacion/modulos/M12-analitica/backend/README.md) · [Norma](../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#8-reglas-de-repository)

Las consultas y escrituras a MySQL, separadas en **lectura** (`<Agregado>LecturaRepositorio`, que implementa los `Contratos/`) y **escritura** (`<Agregado>EscrituraRepositorio`).

| Va aquí | No va aquí |
|---|---|
| Métodos con intención (`porId`, `guardar`, `marcarConfirmada`). Solo tablas de este módulo. | Un `query()` genérico hacia afuera; tablas de otros módulos. |

**Archivos previstos para M12 en esta carpeta:** los lista la ficha, en [§6 Archivos](../../../../../../documentacion/modulos/M12-analitica/backend/README.md#6-archivos).

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
