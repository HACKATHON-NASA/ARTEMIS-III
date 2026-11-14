# AstroHealth · Aplicación

Aplicación **Laravel 13 + Inertia 3 + Vue 3 + TypeScript + Tailwind CSS v4** de AstroHealth.

- Qué se construye: [Plan de módulos](../documentacion/PLAN_ASTROHEALTH.md)
- Cómo se programa: [Guía de arquitectura](../documentacion/arquitectura/README.md)
- Cómo quedó hecho, y el avance por módulo: [Documentación técnica](docs/README.md)

```powershell
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan migrate
composer run dev        # http://localhost:8000
```

| Carpeta                          | Qué es                                                                                                      |
| -------------------------------- | ----------------------------------------------------------------------------------------------------------- |
| `app/Modules/<Modulo>/`          | Backend de los 8 módulos, por capas: `Domain`, `Application`, `Infrastructure`, `Http`, `Database`, `Tests` |
| `app/Shared/`                    | Piezas técnicas comunes: sobre JSON, errores de negocio, transacciones                                      |
| `resources/js/modules/<Modulo>/` | Frontend de cada módulo: `Components`, `Stores`, `Composables`, `Types`, `Utils`                            |
| `resources/js/` (resto)          | Lo global: `pages` (todas las páginas), `components`, `layouts`, `stores`, `lib`, `types`                   |

| Módulo del plan              | Carpeta         |
| ---------------------------- | --------------- |
| M1 Registro de indicadores   | `Registro`      |
| M2 Motor de evaluación       | `Evaluacion`    |
| M3 Cuerpo humano interactivo | `Cuerpo`        |
| M4 Dashboard                 | `Dashboard`     |
| M5 Recomendaciones y alertas | `Alertas`       |
| M6 Entorno de misión         | `Entorno`       |
| M7 Gamificación              | `Gamificacion`  |
| M8 Recordatorios             | `Recordatorios` |
