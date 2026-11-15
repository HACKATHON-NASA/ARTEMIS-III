<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Tests\Feature;

use App\Models\User;
use App\Modules\Entorno\Infrastructure\Models\Astronaut;
use App\Modules\Entorno\Infrastructure\Models\Mission;
use App\Modules\Evaluacion\Database\Seeders\PsychTestSeeder;
use App\Modules\Gamificacion\Database\Seeders\AchievementSeeder;
use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Tests\Support\InteractsWithJwt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * /api/v1/psych-tests y /api/v1/psych-results: el backend puntúa, el ítem
 * crítico avisa a Tierra con urgencia, cada test suma +10 en M7 y las
 * respuestas una por una solo las ve el equipo médico. Hoy es el día 24.
 */
final class PsychApiTest extends TestCase
{
    use InteractsWithJwt;
    use RefreshDatabase;

    private Mission $mission;

    private Astronaut $elena;

    private User $elenaUser;

    private User $surgeon;

    /** @var array<string, int> */
    private array $ids = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([PsychTestSeeder::class, AchievementSeeder::class]);
        $this->mission = $this->mission();
        $this->elena = $this->astronaut($this->mission);
        $this->elenaUser = $this->userWithRole(Role::Astronaut, $this->elena);
        $this->surgeon = User::factory()->create(['role' => Role::FlightSurgeon, 'mission_id' => $this->mission->id]);
        $this->ids = array_column($this->getJson('/api/v1/psych-tests', $this->as($this->elenaUser))->json('data'), 'id', 'code');
    }

    /** @return array<string, string> */
    private function as(User $user): array
    {
        return $this->bearer($this->tokenFor($user));
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    private function submit(array $body, ?User $user = null): array
    {
        return $this->postJson('/api/v1/psych-results', $body, $this->as($user ?? $this->elenaUser))->assertCreated()->json('data');
    }

    public function test_the_catalog_has_the_four_base_tests(): void
    {
        $tests = $this->getJson('/api/v1/psych-tests', $this->as($this->elenaUser))->assertOk()->json('data');

        $this->assertSame(['PHQ-9', 'GAD-7', 'PSS-4', 'PVT'], array_column($tests, 'code'));
        $this->assertCount(9, $tests[0]['questions']);
        $this->assertTrue($tests[0]['questions'][8]['critical']);
        $this->assertTrue($tests[2]['questions'][1]['reverse']);
        $this->assertSame('cognitive', $tests[3]['kind']);
    }

    public function test_the_backend_scores_a_questionnaire_and_gives_ten_points(): void
    {
        // PHQ-9: 1+1+2+2+1+0+1+0+0 = 8 → Leve, amarillo.
        $result = $this->submit(['test_id' => $this->ids['PHQ-9'], 'answers' => [1, 1, 2, 2, 1, 0, 1, 0, 0], 'score' => 0]);

        $this->assertSame(8, $result['score']);
        $this->assertSame('Leve', $result['band']);
        $this->assertSame('yellow', $result['status']);
        $this->assertSame(24, $result['day']);
        $this->assertFalse($result['flagged']);
        $this->assertArrayNotHasKey('answers', $result);

        $profile = $this->getJson("/api/v1/astronauts/{$this->elena->id}/gamification", $this->as($this->elenaUser))->json('data');
        $this->assertSame(10, $profile['points']);
        $this->assertSame("{$this->ids['PHQ-9']}-24", $profile['recent'][0]['reference']);
    }

    public function test_the_same_test_twice_a_day_only_counts_once(): void
    {
        $this->submit(['test_id' => $this->ids['GAD-7'], 'answers' => [0, 0, 0, 0, 0, 0, 0]]);
        $this->submit(['test_id' => $this->ids['GAD-7'], 'answers' => [1, 0, 0, 0, 0, 0, 0]]);

        $profile = $this->getJson("/api/v1/astronauts/{$this->elena->id}/gamification", $this->as($this->elenaUser))->json('data');
        $this->assertSame(10, $profile['points']);
    }

    public function test_reverse_questions_and_the_pvt(): void
    {
        // PSS-4: 4 + (4−0) + (4−0) + 4 = 16 → Estrés alto.
        $pss = $this->submit(['test_id' => $this->ids['PSS-4'], 'answers' => [4, 0, 0, 4]]);
        $this->assertSame(16, $pss['score']);
        $this->assertSame('red', $pss['status']);

        $pvt = $this->submit(['test_id' => $this->ids['PVT'], 'reactions' => [250, 350, 0, 600], 'false_starts' => 2]);
        $this->assertSame(400, $pvt['score']);
        $this->assertSame('Atención lenta', $pvt['band']);
        $this->assertSame('1 lapso > 500 ms · 2 anticipos', $pvt['detail']);
    }

    public function test_answers_that_do_not_fit_the_test_are_rejected(): void
    {
        $headers = $this->as($this->elenaUser);

        $this->postJson('/api/v1/psych-results', ['test_id' => $this->ids['PHQ-9'], 'answers' => [0, 0]], $headers)
            ->assertUnprocessable()->assertJsonPath('error_code', 'EVALUATION_PSYCH_INVALID_ANSWERS');
        $this->postJson('/api/v1/psych-results', ['test_id' => $this->ids['PSS-4'], 'answers' => [0, 0, 0, 9]], $headers)
            ->assertUnprocessable();
        $this->postJson('/api/v1/psych-results', ['test_id' => $this->ids['PVT'], 'reactions' => []], $headers)
            ->assertUnprocessable();
        $this->postJson('/api/v1/psych-results', ['test_id' => 999, 'answers' => []], $headers)
            ->assertNotFound();
    }

    public function test_a_risk_answer_sends_an_urgent_alert_to_ground(): void
    {
        $result = $this->submit(['test_id' => $this->ids['PHQ-9'], 'answers' => [0, 0, 0, 0, 0, 0, 0, 0, 1]]);
        $this->assertTrue($result['flagged']);

        $alerts = $this->getJson('/api/v1/alerts', $this->as($this->surgeon))->assertOk()->json('data');
        $this->assertCount(1, $alerts);
        $this->assertSame('help', $alerts[0]['kind']);
        $this->assertSame('red', $alerts[0]['level']);
        $this->assertSame('Ánimo o estrés', $alerts[0]['category']);
        $this->assertStringContainsString('PHQ-9', (string) $alerts[0]['note']);
        $this->assertStringNotContainsString('lastimarse', (string) $alerts[0]['note'], 'la alerta no lleva la respuesta');
    }

    public function test_no_alert_without_a_risk_answer(): void
    {
        $this->submit(['test_id' => $this->ids['PHQ-9'], 'answers' => [3, 3, 3, 3, 3, 3, 3, 3, 0]]);

        $this->assertSame([], $this->getJson('/api/v1/alerts', $this->as($this->surgeon))->json('data'));
    }

    public function test_only_the_medical_team_sees_each_answer(): void
    {
        $kenji = $this->userWithRole(Role::Astronaut, $this->astronaut($this->mission));
        $stranger = $this->userWithRole(Role::Astronaut, $this->astronaut());
        $this->submit(['test_id' => $this->ids['GAD-7'], 'answers' => [1, 1, 1, 1, 1, 1, 1]]);
        $this->submit(['test_id' => $this->ids['GAD-7'], 'answers' => [0, 0, 0, 0, 0, 0, 0]], $kenji);
        $this->submit(['test_id' => $this->ids['GAD-7'], 'answers' => [0, 0, 0, 0, 0, 0, 0]], $stranger);

        $mine = $this->getJson('/api/v1/psych-results', $this->as($this->elenaUser))->assertOk()->json('data');
        $this->assertSame([$this->elena->id], array_column($mine, 'astronaut_id'));
        $this->assertArrayNotHasKey('answers', $mine[0]);

        $medical = $this->getJson('/api/v1/psych-results', $this->as($this->surgeon))->json('data');
        $this->assertCount(2, $medical, 'solo los de su misión');
        $this->assertSame([1, 1, 1, 1, 1, 1, 1], $medical[0]['answers']['answers']);
    }

    public function test_only_the_crew_answers_tests(): void
    {
        $this->postJson('/api/v1/psych-results', ['test_id' => $this->ids['PVT'], 'reactions' => [300]], $this->as($this->surgeon))
            ->assertForbidden()->assertJsonPath('error_code', 'EVALUATION_PSYCH_NOT_CREW');
    }

    /** @return array<string, mixed> */
    private function newTest(): array
    {
        return [
            'code' => 'SLP-3',
            'name' => 'Calidad del sueño',
            'kind' => 'psychological',
            'description' => 'Cómo duerme en la nave.',
            'instructions' => 'En la última semana…',
            'frequency_days' => 7,
            'unit' => 'puntos',
            'scale' => [['label' => 'No', 'points' => 0], ['label' => 'Sí', 'points' => 2]],
            'questions' => [['id' => 's1', 'text' => '¿Se despierta de noche?', 'reverse' => false, 'critical' => false]],
            'bands' => [['max' => 0, 'label' => 'Bien', 'status' => 'green'], ['max' => 2, 'label' => 'Revisar', 'status' => 'yellow']],
            'source' => null,
        ];
    }

    public function test_the_medical_team_creates_and_changes_tests(): void
    {
        $id = $this->postJson('/api/v1/psych-tests', $this->newTest(), $this->as($this->surgeon))
            ->assertCreated()->assertJsonPath('data.code', 'SLP-3')->json('data.id');

        $this->putJson("/api/v1/psych-tests/{$id}", [...$this->newTest(), 'frequency_days' => 3], $this->as($this->surgeon))
            ->assertOk()->assertJsonPath('data.frequency_days', 3);

        $result = $this->submit(['test_id' => $id, 'answers' => [1]]);
        $this->assertSame('Revisar', $result['band']);
    }

    public function test_test_definitions_are_checked(): void
    {
        $headers = $this->as($this->surgeon);

        $this->postJson('/api/v1/psych-tests', [...$this->newTest(), 'code' => 'PHQ-9'], $headers)
            ->assertStatus(409)->assertJsonPath('error_code', 'EVALUATION_PSYCH_CODE_TAKEN');
        $this->postJson('/api/v1/psych-tests', [...$this->newTest(), 'bands' => [['max' => 5, 'label' => 'A', 'status' => 'green'], ['max' => 2, 'label' => 'B', 'status' => 'red']]], $headers)
            ->assertUnprocessable()->assertJsonValidationErrors('bands');
        $this->postJson('/api/v1/psych-tests', [...$this->newTest(), 'questions' => []], $headers)
            ->assertUnprocessable()->assertJsonValidationErrors('questions');
        $this->postJson('/api/v1/psych-tests', $this->newTest(), $this->as($this->elenaUser))->assertForbidden();
    }
}
