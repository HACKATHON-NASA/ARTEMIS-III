<?php

declare(strict_types=1);

namespace App\Shared;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * Base de los providers de los módulos (app/Modules/<Modulo>/).
 *
 * Carga las migraciones del módulo y su routes.php con el prefijo /api/v1
 * y el grupo de middleware "api". Un módulo que necesite más (listeners,
 * Gates, bindings) sobrescribe register()/boot() y llama a parent::boot().
 */
abstract class ModuleServiceProvider extends ServiceProvider
{
    /** Nombre de la carpeta del módulo, p. ej. "Identidad". */
    abstract protected function module(): string;

    public function boot(): void
    {
        $this->loadMigrationsFrom($this->modulePath('Database/Migrations'));

        $routes = $this->modulePath('routes.php');

        if (is_file($routes) && ! $this->app->routesAreCached()) {
            Route::middleware('api')->prefix('api/v1')->group($routes);
        }
    }

    protected function modulePath(string $path = ''): string
    {
        $base = app_path('Modules/'.$this->module());

        return $path === '' ? $base : $base.'/'.$path;
    }
}
