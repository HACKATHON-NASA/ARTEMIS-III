# Http/Requests · M14 Reportes

> `astrohealth/app/Modules/Reportes/Http/Requests/` · [Ficha de backend de M14](../../../../../../documentacion/modulos/M14-reportes/backend/README.md) · [Norma](../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#11-reglas-httpapi)

**Validación de entrada** (formato, obligatoriedad, rangos de tipo) con `FormRequest`, y conversión al DTO del caso de uso.

| Va aquí | No va aquí |
|---|---|
| `<Accion o Recurso>Request` con `rules()`, `messages()` y un método que devuelve el DTO. | Reglas de negocio (van en `Application` o `Domain`). |

**Archivos previstos para M14 en esta carpeta:** los lista la ficha, en [§6 Archivos](../../../../../../documentacion/modulos/M14-reportes/backend/README.md#6-archivos).

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
