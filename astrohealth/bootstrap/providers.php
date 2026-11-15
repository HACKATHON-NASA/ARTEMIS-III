<?php

use App\Modules\Entorno\EntornoServiceProvider;
use App\Modules\Identidad\IdentidadServiceProvider;
use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,

    // Módulos (app/Modules/<Modulo>/<Modulo>ServiceProvider.php)
    EntornoServiceProvider::class,
    IdentidadServiceProvider::class,
];
