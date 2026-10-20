# Contratos (API pública del módulo) · M01 Identidad y acceso

> `astrohealth/app/Modules/Identidad/Contratos/` · [Ficha de backend de M01](../../../../../documentacion/modulos/M01-identidad/backend/README.md) · [Norma](../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#23-comunicación-entre-módulos-y-eventos)

Las **interfaces** (y los DTOs que devuelven) que este módulo **ofrece a los demás módulos**. Es lo único del módulo que otro módulo puede usar.

| Va aquí | No va aquí |
|---|---|
| Interfaces con nombres de lo que ofrecen (`ConsultaX`, `RegistroX`) y los DTOs de sus respuestas. | Implementaciones (van en `Infrastructure/Repositorios`), modelos Eloquent, lógica. |

## Archivos previstos (según la ficha)

| Archivo | Qué hace | Historia |
|---|---|---|
| `RegistroAuditoria.php` | Interfaz que usan todos los módulos para registrar accesos a datos de salud | HU-01.04 |

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
