<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion;

use App\Modules\Evaluacion\Domain\Eventos\PsychTestTaken;
use App\Modules\Gamificacion\Infrastructure\Listeners\OnMeasurementsRecorded;
use App\Modules\Gamificacion\Infrastructure\Listeners\OnPsychTestTaken;
use App\Modules\Gamificacion\Infrastructure\Listeners\OnReminderCompleted;
use App\Modules\Recordatorios\Domain\Eventos\ReminderCompleted;
use App\Modules\Registro\Domain\Eventos\MeasurementsRecorded;
use App\Shared\ModuleServiceProvider;
use Illuminate\Support\Facades\Event;

/**
 * M7 · Gamificación: puntos, niveles, rachas e insignias. Los da solo, al
 * escuchar lo que registra M1, los recordatorios de M8 y los tests de M2.
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
        Event::listen(ReminderCompleted::class, OnReminderCompleted::class);
        Event::listen(PsychTestTaken::class, OnPsychTestTaken::class);
    }
}
