# M14 · Reportes · Backend

> `astrohealth/app/Modules/Reportes/` · Espacio de nombres `App\Modules\Reportes` · [Frontend de este módulo](../../../resources/js/modules/Reportes/README.md) · [Estructura de carpetas](../../../../documentacion/modulos/00-arquitectura/10-estructura-de-carpetas.md) · [Norma](../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md)

Dar al médico de vuelo un **reporte diario por tripulante** que se lee sin navegar por todo el sistema, un **reporte de misión** con la evolución completa, y a los investigadores una **exportación anonimizada** de los datos. Como los reportes viajan a la Tierra por M15, el resumen diario es lo primero que se entiende tras un corte de enlace.

**Especificación** (qué construir): [ficha](../../../../documentacion/modulos/M14-reportes/README.md) · [backend](../../../../documentacion/modulos/M14-reportes/backend/README.md) · [base de datos](../../../../documentacion/modulos/M14-reportes/database/README.md) · [frontend](../../../../documentacion/modulos/M14-reportes/frontend/README.md).

## Capas de este módulo

| Carpeta | Qué es |
|---|---|
| `ReportesServiceProvider.php` y `routes.php` | Se crean en HU-00.01: registran rutas, migraciones, contratos y *listeners* del módulo |
| [`Contratos/`](Contratos/README.md) | Las **interfaces** (y los DTOs que devuelven) que este módulo **ofrece a los demás módulos**. |
| [`Domain/`](Domain/README.md) | Las **reglas del negocio puras**: lo que el módulo *es*, sin base de datos ni HTTP. |
| [`Application/`](Application/README.md) | Los **casos de uso**: una clase por acción (`<Verbo><Cosa>`), con un único método público `ejecutar()`. |
| [`Infrastructure/`](Infrastructure/README.md) | La **única** parte del módulo que toca la base de datos, y los adaptadores hacia afuera (*listeners*, importador de sincronización). |
| [`Http/`](Http/README.md) | La **entrada y salida HTTP** del módulo: controladores delgados, validación de entrada y forma de las respuestas. |
| [`Policies/`](Policies/README.md) | La **autorización sobre recursos concretos** (por ejemplo, "solo sus propios dispositivos"). |
| [`Console/`](Console/README.md) | Los **comandos de Artisan** del módulo (`<modulo>:<accion>`). |
| [`Database/`](Database/README.md) | Las **migraciones**, *seeders* y *factories* de las tablas de este módulo, en sus subcarpetas. |
| [`Tests/`](Tests/README.md) | Las **pruebas** del módulo: cada criterio *Dado / Cuando / Entonces* de sus historias es una prueba, escrita **antes** del código. |

Cada carpeta tiene su README con lo que va y lo que no va.

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
