<?php

declare(strict_types=1);

namespace App\Modules\Registro\Infrastructure\Listeners;

use App\Modules\Evaluacion\Domain\Eventos\ThresholdUpdated;
use App\Modules\Registro\Application\ReevaluateLatest;

/**
 * M2 cambió el umbral de un indicador en un entorno: la última medición de
 * ese indicador, en las misiones de ese entorno, se vuelve a evaluar.
 */
final readonly class OnThresholdUpdated
{
    public function __construct(private ReevaluateLatest $reevaluate) {}

    public function handle(ThresholdUpdated $event): void
    {
        $this->reevaluate->forEnvironment($event->environment, $event->indicatorCode);
    }
}
