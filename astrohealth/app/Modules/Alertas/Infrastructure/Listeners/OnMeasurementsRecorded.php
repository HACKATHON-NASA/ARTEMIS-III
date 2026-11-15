<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Infrastructure\Listeners;

use App\Modules\Alertas\Application\CreateVitalAlerts;
use App\Modules\Registro\Domain\Eventos\MeasurementsRecorded;

/**
 * M1 guardó mediciones: cada valor reciente en amarillo o en rojo crea su alerta.
 */
final readonly class OnMeasurementsRecorded
{
    public function __construct(private CreateVitalAlerts $createVitalAlerts) {}

    public function handle(MeasurementsRecorded $event): void
    {
        $this->createVitalAlerts->execute($event);
    }
}
