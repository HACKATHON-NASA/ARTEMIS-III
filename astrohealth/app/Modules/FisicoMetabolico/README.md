# M10 · Físico y metabólico · Backend

> `astrohealth/app/Modules/FisicoMetabolico/` · Espacio de nombres `App\Modules\FisicoMetabolico` · [Frontend de este módulo](../../../resources/js/modules/FisicoMetabolico/README.md) · [Estructura de carpetas](../../../../documentacion/modulos/00-arquitectura/10-estructura-de-carpetas.md) · [Norma](../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md)

Registrar **las sesiones de ejercicio**, que son la principal contramedida contra la pérdida ósea y muscular; seguir la **masa corporal** y la **fuerza de prensión** semana a semana; registrar la **ingesta y la hidratación**, y vigilar la **glucosa** del monitor continuo ([Fase 1 §2.9](../../../../documentacion/docs/investigacion/02-monitorizacion-salud-fase1.md#29-nutrición-metabolismo-e-hidratación)).

**Especificación** (qué construir): [ficha](../../../../documentacion/modulos/M10-fisico-metabolico/README.md) · [backend](../../../../documentacion/modulos/M10-fisico-metabolico/backend/README.md) · [base de datos](../../../../documentacion/modulos/M10-fisico-metabolico/database/README.md) · [frontend](../../../../documentacion/modulos/M10-fisico-metabolico/frontend/README.md).

## Capas de este módulo

| Carpeta | Qué es |
|---|---|
| `FisicoMetabolicoServiceProvider.php` y `routes.php` | Se crean en HU-00.01: registran rutas, migraciones, contratos y *listeners* del módulo |
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
