# Http/Requests · M01 Identidad y acceso

> `astrohealth/app/Modules/Identidad/Http/Requests/` · [Ficha de backend de M01](../../../../../../documentacion/modulos/M01-identidad/backend/README.md) · [Norma](../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#11-reglas-httpapi)

**Validación de entrada** (formato, obligatoriedad, rangos de tipo) con `FormRequest`, y conversión al DTO del caso de uso.

| Va aquí | No va aquí |
|---|---|
| `<Accion o Recurso>Request` con `rules()`, `messages()` y un método que devuelve el DTO. | Reglas de negocio (van en `Application` o `Domain`). |

## Archivos previstos (según la ficha)

| Archivo | Qué hace | Historia |
|---|---|---|
| `LoginRequest.php` | Extiende el de Fortify y fuerza `remember = false` | HU-01.01 |
| `GuardarUsuarioRequest.php` | Alta y edición de usuarios | HU-01.02 |
| `EmitirTokenRequest.php` | Emisión de tokens de dispositivo | HU-01.03 |

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
