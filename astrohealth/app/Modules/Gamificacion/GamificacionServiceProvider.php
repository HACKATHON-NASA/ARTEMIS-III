<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion;

use App\Modules\Gamificacion\Infrastructure\Listeners\OnMeasurementsRecorded;
use App\Modules\Registro\Domain\Eventos\MeasurementsRecorded;
use App\Shared\ModuleServiceProvider;
use Illuminate\Support\Facades\Event;

/**
 * M7 · Gamificación: puntos, niveles, rachas e insignias. Los da solo, al
 * escuchar lo que registra M1 (y, hasta F8/F9, lo que reclama el tripulante).
 */
final class GamificacionServiceProvider extends ModuleServiceProvider
{
    protected function module(): string
    {
        return 'Gamificacion';
    }

    public function boot(): void
    {
        parent::boot();

        Event::listen(MeasurementsRecorded::class, OnMeasurementsRecorded::class);
    }
}
