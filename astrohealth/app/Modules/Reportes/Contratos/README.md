# Contratos (API pública del módulo) · M14 Reportes

> `astrohealth/app/Modules/Reportes/Contratos/` · [Ficha de backend de M14](../../../../../documentacion/modulos/M14-reportes/backend/README.md) · [Norma](../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#23-comunicación-entre-módulos-y-eventos)

Las **interfaces** (y los DTOs que devuelven) que este módulo **ofrece a los demás módulos**. Es lo único del módulo que otro módulo puede usar.

| Va aquí | No va aquí |
|---|---|
| Interfaces con nombres de lo que ofrecen (`ConsultaX`, `RegistroX`) y los DTOs de sus respuestas. | Implementaciones (van en `Infrastructure/Repositorios`), modelos Eloquent, lógica. |

**Archivos previstos para M14 en esta carpeta:** los lista la ficha, en [§6 Archivos](../../../../../documentacion/modulos/M14-reportes/backend/README.md#6-archivos).

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
