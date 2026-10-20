# Domain/Excepciones · M01 Identidad y acceso

> `astrohealth/app/Modules/Identidad/Domain/Excepciones/` · [Ficha de backend de M01](../../../../../../documentacion/modulos/M01-identidad/backend/README.md) · [Norma](../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#13-manejo-de-errores-y-códigos-funcionales)

Los **errores de negocio** del módulo. Heredan de `App\Shared\Excepciones\ErrorDeNegocio` y llevan su código funcional y su HTTP; el manejador común los convierte en el sobre JSON de error.

| Va aquí | No va aquí |
|---|---|
| Una clase por error, nombrada con lo que pasó (sin sufijo `Exception`). | Errores técnicos ni respuestas armadas a mano. |

## Archivos previstos (según la ficha)

| Archivo | Qué hace | Historia |
|---|---|---|
| `UsuarioDesactivado.php` · `RolFueraDeNodo.php` | Inicio de sesión rechazado (`IDENTIDAD_USUARIO_DESACTIVADO`, `IDENTIDAD_ROL_FUERA_DE_NODO`) | HU-01.01 |
| `NoPuedeCambiarSuPropioRol.php` · `UltimoAdministrador.php` · `UsuarioVinculadoATripulante.php` | Gestión de usuarios (`IDENTIDAD_PROPIO_ROL`, `IDENTIDAD_ULTIMO_ADMIN`, `IDENTIDAD_USUARIO_VINCULADO`) | HU-01.02 |
| `TabletaSinToken.php` · `DispositivoConToken.php` | Emisión de tokens (`IDENTIDAD_TABLETA_SIN_TOKEN`, `IDENTIDAD_DISPOSITIVO_CON_TOKEN`) | HU-01.03 |

La lista completa de códigos está en la [ficha §8](../../../../../../documentacion/modulos/M01-identidad/backend/README.md#8-códigos-de-respuesta).

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
