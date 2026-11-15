<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Application;

use App\Modules\Evaluacion\Application\Servicios\MissionDay;
use App\Modules\Evaluacion\Domain\Entidades\PsychResult;
use App\Modules\Evaluacion\Domain\Entidades\PsychTest;
use App\Modules\Evaluacion\Domain\Enums\PsychKind;
use App\Modules\Evaluacion\Domain\Eventos\PsychCriticalAnswered;
use App\Modules\Evaluacion\Domain\Eventos\PsychTestTaken;
use App\Modules\Evaluacion\Domain\Excepciones\PsychProblem;
use App\Modules\Evaluacion\Domain\Reglas\PsychScoringRule;
use App\Modules\Evaluacion\Domain\Reglas\PvtScoringRule;
use App\Modules\Evaluacion\Infrastructure\Repositorios\Psych\FindPsychTestRepository;
use App\Modules\Evaluacion\Infrastructure\Repositorios\Psych\InsertPsychResultRepository;
use App\Modules\Identidad\Contratos\CurrentActor;
use App\Shared\Persistencia\Transaction;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Caso de uso: un tripulante responde un test. El BACKEND puntúa (nunca se
 * confía en un puntaje que mande la pantalla), elige la banda y el color,
 * guarda las respuestas y publica PsychTestTaken (M7 suma puntos) y, si
 * hubo una respuesta de riesgo, PsychCriticalAnswered (M5 avisa a Tierra).
 */
final readonly class SubmitPsychTest
{
    public function __construct(
        private CurrentActor $currentActor,
        private FindPsychTestRepository $findTest,
        private InsertPsychResultRepository $insertResult,
        private MissionDay $missionDay,
        private Transaction $transaction,
        private Dispatcher $events,
    ) {}

    /**
     * @param  list<int>  $answers  Cuestionario: el índice de la opción en cada pregunta.
     * @param  list<int>  $reactions  PVT: los tiempos de reacción en ms.
     */
    public function execute(int $testId, array $answers, array $reactions, int $falseStarts): PsychResult
    {
        $astronautId = $this->currentActor->claims()->astronautId ?? throw PsychProblem::notCrew();
        $test = $this->findTest->findById($testId) ?? throw PsychProblem::testNotFound();
        $now = CarbonImmutable::now();

        [$score, $detail, $flagged, $stored] = $test->kind === PsychKind::Psychological
            ? $this->questionnaire($test, $answers)
            : $this->pvt($reactions, $falseStarts);

        $band = PsychScoringRule::bandFor($test->bands, $score);
        $day = max(1, $this->missionDay->of($astronautId, $now));

        return $this->transaction->run(function () use ($test, $astronautId, $day, $now, $score, $band, $detail, $flagged, $stored): PsychResult {
            $result = $this->insertResult->insert([
                'test_id' => $test->id,
                'astronaut_id' => $astronautId,
                'day' => $day,
                'taken_at' => $now,
                'score' => $score,
                'status' => $band['status'],
                'band' => $band['label'],
                'detail' => $detail,
                'flagged' => $flagged,
                'answers' => $stored,
            ]);

            $this->events->dispatch(new PsychTestTaken($astronautId, $test->id, $day));

            if ($flagged) {
                $this->events->dispatch(new PsychCriticalAnswered($astronautId, $test->code));
            }

            return $result;
        });
    }

    /**
     * @param  list<int>  $answers
     * @return array{int, null, bool, array<string, mixed>}
     */
    private function questionnaire(PsychTest $test, array $answers): array
    {
        $options = count($test->scale);

        if (count($answers) !== count($test->questions)) {
            throw PsychProblem::answersDoNotFit();
        }

        foreach ($answers as $answer) {
            if ($answer < 0 || $answer >= $options) {
                throw PsychProblem::answersDoNotFit();
            }
        }

        $scored = PsychScoringRule::score($test, $answers);

        return [$scored['score'], null, $scored['flagged'], ['answers' => $answers]];
    }

    /**
     * @param  list<int>  $reactions
     * @return array{int, string, bool, array<string, mixed>}
     */
    private function pvt(array $reactions, int $falseStarts): array
    {
        if ($reactions === []) {
            throw PsychProblem::answersDoNotFit();
        }

        $scored = PvtScoringRule::score($reactions, $falseStarts);

        return [$scored['score'], $scored['detail'], false, ['reactions' => $reactions, 'false_starts' => $falseStarts]];
    }
}
