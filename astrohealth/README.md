# AstroHealth · Aplicación

Aplicación **Laravel 13 + Inertia 3 + Vue 3 + TypeScript + Tailwind CSS v4** de AstroHealth. El mismo código corre en los dos nodos (`APP_NODO=nave` o `tierra`).

| Para… | Lea |
|---|---|
| Instalarla en su computador | [Preparar el entorno local](../documentacion/modulos/00-arquitectura/08-entorno-local.md) |
| Saber dónde va cada cosa | [Estructura de carpetas](../documentacion/modulos/00-arquitectura/10-estructura-de-carpetas.md) |
| Las reglas obligatorias | [ARCHITECTURE.md](../documentacion/modulos/00-arquitectura/ARCHITECTURE.md) |
| Trabajar con Git | [Flujo de trabajo con Git](../documentacion/modulos/00-arquitectura/09-flujo-de-git.md) |
| Qué construir en cada historia | [Fichas de los módulos](../documentacion/modulos/README.md) |

```powershell
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan migrate
composer run dev        # http://localhost:8000
```

| Carpeta | Qué es |
|---|---|
| [`app/`](app/README.md) | Backend: los 16 módulos en [`app/Modules`](app/Modules/README.md) y lo común en [`app/Shared`](app/Shared/README.md) |
| `resources/js/` | Frontend: los módulos en [`resources/js/modules`](resources/js/modules/README.md) y lo global al lado |
| `resources/css/` | Tailwind v4 y las variables de color |
| `routes/` | Rutas del *starter kit* (las de cada módulo viven en su `routes.php`) |
| `database/` | Migraciones y *seeders* del kit (`users`, `cache`, `jobs`); las de cada módulo, en su carpeta |
| `config/`, `bootstrap/` | Configuración de Laravel; `bootstrap/providers.php` registra los módulos |
| `tests/` | Configuración de Pest y la prueba de arquitectura; las pruebas de cada módulo, en su carpeta |
| [`docs/`](docs/README.md) | Documentación **técnica del código** (decisiones de implementación, diagramas, despliegue) |
| `public/` | Lo único accesible desde el navegador |
| `storage/` | Archivos privados, *logs* y caché (no se suben) |

> 📝 **Documentar siempre:** si cambia lo que hace esta carpeta o añade un tipo de archivo nuevo, actualice este README y el CHANGELOG en el mismo *pull request*.
