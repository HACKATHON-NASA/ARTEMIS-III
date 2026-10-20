# Http/Controllers/Web · M01 Identidad y acceso

> `astrohealth/app/Modules/Identidad/Http/Controllers/Web/` · [Ficha de backend de M01](../../../../../../../documentacion/modulos/M01-identidad/backend/README.md) · [Norma](../../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#11-reglas-httpapi)

Controladores de las **pantallas** (Inertia): reciben la petición, llaman a **un** caso de uso y devuelven `Inertia::render()` o una redirección con *flash*.

| Va aquí | No va aquí |
|---|---|
| `<Recurso>Controller` con métodos de pocas líneas. | Consultas o reglas. |

## Archivos previstos (según la ficha)

| Archivo | Qué hace | Historia |
|---|---|---|
| `InicioController.php` | Páginas temporales de inicio de cada rol (Sprint 0) | HU-01.01 |
| `UsuarioController.php` | `/admin/usuarios` | HU-01.02 |
| `TokenController.php` | `/admin/tokens` | HU-01.03 |
| `AuditoriaController.php` | `/admin/auditoria` | HU-01.04 |

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
