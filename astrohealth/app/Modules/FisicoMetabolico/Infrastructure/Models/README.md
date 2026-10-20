# Infrastructure/Models · M10 Físico y metabólico

> `astrohealth/app/Modules/FisicoMetabolico/Infrastructure/Models/` · [Ficha de backend de M10](../../../../../../documentacion/modulos/M10-fisico-metabolico/backend/README.md) · [Norma](../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#8-reglas-de-repository)

Los **modelos Eloquent** de las tablas **propias** del módulo.

| Va aquí | No va aquí |
|---|---|
| `$fillable` explícito, `casts()` con los *enums*, `$dateFormat` con milisegundos si hay `datetime(3)`, `HasUuids` si el `id` es UUID. | Modelos de tablas de otros módulos; lógica de negocio. |

**Archivos previstos para M10 en esta carpeta:** los lista la ficha, en [§6 Archivos](../../../../../../documentacion/modulos/M10-fisico-metabolico/backend/README.md#6-archivos).

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
