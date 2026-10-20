# app/ · Backend

| Carpeta | De quién | Qué hacer |
|---|---|---|
| [`Modules/`](Modules/README.md) | **AstroHealth** | **Aquí se programa todo lo del proyecto**: un módulo por carpeta |
| [`Shared/`](Shared/README.md) | **AstroHealth** | Piezas técnicas comunes a todos los módulos (sin reglas de negocio) |
| `Actions/Fortify/`, `Concerns/` | *Starter kit* (inicio de sesión con Fortify) | No se añade nada. En HU-01.01 se adapta el registro y el inicio de sesión |
| `Http/Controllers/Settings/`, `Http/Requests/Settings/` | *Starter kit* (ajustes de perfil) | Se conserva solo el cambio de contraseña (HU-01.01) |
| `Http/Middleware/` | *Starter kit* (`HandleInertiaRequests`) | Ahí se añaden las *props* compartidas (HU-01.01) |
| `Models/User.php` | *Starter kit* | Lo administra M01 (`rol`, `activo`) |
| `Providers/` | Laravel | `AppServiceProvider`: configuración global ([HU-00.01](../../documentacion/scrum/fundaciones/HU-00.01-repositorio-y-estructura-de-modulos.md)) |
| `Console/` | Laravel | Comandos generales; los de cada módulo van en su `Console/` |

**Regla:** código nuevo **nunca** en las carpetas del kit; siempre en `Modules/<Modulo>/` o en `Shared/`.

> 📝 **Documentar siempre:** si cambia lo que hace esta carpeta o añade un tipo de archivo nuevo, actualice este README y el CHANGELOG en el mismo *pull request*.
