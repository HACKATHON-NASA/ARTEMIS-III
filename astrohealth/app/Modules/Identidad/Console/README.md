# Console · M01 Identidad y acceso

> `astrohealth/app/Modules/Identidad/Console/` · [Ficha de backend de M01](../../../../../documentacion/modulos/M01-identidad/backend/README.md) · [Norma](../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#17-convenciones-de-nombres)

Los **comandos de Artisan** del módulo (`<modulo>:<accion>`). Los programados se registran en el *ServiceProvider*. Opcional.

| Va aquí | No va aquí |
|---|---|
| Clases `<Accion>Command` que llaman a un caso de uso. | Lógica dentro del comando. |

## Archivos previstos (según la ficha)

| Archivo | Qué hace | Historia |
|---|---|---|
| `TokenNodoCommand.php` | `identidad:token-nodo` | HU-01.03 |
| `VerificarAuditoriaCommand.php` | `identidad:verificar-auditoria` | HU-01.04 |
| `EncolarAuditoriaCommand.php` | `identidad:encolar-auditoria` (solo nave, cada hora) | HU-15.01 |

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
