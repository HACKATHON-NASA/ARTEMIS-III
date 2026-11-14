<?php

use App\Modules\Entorno\EntornoServiceProvider;
use App\Modules\Identidad\IdentidadServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\FortifyServiceProvider;

return [
    AppServiceProvider::class,
    FortifyServiceProvider::class,

    // Módulos (app/Modules/<Modulo>/<Modulo>ServiceProvider.php)
    EntornoServiceProvider::class,
    IdentidadServiceProvider::class,
];
