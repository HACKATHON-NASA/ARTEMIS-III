# Policies · M01 Identidad y acceso

> `astrohealth/app/Modules/Identidad/Policies/` · [Ficha de backend de M01](../../../../../documentacion/modulos/M01-identidad/backend/README.md) · [Norma](../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#14-seguridad)

La **autorización sobre recursos concretos** (por ejemplo, "solo sus propios dispositivos"). Los nombres de los *Gates* los define solo M01.

| Va aquí | No va aquí |
|---|---|
| Clases `<Recurso>Policy`. | *Gates* nuevos fuera de M01. |

## Archivos previstos (según la ficha)

| Archivo | Qué hace | Historia |
|---|---|---|
| `GatesIdentidad.php` | Los 23 *Gates* del sistema y `queCumple()` | HU-01.02 |
| `TokenPolicy.php` | Solo el admin revoca un token de nodo | HU-01.03 |

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
