<?php

use App\Modules\Entorno\EntornoServiceProvider;
use App\Modules\Evaluacion\EvaluacionServiceProvider;
use App\Modules\Identidad\IdentidadServiceProvider;
use App\Modules\Registro\RegistroServiceProvider;
use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,

    // Módulos (app/Modules/<Modulo>/<Modulo>ServiceProvider.php)
    EntornoServiceProvider::class,
    EvaluacionServiceProvider::class,
    IdentidadServiceProvider::class,
    RegistroServiceProvider::class,
];
