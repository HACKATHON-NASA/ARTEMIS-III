# app/Modules · Los 16 módulos

Cada módulo es independiente: sus tablas, sus reglas y sus rutas. Se comunican solo por `Contratos/` y eventos ([ARCHITECTURE §23](../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#23-comunicación-entre-módulos-y-eventos)).

| Código | Carpeta | Módulo | Espacio de nombres |
|---|---|---|---|
| M01 | [`Identidad/`](Identidad/README.md) | Identidad y acceso | `App\Modules\Identidad` |
| M02 | [`Mision/`](Mision/README.md) | Misión y tripulación | `App\Modules\Mision` |
| M03 | [`Ingesta/`](Ingesta/README.md) | Ingesta de datos | `App\Modules\Ingesta` |
| M04 | [`SignosVitales/`](SignosVitales/README.md) | Signos vitales | `App\Modules\SignosVitales` |
| M05 | [`Psicologia/`](Psicologia/README.md) | Psicología y cognición | `App\Modules\Psicologia` |
| M06 | [`Sueno/`](Sueno/README.md) | Sueño | `App\Modules\Sueno` |
| M07 | [`Examenes/`](Examenes/README.md) | Exámenes médicos | `App\Modules\Examenes` |
| M08 | [`Ambiente/`](Ambiente/README.md) | Ambiente | `App\Modules\Ambiente` |
| M09 | [`Radiacion/`](Radiacion/README.md) | Radiación | `App\Modules\Radiacion` |
| M10 | [`FisicoMetabolico/`](FisicoMetabolico/README.md) | Físico y metabólico | `App\Modules\FisicoMetabolico` |
| M11 | [`Sintomas/`](Sintomas/README.md) | Síntomas | `App\Modules\Sintomas` |
| M12 | [`Analitica/`](Analitica/README.md) | Analítica y línea base | `App\Modules\Analitica` |
| M13 | [`Alertas/`](Alertas/README.md) | Alertas y casos clínicos | `App\Modules\Alertas` |
| M14 | [`Reportes/`](Reportes/README.md) | Reportes | `App\Modules\Reportes` |
| M15 | [`Sincronizacion/`](Sincronizacion/README.md) | Sincronización | `App\Modules\Sincronizacion` |
| M16 | [`Configuracion/`](Configuracion/README.md) | Configuración | `App\Modules\Configuracion` |

## Anatomía de un módulo (igual en los 16)

```
<Modulo>/
├── <Modulo>ServiceProvider.php   # Registra rutas, migraciones, contratos y listeners (HU-00.01)
├── routes.php                    # Grupo web (Inertia) y grupo API (/api/v1/<modulo>)
├── Contratos/                    # Lo único que otros módulos pueden usar
├── Domain/                       # Reglas puras: Enums, Reglas, Eventos, ValueObjects, Excepciones
├── Application/                  # Casos de uso (= Service) y DTOs
├── Infrastructure/               # Base de datos (= Repository): Repositorios, Models, Listeners, Sincronizacion
├── Http/                         # Entrada y salida (= API): Controllers/Web, Controllers/Api, Requests, Resources, Middleware
├── Policies/                     # Autorización por recurso
├── Console/                      # Comandos de Artisan
├── Database/                     # Migrations, Seeders, Factories
└── Tests/                        # Unit y Feature
```

**Ejemplo completo:** [M01 · Identidad](Identidad/README.md), con los archivos que crea cada historia en cada carpeta.

Guía detallada: [Estructura de carpetas](../../../documentacion/modulos/00-arquitectura/10-estructura-de-carpetas.md).

> 📝 **Documentar siempre:** si cambia lo que hace esta carpeta o añade un tipo de archivo nuevo, actualice este README y el CHANGELOG en el mismo *pull request*.
