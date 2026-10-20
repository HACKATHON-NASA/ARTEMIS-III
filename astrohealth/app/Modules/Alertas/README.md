# M13 · Alertas y casos clínicos · Backend

> `astrohealth/app/Modules/Alertas/` · Espacio de nombres `App\Modules\Alertas` · [Frontend de este módulo](../../../resources/js/modules/Alertas/README.md) · [Estructura de carpetas](../../../../documentacion/modulos/00-arquitectura/10-estructura-de-carpetas.md) · [Norma](../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md)

Convertir los hallazgos en **alertas que las personas atienden**: las crea, evita duplicados, las prioriza, avisa a quien corresponde (con alarma sonora si es crítica), pide confirmación de los avisos, guía al CMO con protocolos paso a paso y aprende de los falsos positivos. También calcula el **semáforo** de cada tripulante.

**Especificación** (qué construir): [ficha](../../../../documentacion/modulos/M13-alertas/README.md) · [backend](../../../../documentacion/modulos/M13-alertas/backend/README.md) · [base de datos](../../../../documentacion/modulos/M13-alertas/database/README.md) · [frontend](../../../../documentacion/modulos/M13-alertas/frontend/README.md).

## Capas de este módulo

| Carpeta | Qué es |
|---|---|
| `AlertasServiceProvider.php` y `routes.php` | Se crean en HU-00.01: registran rutas, migraciones, contratos y *listeners* del módulo |
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
