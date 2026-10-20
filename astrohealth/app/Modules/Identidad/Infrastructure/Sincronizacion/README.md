# Infrastructure/Sincronizacion · M01 Identidad y acceso

> `astrohealth/app/Modules/Identidad/Infrastructure/Sincronizacion/` · [Ficha de backend de M01](../../../../../../documentacion/modulos/M01-identidad/backend/README.md) · [Norma](../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#23-comunicación-entre-módulos-y-eventos)

El **importador** de M15 (`Sincronizacion\Contratos\Importador`) si los datos de este módulo viajan entre la nave y la Tierra.

| Va aquí | No va aquí |
|---|---|
| La clase `Importador<Cosa>` con `tipo()`, `importar()` y `exportar()`. | Llamadas directas al otro nodo (solo M15 habla con él). |

## Archivos previstos (según la ficha)

| Archivo | Qué hace | Historia |
|---|---|---|
| `ImportadorAuditoria.php` | Recibe en la Tierra la auditoría de la nave | HU-15.01 |

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
