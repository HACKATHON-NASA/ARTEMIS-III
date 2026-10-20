# Application (= Service: casos de uso) · M01 Identidad y acceso

> `astrohealth/app/Modules/Identidad/Application/` · [Ficha de backend de M01](../../../../../documentacion/modulos/M01-identidad/backend/README.md) · [Norma](../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#9-reglas-de-service)

Los **casos de uso**: una clase por acción (`<Verbo><Cosa>`), con un único método público `ejecutar()`. Coordina repositorios, reglas del dominio, contratos de otros módulos, la transacción y los eventos.

| Va aquí | No va aquí |
|---|---|
| Servicios como `RegistrarSintoma`; la subcarpeta `DTOs`. | SQL, `DB::`, Query Builder, `Request`, respuestas HTTP o páginas. |

## Archivos previstos (según la ficha)

| Archivo | Qué hace | Historia |
|---|---|---|
| `AutenticarUsuario.php` | Comprobaciones del inicio de sesión (lo llama `Fortify::authenticateUsing()`) | HU-01.01 |
| `GuardarUsuario.php` | Crear y editar usuarios con sus reglas | HU-01.02 |
| `DesactivarUsuario.php` | Desactiva y revoca tokens | HU-01.02 |
| `EmitirTokenDispositivo.php` | Emite (o reemplaza) el token de un dispositivo | HU-01.03 |
| `RevocarToken.php` | Revoca un token | HU-01.03 |
| `EmitirTokenPwa.php` | Token de la cola offline al iniciar sesión | HU-15.04 |
| `RegistrarAuditoria.php` | Implementa las reglas de `ver` (10 minutos, sin recargas parciales) | HU-01.04 |

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
