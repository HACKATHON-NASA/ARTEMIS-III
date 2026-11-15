<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Infrastructure\Listeners;

use App\Modules\Gamificacion\Application\AwardForMeasurements;
use App\Modules\Registro\Domain\Eventos\MeasurementsRecorded;

/**
 * M1 guardó mediciones: M7 da los puntos que correspondan.
 */
final readonly class OnMeasurementsRecorded
{
    public function __construct(private AwardForMeasurements $award) {}

    public function handle(MeasurementsRecorded $event): void
    {
        $this->award->execute($event);
    }
}
