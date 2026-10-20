# Infrastructure/Sincronizacion · M04 Signos vitales

> `astrohealth/app/Modules/SignosVitales/Infrastructure/Sincronizacion/` · [Ficha de backend de M04](../../../../../../documentacion/modulos/M04-signos-vitales/backend/README.md) · [Norma](../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#23-comunicación-entre-módulos-y-eventos)

El **importador** de M15 (`Sincronizacion\Contratos\Importador`) si los datos de este módulo viajan entre la nave y la Tierra.

| Va aquí | No va aquí |
|---|---|
| La clase `Importador<Cosa>` con `tipo()`, `importar()` y `exportar()`. | Llamadas directas al otro nodo (solo M15 habla con él). |

**Archivos previstos para M04 en esta carpeta:** los lista la ficha, en [§6 Archivos](../../../../../../documentacion/modulos/M04-signos-vitales/backend/README.md#6-archivos).

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
