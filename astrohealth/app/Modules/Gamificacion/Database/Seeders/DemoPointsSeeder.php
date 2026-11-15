<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Database\Seeders;

use App\Modules\Entorno\Contratos\CrewMember;
use App\Modules\Entorno\Contratos\CrewQuery;
use App\Modules\Gamificacion\Application\Servicios\MissionClock;
use App\Modules\Gamificacion\Application\Servicios\PointsAwarder;
use App\Modules\Gamificacion\Domain\Enums\PointReason;
use App\Modules\Gamificacion\Infrastructure\Configuracion\GamificationSettings;
use App\Modules\Gamificacion\Infrastructure\Repositorios\Points\InsertPointsRepository;
use App\Modules\Registro\Contratos\MeasurementQuery;
use Illuminate\Database\Seeder;

/**
 * Los puntos de la demo de Ares-1, como los habría ganado cada tripulante
 * hasta hoy: registro completo (con algunos días sin hacer, para que las
 * rachas no sean perfectas), sueño y ejercicio según su historia real (M1),
 * recordatorios y los tests de cada semana. Al final, las insignias.
 * Solo fuera de producción; idempotente (la llave única de points_log).
 */
final class DemoPointsSeeder extends Seeder
{
    /** Días en que no completó el registro (por orden en la tripulación, desde 1). */
    private const MISSED = [2 => [9, 19], 3 => [21]];

    /** Días en que hizo los 4 tests (la primera aún no hace los de esta semana). */
    private const PSYCH_DAYS = [1 => [3, 10, 17], 2 => [3, 10, 17, 24], 3 => [3, 10, 17, 24], 4 => [3, 10, 17, 24]];

    public function run(
        CrewQuery $crew,
        MissionClock $clock,
        MeasurementQuery $measurements,
        InsertPointsRepository $insert,
        PointsAwarder $awarder,
        GamificationSettings $settings,
    ): void {
        if (app()->isProduction()) {
            return;
        }

        $all = $crew->all();
        $members = array_values(array_filter($all, fn (CrewMember $member): bool => $member->missionId === ($all[0]->missionId ?? 0)));

        foreach ($members as $index => $member) {
            $order = $index + 1;
            $start = $clock->startOf($member->id);
            $today = $clock->today($member->id);

            if ($start === null || $today < 1 || ! isset(self::PSYCH_DAYS[$order])) {
                continue;
            }

            $add = function (PointReason $reason, string $reference, int $day) use ($insert, $settings, $member, $start): void {
                $insert->add($member->id, $reason, $reference, $settings->pointsFor($reason), $day, $start->utc()->startOfDay()->addDays($day - 1)->addHours(20));
            };

            $sleep = $this->perDay($measurements, $clock, $member->id, 'sleep');
            $exercise = $this->perDay($measurements, $clock, $member->id, 'exercise_min');
            $run = 0;

            for ($day = 1; $day < $today; $day++) {
                if (in_array($day, self::MISSED[$order] ?? [], true)) {
                    $run = 0;
                } else {
                    $add(PointReason::DailyComplete, (string) $day, $day);

                    if (++$run % 7 === 0) {
                        $add(PointReason::Streak7, (string) $day, $day);
                    }
                }

                if (($sleep[$day] ?? 0) >= $settings->sleepHours()) {
                    $add(PointReason::Sleep7h, (string) $day, $day);
                }

                if (($exercise[$day] ?? 0) >= $settings->exerciseMinutes()) {
                    $add(PointReason::Exercise, (string) $day, $day);
                }

                for ($k = 0; $k < 3 + ($day * $order) % 3; $k++) {
                    $add(PointReason::Reminder, "r{$k}-{$day}", $day);
                }
            }

            foreach (self::PSYCH_DAYS[$order] as $day) {
                for ($test = 1; $test <= 4; $test++) {
                    if ($day <= $today) {
                        $add(PointReason::PsychTest, "{$test}-{$day}", $day);
                    }
                }
            }

            // Hoy: todos menos la primera ya completaron el registro. El
            // recordatorio de la presión de hoy lo marca DemoRemindersSeeder (M8).
            if ($order !== 1) {
                $add(PointReason::DailyComplete, (string) $today, $today);
            }

            $awarder->checkBadges($member->id);
        }
    }

    /**
     * El último valor de cada día de misión de un indicador.
     *
     * @return array<int, float>
     */
    private function perDay(MeasurementQuery $measurements, MissionClock $clock, int $astronautId, string $code): array
    {
        $start = $clock->startOf($astronautId);
        $values = [];

        foreach ($start === null ? [] : $measurements->series($astronautId, $start, $code) as $m) {
            $values[$clock->dayOf($astronautId, $m->measuredAt)] = $m->value;
        }

        return $values;
    }
}
