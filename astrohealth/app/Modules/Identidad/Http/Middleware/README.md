# Http/Middleware · M01 Identidad y acceso

> `astrohealth/app/Modules/Identidad/Http/Middleware/` · [Ficha de backend de M01](../../../../../../documentacion/modulos/M01-identidad/backend/README.md) · [Norma](../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#11-reglas-httpapi)

Middleware **propio del módulo**, si lo necesita. Opcional: si el módulo no tiene, la carpeta queda solo con este README.

| Va aquí | No va aquí |
|---|---|
| Clases de middleware registradas en el *ServiceProvider* o en `bootstrap/app.php`. | Middleware común a todos (va en `app/Shared/Http/Middleware`). |

## Archivos previstos (según la ficha)

| Archivo | Qué hace | Historia |
|---|---|---|
| `UsuarioActivo.php` | Cierra la sesión de un usuario desactivado | HU-01.01 |
| `RolPermitidoEnNodo.php` | Cierra la sesión si el rol no trabaja en este nodo | HU-01.01 |
| `RedirigirSegunRol.php` | `/inicio` → inicio de cada rol (con `reflash()`) | HU-01.01 |
| `TokenDeTipo.php` | Guardia de las escrituras de la API (`dispositivo`, `pwa`, `nodo`) | HU-01.03 |

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
