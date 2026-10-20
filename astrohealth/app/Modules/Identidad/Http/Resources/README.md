# Http/Resources · M01 Identidad y acceso

> `astrohealth/app/Modules/Identidad/Http/Resources/` · [Ficha de backend de M01](../../../../../../documentacion/modulos/M01-identidad/backend/README.md) · [Norma](../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#12-estándar-json)

La **forma exacta** de `data` en la API y de las *props* de las páginas.

| Va aquí | No va aquí |
|---|---|
| `<Recurso>Resource` con `toArray()`; fechas en ISO 8601 UTC. | Consultas (los datos llegan ya cargados). |

## Archivos previstos (según la ficha)

| Archivo | Qué hace | Historia |
|---|---|---|
| `UsuarioResource.php` · `TokenResource.php` · `AuditoriaResource.php` | *Props* de las tres pantallas de F4 | HU-01.02 a HU-01.04 |

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
