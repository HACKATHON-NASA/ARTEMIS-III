<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Database\Seeders;

use App\Modules\Entorno\Contratos\CrewMember;
use App\Modules\Entorno\Contratos\CrewQuery;
use App\Modules\Entorno\Contratos\MissionQuery;
use App\Modules\Evaluacion\Domain\Entidades\PsychTest;
use App\Modules\Evaluacion\Domain\Reglas\PsychScoringRule;
use App\Modules\Evaluacion\Infrastructure\Repositorios\Psych\FindPsychResultRepository;
use App\Modules\Evaluacion\Infrastructure\Repositorios\Psych\FindPsychTestRepository;
use App\Modules\Evaluacion\Infrastructure\Repositorios\Psych\InsertPsychResultRepository;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

/**
 * Los resultados de Ares-1 en los 4 tests, una vez por semana (días 3, 10,
 * 17 y 24), coherentes con el resto de la demo: Kenji duerme poco y su
 * ánimo y atención empeoran; Elena tiene la presión alta y su ansiedad y
 * estrés suben (y aún no hace los de esta semana); Amara y Lucas, estables.
 * La banda y el color salen de las mismas reglas de M2. Sin eventos: los
 * puntos de estos tests ya los pone DemoPointsSeeder. Solo fuera de
 * producción y solo para quien no tiene resultados.
 */
final class DemoPsychResultsSeeder extends Seeder
{
    private const DAYS = [3, 10, 17, 24];

    /** Por orden en la tripulación: [PHQ-9, GAD-7, PSS-4, PVT] por semana. */
    private const HISTORY = [
        [[3, 4, 5], [4, 6, 8], [5, 7, 9], [265, 270, 285]],
        [[3, 5, 8, 11], [3, 4, 5, 6], [4, 6, 8, 9], [255, 290, 330, 372]],
        [[2, 3, 3, 4], [3, 3, 4, 5], [4, 5, 6, 7], [248, 252, 262, 270]],
        [[1, 1, 2, 1], [1, 2, 1, 2], [3, 3, 4, 3], [240, 238, 245, 242]],
    ];

    private const CODES = ['PHQ-9', 'GAD-7', 'PSS-4', 'PVT'];

    public function run(
        CrewQuery $crew,
        MissionQuery $missions,
        FindPsychTestRepository $tests,
        FindPsychResultRepository $results,
        InsertPsychResultRepository $insert,
    ): void {
        if (app()->isProduction()) {
            return;
        }

        $byCode = [];

        foreach ($tests->all() as $test) {
            $byCode[$test->code] = $test;
        }

        $all = $crew->all();
        $members = array_values(array_filter($all, fn (CrewMember $member): bool => $member->missionId === ($all[0]->missionId ?? 0)));

        foreach ($members as $index => $member) {
            $start = $missions->startDateOf($member->missionId);

            if ($start === null || ! isset(self::HISTORY[$index]) || $results->hasAny($member->id)) {
                continue;
            }

            foreach (self::HISTORY[$index] as $testIndex => $scores) {
                $test = $byCode[self::CODES[$testIndex]] ?? null;

                if ($test instanceof PsychTest) {
                    $this->insertAll($insert, $test, $member->id, $start, $scores);
                }
            }
        }
    }

    /**
     * @param  list<int>  $scores
     */
    private function insertAll(InsertPsychResultRepository $insert, PsychTest $test, int $astronautId, CarbonImmutable $start, array $scores): void
    {
        foreach ($scores as $week => $score) {
            $day = self::DAYS[$week];
            $band = PsychScoringRule::bandFor($test->bands, $score);
            $lapses = $score > 330 ? 2 : 0;

            $insert->insert([
                'test_id' => $test->id,
                'astronaut_id' => $astronautId,
                'day' => $day,
                'taken_at' => $start->utc()->startOfDay()->addDays($day - 1)->addHours(9),
                'score' => $score,
                'status' => $band['status'],
                'band' => $band['label'],
                'detail' => $test->code === 'PVT' ? "{$lapses} lapsos > 500 ms · 0 anticipos" : null,
                'flagged' => false,
                'answers' => ['demo' => true],
            ]);
        }
    }
}
