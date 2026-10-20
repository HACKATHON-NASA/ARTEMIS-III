# Domain/Reglas · M14 Reportes

> `astrohealth/app/Modules/Reportes/Domain/Reglas/` · [Ficha de backend de M14](../../../../../../documentacion/modulos/M14-reportes/backend/README.md) · [Norma](../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#10-reglas-de-domain)

Cálculos y reglas **puras** del negocio. Reciben los datos (y los umbrales de M16) como parámetros y devuelven un resultado.

| Va aquí | No va aquí |
|---|---|
| Clases pequeñas, sin estado, con un método claro. | Consultas a la base o llamadas a otros módulos. |

**Archivos previstos para M14 en esta carpeta:** los lista la ficha, en [§6 Archivos](../../../../../../documentacion/modulos/M14-reportes/backend/README.md#6-archivos).

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
