# Domain/Eventos · M02 Misión y tripulación

> `astrohealth/app/Modules/Mision/Domain/Eventos/` · [Ficha de backend de M02](../../../../../../documentacion/modulos/M02-mision/backend/README.md) · [Norma](../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#23-comunicación-entre-módulos-y-eventos)

Los **eventos de dominio** que **publica** este módulo. Son públicos: otros módulos los escuchan.

| Va aquí | No va aquí |
|---|---|
| Clases de evento con su *payload* (el de módulos §5) e `importado`; `ShouldDispatchAfterCommit`. | Eventos de otros módulos (se escuchan desde `Infrastructure/Listeners`). |

**Archivos previstos para M02 en esta carpeta:** los lista la ficha, en [§6 Archivos](../../../../../../documentacion/modulos/M02-mision/backend/README.md#6-archivos).

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
