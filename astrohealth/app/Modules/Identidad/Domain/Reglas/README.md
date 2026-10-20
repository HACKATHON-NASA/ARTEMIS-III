# Domain/Reglas · M01 Identidad y acceso

> `astrohealth/app/Modules/Identidad/Domain/Reglas/` · [Ficha de backend de M01](../../../../../../documentacion/modulos/M01-identidad/backend/README.md) · [Norma](../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#10-reglas-de-domain)

Cálculos y reglas **puras** del negocio. Reciben los datos (y los umbrales de M16) como parámetros y devuelven un resultado.

| Va aquí | No va aquí |
|---|---|
| Clases pequeñas, sin estado, con un método claro. | Consultas a la base o llamadas a otros módulos. |

## Archivos previstos (según la ficha)

| Archivo | Qué hace | Historia |
|---|---|---|
| `ReglasAdministrador.php` | No cambiarse el propio rol, quedar al menos un admin activo, rol de un usuario vinculado | HU-01.02 |

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
