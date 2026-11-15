<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Tests\Feature;

use App\Models\User;
use App\Modules\Evaluacion\Contratos\Evaluator;
use App\Modules\Evaluacion\Contratos\LatestReading;
use App\Modules\Evaluacion\Contratos\TrendAnalyzer;
use App\Modules\Evaluacion\Database\Seeders\ThresholdSeeder;
use App\Modules\Evaluacion\Domain\Excepciones\ThresholdMissing;
use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Tests\Support\InteractsWithJwt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Los contratos Evaluator y TrendAnalyzer (los usarán M1 y M4) y
 * GET /api/v1/astronauts/{id}/assessment.
 */
final class EvaluatorTest extends TestCase
{
    use InteractsWithJwt;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ThresholdSeeder::class);
    }

    public function test_it_colors_a_value_with_the_thresholds_of_the_environment(): void
    {
        $evaluator = app(Evaluator::class);

        $this->assertSame('red', $evaluator->evaluate('bp_sys', 148, 'mars'));
        $this->assertSame('red', $evaluator->evaluate('sleep', 5.8, 'mars'));
        $this->assertSame('yellow', $evaluator->evaluate('radiation', 0.94, 'mars'));
        $this->assertSame('green', $evaluator->evaluate('radiation', 0.94, 'transit'));
        $this->assertEquals(0.6, $evaluator->thresholdFor('radiation', 'mars')->greenMax);
    }

    public function test_a_missing_threshold_is_a_data_error(): void
    {
        $this->expectException(ThresholdMissing::class);

        app(Evaluator::class)->evaluate('pressure', 1, 'mars');
    }

    public function test_assess_saves_the_score_and_the_worst_state_of_each_zone(): void
    {
        $astronaut = $this->astronaut();

        $summary = app(Evaluator::class)->assess($astronaut->id, [
            new LatestReading('hr', 'green', 'heart'),
            new LatestReading('bp_sys', 'red', 'heart'),
            new LatestReading('sleep', 'yellow', 'brain'),
            new LatestReading('spo2', 'green', 'lungs'),
        ]);

        $this->assertNotNull($summary);
        // (1,5·100 + 1,5·20 + 1·60 + 1,5·100) / 5,5 = 70,9 → 71.
        $this->assertSame(71, $summary->score);
        $this->assertSame('yellow', $summary->status);
        $this->assertSame(['heart' => 'red', 'brain' => 'yellow', 'lungs' => 'green'], $summary->bodyStatus);
        $this->assertSame('Corazón en crítico; Mente y sueño en precaución; el resto, normal.', $summary->summary);
        $this->assertDatabaseHas('assessments', ['astronaut_id' => $astronaut->id, 'score' => 71]);
        $this->assertNull(app(Evaluator::class)->assess($astronaut->id, []));
    }

    public function test_the_trend_analyzer_warns_when_sleep_keeps_falling(): void
    {
        $trend = app(TrendAnalyzer::class)->trend('sleep', [7.5, 7.1, 6.8, 6.5], 'mars');

        $this->assertSame('down', $trend->direction);
        $this->assertTrue($trend->alert);
    }

    public function test_the_latest_assessment_is_served_to_whoever_may_see_it(): void
    {
        $mission = $this->mission();
        $me = $this->astronaut($mission);
        $evaluator = app(Evaluator::class);
        $evaluator->assess($me->id, [new LatestReading('hr', 'red', 'heart')]);
        $evaluator->assess($me->id, [new LatestReading('hr', 'green', 'heart')]);

        $this->getJson("/api/v1/astronauts/{$me->id}/assessment", $this->bearer($this->tokenFor($this->userWithRole(Role::Astronaut, $me))))
            ->assertOk()
            ->assertJsonPath('data.score', 100)
            ->assertJsonPath('data.status', 'green')
            ->assertJsonPath('data.body_status.heart', 'green')
            ->assertJsonPath('data.summary', 'Todo en orden.');

        $surgeon = User::factory()->create(['role' => Role::FlightSurgeon, 'mission_id' => $mission->id]);
        $this->getJson("/api/v1/astronauts/{$me->id}/assessment", $this->bearer($this->tokenFor($surgeon)))->assertOk();
    }

    public function test_others_cannot_see_it_and_without_data_it_is_404(): void
    {
        $mission = $this->mission();
        $other = $this->astronaut($mission);
        $token = $this->tokenFor($this->userWithRole(Role::Astronaut, $this->astronaut($mission)));

        $this->getJson("/api/v1/astronauts/{$other->id}/assessment", $this->bearer($token))->assertForbidden();

        $this->getJson("/api/v1/astronauts/{$other->id}/assessment", $this->bearer($this->tokenFor($this->userWithRole(Role::Admin))))
            ->assertNotFound()
            ->assertJsonPath('error_code', 'EVALUATION_NO_DATA');
    }
}
