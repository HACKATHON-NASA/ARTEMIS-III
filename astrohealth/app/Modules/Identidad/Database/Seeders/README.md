# Database/Seeders · M01 Identidad y acceso

> `astrohealth/app/Modules/Identidad/Database/Seeders/` · [Ficha de backend de M01](../../../../../../documentacion/modulos/M01-identidad/backend/README.md) · [Norma](../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#26-excepciones-documentadas)

Los **datos iniciales** del módulo. Se pueden ejecutar dos veces sin duplicar (`updateOrCreate`). `DatabaseSeeder` los llama en orden.

| Va aquí | No va aquí |
|---|---|
| `<Cosa>Seeder`. | Datos de prueba de una sola prueba (van en *factories*). |

## Archivos previstos (según la ficha)

| Archivo | Qué hace | Historia |
|---|---|---|
| `UsuariosSistemaSeeder.php` | `nodo-nave` y `nodo-tierra` | HU-01.01 |
| `UsuariosDePruebaSeeder.php` | Un usuario por rol, `id` fijos en los dos nodos | HU-01.01 |
| `TokenSimuladorSeeder.php` | Token del simulador (solo local) | HU-01.03 |

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
