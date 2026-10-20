# Database/Migrations · M01 Identidad y acceso

> `astrohealth/app/Modules/Identidad/Database/Migrations/` · [Ficha de backend de M01](../../../../../../documentacion/modulos/M01-identidad/backend/README.md) · [Norma](../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#26-excepciones-documentadas)

Las **migraciones** de las tablas propias. El *provider* del módulo las carga solo.

| Va aquí | No va aquí |
|---|---|
| Una por tabla o cambio (`create_<tabla>`, `add_<cols>_to_<tabla>`), copiando la tabla de la historia de base de datos. | Editar una migración ya fusionada en `development` (se crea otra); valores clínicos. |

## Archivos previstos (según la ficha)

| Archivo | Qué hace | Historia |
|---|---|---|
| `add_rol_activo_to_users.php` | Columnas `rol` y `activo` en `users` (+ Sanctum con `tokenable_id` de 36) | HU-01.01 |
| `create_auditoria.php` | Tabla de solo inserción con sus *triggers* | HU-01.04 |
| `create_auditoria_envios.php` | Horas enviadas a la Tierra | HU-15.01 |

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
