<?php

declare(strict_types=1);

namespace App\Modules\Alertas;

use App\Modules\Alertas\Infrastructure\Listeners\OnMeasurementsRecorded;
use App\Modules\Registro\Domain\Eventos\MeasurementsRecorded;
use App\Shared\ModuleServiceProvider;
use Illuminate\Support\Facades\Event;

/**
 * M5 · Alertas: dueño de alerts, recommendations y advice. Los valores en
 * amarillo y rojo se vuelven alertas solas; el tripulante habla con Tierra
 * y el médico le da indicaciones, con el retraso del entorno.
 */
final class AlertasServiceProvider extends ModuleServiceProvider
{
    protected function module(): string
    {
        return 'Alertas';
    }

    public function boot(): void
    {
        parent::boot();

        Event::listen(MeasurementsRecorded::class, OnMeasurementsRecorded::class);
    }
}
