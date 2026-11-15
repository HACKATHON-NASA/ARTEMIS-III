<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Application;

use App\Modules\Gamificacion\Application\Servicios\MissionClock;
use App\Modules\Gamificacion\Application\Servicios\PointsAwarder;
use App\Modules\Gamificacion\Domain\Enums\PointReason;
use App\Modules\Gamificacion\Infrastructure\Configuracion\GamificationSettings;
use App\Modules\Registro\Contratos\IndicatorCatalog;
use App\Modules\Registro\Contratos\MeasurementQuery;
use App\Modules\Registro\Domain\Eventos\MeasurementsRecorded;

/**
 * Caso de uso: puntos por lo que se registró (M1 publica MeasurementsRecorded).
 * - Registro completo del día: los 10 indicadores medidos ese día de misión.
 * - Dormir 7 h o más · 90 min de ejercicio o más.
 * Cada uno una sola vez por día. La racha y las insignias las revisa PointsAwarder.
 */
final readonly class AwardForMeasurements
{
    public function __construct(
        private PointsAwarder $awarder,
        private MissionClock $clock,
        private MeasurementQuery $measurements,
        private IndicatorCatalog $indicators,
        private GamificationSettings $settings,
    ) {}

    public function execute(MeasurementsRecorded $event): void
    {
        $id = $event->astronautId;
        $days = [];

        foreach ($event->items as $item) {
            $day = $this->clock->dayOf($id, $item->measuredAt);

            if ($day < 1) {
                continue;
            }

            $days[$day] = $item->measuredAt;

            if ($item->indicator === 'sleep' && $item->value >= $this->settings->sleepHours()) {
                $this->awarder->award($id, PointReason::Sleep7h, (string) $day, $day);
            }

            if ($item->indicator === 'exercise_min' && $item->value >= $this->settings->exerciseMinutes()) {
                $this->awarder->award($id, PointReason::Exercise, (string) $day, $day);
            }
        }

        $total = count($this->indicators->all());

        foreach ($days as $day => $at) {
            $measured = [];

            foreach ($this->measurements->series($id, $at->utc()->startOfDay()) as $m) {
                if ($this->clock->dayOf($id, $m->measuredAt) === $day) {
                    $measured[$m->indicator] = true;
                }
            }

            if ($total > 0 && count($measured) >= $total) {
                $this->awarder->award($id, PointReason::DailyComplete, (string) $day, $day);
            }
        }
    }
}
