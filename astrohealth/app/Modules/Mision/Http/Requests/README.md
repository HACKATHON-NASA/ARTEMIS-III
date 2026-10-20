# Http/Requests · M02 Misión y tripulación

> `astrohealth/app/Modules/Mision/Http/Requests/` · [Ficha de backend de M02](../../../../../../documentacion/modulos/M02-mision/backend/README.md) · [Norma](../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#11-reglas-httpapi)

**Validación de entrada** (formato, obligatoriedad, rangos de tipo) con `FormRequest`, y conversión al DTO del caso de uso.

| Va aquí | No va aquí |
|---|---|
| `<Accion o Recurso>Request` con `rules()`, `messages()` y un método que devuelve el DTO. | Reglas de negocio (van en `Application` o `Domain`). |

**Archivos previstos para M02 en esta carpeta:** los lista la ficha, en [§6 Archivos](../../../../../../documentacion/modulos/M02-mision/backend/README.md#6-archivos).

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
