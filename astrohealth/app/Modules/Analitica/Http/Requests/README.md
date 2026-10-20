# Http/Requests · M12 Analítica y línea base

> `astrohealth/app/Modules/Analitica/Http/Requests/` · [Ficha de backend de M12](../../../../../../documentacion/modulos/M12-analitica/backend/README.md) · [Norma](../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#11-reglas-httpapi)

**Validación de entrada** (formato, obligatoriedad, rangos de tipo) con `FormRequest`, y conversión al DTO del caso de uso.

| Va aquí | No va aquí |
|---|---|
| `<Accion o Recurso>Request` con `rules()`, `messages()` y un método que devuelve el DTO. | Reglas de negocio (van en `Application` o `Domain`). |

**Archivos previstos para M12 en esta carpeta:** los lista la ficha, en [§6 Archivos](../../../../../../documentacion/modulos/M12-analitica/backend/README.md#6-archivos).

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
