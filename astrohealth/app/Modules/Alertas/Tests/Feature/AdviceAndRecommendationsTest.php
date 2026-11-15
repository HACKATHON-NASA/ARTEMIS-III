<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Tests\Feature;

use App\Models\User;
use App\Modules\Alertas\Database\Seeders\RecommendationSeeder;
use App\Modules\Entorno\Infrastructure\Models\Astronaut;
use App\Modules\Entorno\Infrastructure\Models\Mission;
use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Tests\Support\InteractsWithJwt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * /api/v1/advice (indicaciones del médico) y /api/v1/recommendations ("qué hacer").
 */
final class AdviceAndRecommendationsTest extends TestCase
{
    use InteractsWithJwt;
    use RefreshDatabase;

    private Mission $mission;

    private Astronaut $kenji;

    private User $kenjiUser;

    private User $surgeon;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mission = $this->mission();
        $this->kenji = $this->astronaut($this->mission);
        $this->kenjiUser = $this->userWithRole(Role::Astronaut, $this->kenji);
        $this->surgeon = User::factory()->create(['name' => 'Dra. Sofía Ramírez', 'role' => Role::FlightSurgeon, 'mission_id' => $this->mission->id]);
    }

    /** @return array<string, string> */
    private function as(User $user): array
    {
        return $this->bearer($this->tokenFor($user));
    }

    /** @return array<string, mixed> */
    private function draft(): array
    {
        return ['astronaut_id' => $this->kenji->id, 'category' => 'sleep', 'title' => 'Higiene del sueño', 'body' => "Apaga pantallas\nUsa el antifaz", 'important' => true, 'valid_days' => 7];
    }

    public function test_the_advice_travels_to_the_ship_and_is_read_when_it_arrives(): void
    {
        $advice = $this->postJson('/api/v1/advice', $this->draft(), $this->as($this->surgeon))
            ->assertCreated()
            ->assertJsonPath('data.author', 'Dra. Sofía Ramírez')
            ->assertJsonPath('data.delay_seconds', 1200)
            ->json('data');

        $this->getJson('/api/v1/advice', $this->as($this->kenjiUser))->assertJsonCount(1, 'data');

        $this->patchJson("/api/v1/advice/{$advice['id']}/read", [], $this->as($this->kenjiUser))
            ->assertStatus(409)
            ->assertJsonPath('error_code', 'ALERTS_ADVICE_NOT_ARRIVED');

        Carbon::setTestNow(now()->addMinutes(21));
        $this->patchJson("/api/v1/advice/{$advice['id']}/read", [], $this->as($this->kenjiUser))
            ->assertOk()
            ->assertJsonPath('data.read_at', fn ($value): bool => $value !== null);
        Carbon::setTestNow();
    }

    public function test_only_its_crew_member_marks_it_and_only_doctors_of_the_mission_send(): void
    {
        $advice = $this->postJson('/api/v1/advice', $this->draft(), $this->as($this->surgeon))->json('data.id');
        $mate = $this->userWithRole(Role::Astronaut, $this->astronaut($this->mission));

        $this->patchJson("/api/v1/advice/{$advice}/read", [], $this->as($mate))
            ->assertForbidden()
            ->assertJsonPath('error_code', 'ALERTS_ADVICE_NOT_YOURS');
        $this->getJson('/api/v1/advice', $this->as($mate))->assertJsonCount(0, 'data');

        $stranger = User::factory()->create(['role' => Role::FlightSurgeon, 'mission_id' => $this->mission()->id]);
        $this->postJson('/api/v1/advice', $this->draft(), $this->as($stranger))
            ->assertForbidden()
            ->assertJsonPath('error_code', 'ALERTS_NOT_YOUR_PATIENT');

        $this->postJson('/api/v1/advice', $this->draft(), $this->as($this->kenjiUser))->assertForbidden();
        $this->postJson('/api/v1/advice', ['astronaut_id' => $this->kenji->id], $this->as($this->surgeon))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['category', 'title', 'body']);
    }

    public function test_recommendations_are_read_by_all_and_edited_by_the_guide(): void
    {
        $this->seed(RecommendationSeeder::class);

        $this->getJson('/api/v1/recommendations', $this->as($this->kenjiUser))
            ->assertOk()
            ->assertJsonCount(20, 'data');

        $this->putJson('/api/v1/recommendations/sleep/yellow', [
            'title' => 'Poco sueño',
            'action_text' => "Apaga pantallas\n\n  Duerme a la misma hora  ",
            'risk_ref' => 'https://www.nasa.gov/hrp/risks/',
        ], $this->as($this->surgeon))
            ->assertOk()
            ->assertJsonPath('data.action_text', "Apaga pantallas\nDuerme a la misma hora");

        $this->assertDatabaseHas('recommendations', ['indicator_code' => 'sleep', 'status' => 'yellow', 'title' => 'Poco sueño']);
        $this->putJson('/api/v1/recommendations/sleep/green', ['title' => 'x', 'action_text' => 'y'], $this->as($this->surgeon))->assertNotFound();
        $this->putJson('/api/v1/recommendations/sleep/red', ['title' => 'x', 'action_text' => 'y'], $this->as($this->kenjiUser))->assertForbidden();
    }
}
