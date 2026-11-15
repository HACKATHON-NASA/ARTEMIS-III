<?php

use App\Modules\Alertas\AlertasServiceProvider;
use App\Modules\Cuerpo\CuerpoServiceProvider;
use App\Modules\Dashboard\DashboardServiceProvider;
use App\Modules\Entorno\EntornoServiceProvider;
use App\Modules\Evaluacion\EvaluacionServiceProvider;
use App\Modules\Gamificacion\GamificacionServiceProvider;
use App\Modules\Identidad\IdentidadServiceProvider;
use App\Modules\Registro\RegistroServiceProvider;
use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,

    // Módulos (app/Modules/<Modulo>/<Modulo>ServiceProvider.php)
    AlertasServiceProvider::class,
    CuerpoServiceProvider::class,
    DashboardServiceProvider::class,
    EntornoServiceProvider::class,
    EvaluacionServiceProvider::class,
    GamificacionServiceProvider::class,
    IdentidadServiceProvider::class,
    RegistroServiceProvider::class,
];
