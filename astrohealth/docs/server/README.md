# Documentación técnica · Servidor

Todo lo del **servidor**, común a la aplicación entera (no va por módulo).

| Documento | Qué tendrá | Historia |
|---|---|---|
| `despliegue-cpanel.md` | Paso a paso para desplegar los nodos nave y Tierra en cPanel | HU-00.04 |
| `variables-de-entorno.md` | El `.env` de cada nodo (sin valores secretos) | HU-00.04 |
| `cron-y-colas.md` | `schedule:run` cada minuto y la cola `database` | HU-00.04 |
| `ci.md` | Qué ejecuta la integración continua en cada *pull request* | HU-00.02 |
| `logs-y-errores.md` | Dónde están los *logs* y cómo buscar un `traza_id` | HU-00.01 |

Versiones del servidor (cPanel): **PHP 8.4** y base de datos del hosting con la conexión `mysql`.

> 📝 **Documentar siempre:** este documento se actualiza en el mismo *pull request* que cambia el código.
