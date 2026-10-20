# Infrastructure/Repositorios · M01 Identidad y acceso

> `astrohealth/app/Modules/Identidad/Infrastructure/Repositorios/` · [Ficha de backend de M01](../../../../../../documentacion/modulos/M01-identidad/backend/README.md) · [Norma](../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#8-reglas-de-repository)

Las consultas y escrituras a MySQL, separadas en **lectura** (`<Agregado>LecturaRepositorio`, que implementa los `Contratos/`) y **escritura** (`<Agregado>EscrituraRepositorio`).

| Va aquí | No va aquí |
|---|---|
| Métodos con intención (`porId`, `guardar`, `marcarConfirmada`). Solo tablas de este módulo. | Un `query()` genérico hacia afuera; tablas de otros módulos. |

## Archivos previstos (según la ficha)

| Archivo | Qué hace | Historia |
|---|---|---|
| `UsuarioLecturaRepositorio.php` · `UsuarioEscrituraRepositorio.php` | Usuarios (la tabla `users` del kit) | HU-01.02 |
| `TokenLecturaRepositorio.php` · `TokenEscrituraRepositorio.php` | Tokens de Sanctum (`personal_access_tokens`) | HU-01.02, HU-01.03 |
| `AuditoriaLecturaRepositorio.php` · `AuditoriaEscrituraRepositorio.php` | Auditoría (la ficha la llama `AuditoriaEloquent`) | HU-01.04 |

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
