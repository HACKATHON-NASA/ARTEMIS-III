# Domain/Excepciones · M07 Exámenes médicos

> `astrohealth/app/Modules/Examenes/Domain/Excepciones/` · [Ficha de backend de M07](../../../../../../documentacion/modulos/M07-examenes/backend/README.md) · [Norma](../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#13-manejo-de-errores-y-códigos-funcionales)

Los **errores de negocio** del módulo. Heredan de `App\Shared\Excepciones\ErrorDeNegocio` y llevan su código funcional y su HTTP; el manejador común los convierte en el sobre JSON de error.

| Va aquí | No va aquí |
|---|---|
| Una clase por error, nombrada con lo que pasó (sin sufijo `Exception`). | Errores técnicos ni respuestas armadas a mano. |

**Archivos previstos para M07 en esta carpeta:** los lista la ficha, en [§6 Archivos](../../../../../../documentacion/modulos/M07-examenes/backend/README.md#6-archivos).

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
