# Infrastructure/Models · M01 Identidad y acceso

> `astrohealth/app/Modules/Identidad/Infrastructure/Models/` · [Ficha de backend de M01](../../../../../../documentacion/modulos/M01-identidad/backend/README.md) · [Norma](../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#8-reglas-de-repository)

Los **modelos Eloquent** de las tablas **propias** del módulo.

| Va aquí | No va aquí |
|---|---|
| `$fillable` explícito, `casts()` con los *enums*, `$dateFormat` con milisegundos si hay `datetime(3)`, `HasUuids` si el `id` es UUID. | Modelos de tablas de otros módulos; lógica de negocio. |

## Archivos previstos (según la ficha)

| Archivo | Qué hace | Historia |
|---|---|---|
| `Auditoria.php` | Solo inserción: bloquea `updating` y `deleting` | HU-01.04 |
| `AuditoriaEnvio.php` | Horas de auditoría ya encoladas hacia la Tierra | HU-15.01 |

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
