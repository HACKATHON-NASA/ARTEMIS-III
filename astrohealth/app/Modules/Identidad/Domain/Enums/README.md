# Domain/Enums · M01 Identidad y acceso

> `astrohealth/app/Modules/Identidad/Domain/Enums/` · [Ficha de backend de M01](../../../../../../documentacion/modulos/M01-identidad/backend/README.md) · [Norma](../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#13-manejo-de-errores-y-códigos-funcionales)

*Backed enums* de PHP (tipo `string`) con los valores cerrados del módulo y el *enum* de sus **códigos funcionales** (`Codigo<Modulo>`).

| Va aquí | No va aquí |
|---|---|
| Un archivo por *enum*. Valores exactamente como en el contrato §9.1 del plan. | Listas que cambian por misión (van en la configuración de M16). |

## Archivos previstos (según la ficha)

| Archivo | Qué hace | Historia |
|---|---|---|
| `Rol.php` | Los 6 roles + `sistema`; incluye `puedeIniciarSesionEn(nodo)` | HU-01.01 |
| `TipoToken.php` | `dispositivo`, `pwa`, `nodo` | HU-01.03 |
| `AccionAuditoria.php` | `login`, `login_fallido`, `logout`, `ver`, `crear`, `anular`, `modificar`, `exportar` | HU-01.04 |
| `CodigoIdentidad.php` | Códigos funcionales `IDENTIDAD_…` | HU-01.01 |

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
