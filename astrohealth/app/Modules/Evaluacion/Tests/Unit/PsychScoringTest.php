<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Tests\Unit;

use App\Modules\Evaluacion\Domain\Entidades\PsychTest;
use App\Modules\Evaluacion\Domain\Enums\PsychKind;
use App\Modules\Evaluacion\Domain\Reglas\PsychScoringRule;
use App\Modules\Evaluacion\Domain\Reglas\PvtScoringRule;
use PHPUnit\Framework\TestCase;

/**
 * Cómo puntúa M2 los tests: los mismos casos que tenía psychScoring.ts.
 */
final class PsychScoringTest extends TestCase
{
    /**
     * @param  list<array{id: string, text: string, reverse: bool, critical: bool}>  $questions
     * @param  list<array{label: string, points: int}>  $scale
     */
    private function test(array $questions, array $scale): PsychTest
    {
        return new PsychTest(1, 'T', 'Test', PsychKind::Psychological, '', '', 7, 'puntos', $scale, $questions, [
            ['max' => 4, 'label' => 'Mínimo', 'status' => 'green'],
            ['max' => 9, 'label' => 'Leve', 'status' => 'yellow'],
            ['max' => 27, 'label' => 'Severo', 'status' => 'red'],
        ], null);
    }

    /** @return array{id: string, text: string, reverse: bool, critical: bool} */
    private function q(string $id, bool $reverse = false, bool $critical = false): array
    {
        return ['id' => $id, 'text' => $id, 'reverse' => $reverse, 'critical' => $critical];
    }

    /** @return list<array{label: string, points: int}> */
    private function scale(int $top): array
    {
        return array_map(fn (int $p): array => ['label' => (string) $p, 'points' => $p], range(0, $top));
    }

    public function test_a_questionnaire_adds_the_points_of_each_answer(): void
    {
        $test = $this->test([$this->q('a'), $this->q('b'), $this->q('c')], $this->scale(3));

        $this->assertSame(['score' => 6, 'flagged' => false], PsychScoringRule::score($test, [1, 2, 3]));
        $this->assertSame(['score' => 0, 'flagged' => false], PsychScoringRule::score($test, [0, 0, 0]));
    }

    public function test_reverse_questions_count_backwards(): void
    {
        // PSS-4: escala 0–4, las preguntas 2 y 3 al revés.
        $test = $this->test([$this->q('p1'), $this->q('p2', reverse: true), $this->q('p3', reverse: true), $this->q('p4')], $this->scale(4));

        $this->assertSame(16, PsychScoringRule::score($test, [4, 0, 0, 4])['score']);
        $this->assertSame(0, PsychScoringRule::score($test, [0, 4, 4, 0])['score']);
        $this->assertSame(8, PsychScoringRule::score($test, [2, 2, 2, 2])['score']);
    }

    public function test_any_points_on_a_critical_question_flag_the_result(): void
    {
        $test = $this->test([$this->q('a'), $this->q('phq9', critical: true)], $this->scale(3));

        $this->assertFalse(PsychScoringRule::score($test, [3, 0])['flagged']);
        $this->assertTrue(PsychScoringRule::score($test, [0, 1])['flagged']);
    }

    public function test_the_band_is_the_first_whose_max_reaches_the_score(): void
    {
        $bands = $this->test([], [])->bands;

        $this->assertSame('Mínimo', PsychScoringRule::bandFor($bands, 0)['label']);
        $this->assertSame('Mínimo', PsychScoringRule::bandFor($bands, 4)['label']);
        $this->assertSame('yellow', PsychScoringRule::bandFor($bands, 5)['status']);
        $this->assertSame('Severo', PsychScoringRule::bandFor($bands, 99)['label'], 'por encima de todo, la última');
        $this->assertSame('—', PsychScoringRule::bandFor([], 3)['label']);
    }

    public function test_the_pvt_is_the_mean_of_valid_reactions(): void
    {
        $this->assertSame(
            ['score' => 400, 'lapses' => 1, 'detail' => '1 lapso > 500 ms · 2 anticipos'],
            PvtScoringRule::score([250, 350, 0, 600], 2),
        );
        $this->assertSame(0, PvtScoringRule::score([], 0)['score']);
        $this->assertSame('0 lapsos > 500 ms · 0 anticipos', PvtScoringRule::score([300], 0)['detail']);
    }
}
