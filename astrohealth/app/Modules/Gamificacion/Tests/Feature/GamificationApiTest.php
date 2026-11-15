<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Tests\Feature;

use App\Models\User;
use App\Modules\Entorno\Infrastructure\Models\Astronaut;
use App\Modules\Entorno\Infrastructure\Models\Mission;
use App\Modules\Evaluacion\Database\Seeders\ThresholdSeeder;
use App\Modules\Gamificacion\Database\Seeders\AchievementSeeder;
use App\Modules\Gamificacion\Domain\Enums\PointReason;
use App\Modules\Gamificacion\Infrastructure\Repositorios\Points\InsertPointsRepository;
use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Tests\Support\InteractsWithJwt;
use App\Modules\Registro\Database\Seeders\IndicatorSeeder;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * M7: los puntos salen solos de lo que se registra (una vez por día y por
 * acción); el perfil, el ranking de la misión y los reclamos temporales.
 * La misión de prueba empezó hace 23 días: hoy es el día 24.
 */
final class GamificationApiTest extends TestCase
{
    use InteractsWithJwt;
    use RefreshDatabase;

    private const ALL_TEN = [
        ['indicator' => 'hr', 'value' => 72],
        ['indicator' => 'bp_sys', 'value' => 118],
        ['indicator' => 'bp_dia', 'value' => 76],
        ['indicator' => 'glucose', 'value' => 90],
        ['indicator' => 'temp', 'value' => 36.6],
        ['indicator' => 'spo2', 'value' => 98],
        ['indicator' => 'sleep', 'value' => 7.5],
        ['indicator' => 'mood', 'value' => 4],
        ['indicator' => 'exercise_min', 'value' => 95],
        ['indicator' => 'radiation', 'value' => 0.3],
    ];

    private Mission $mission;

    private Astronaut $elena;

    private User $elenaUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([ThresholdSeeder::class, IndicatorSeeder::class, AchievementSeeder::class]);
        $this->mission = $this->mission();
        $this->elena = $this->astronaut($this->mission);
        $this->elenaUser = $this->userWithRole(Role::Astronaut, $this->elena);
    }

    /** @return array<string, string> */
    private function as(User $user): array
    {
        return $this->bearer($this->tokenFor($user));
    }

    /**
     * @param  list<array{indicator: string, value: int|float}>  $measurements
     */
    private function record(array $measurements): void
    {
        $this->postJson('/api/v1/measurements', ['measurements' => $measurements], $this->as($this->elenaUser))->assertCreated();
    }

    /** @return array<string, mixed> */
    private function profile(): array
    {
        return $this->getJson("/api/v1/astronauts/{$this->elena->id}/gamification", $this->as($this->elenaUser))->assertOk()->json('data');
    }

    public function test_a_complete_day_gives_its_points_and_the_first_badge(): void
    {
        $this->record(self::ALL_TEN);

        $profile = $this->profile();
        $reasons = array_column($profile['recent'], 'reason');
        sort($reasons);

        // 10 (registro completo) + 5 (7 h de sueño) + 15 (ejercicio) + 5 (bono de "Primer registro").
        $this->assertSame(35, $profile['points']);
        $this->assertSame(1, $profile['level']);
        $this->assertSame(1, $profile['streak_days']);
        $this->assertSame(['badge', 'daily_complete', 'exercise', 'sleep_7h'], $reasons);
        $this->assertSame(24, $profile['recent'][0]['day']);

        $badges = array_column($profile['badges'], null, 'code');
        $this->assertCount(9, $badges);
        $this->assertSame(24, $badges['first_log']['earned_day']);
        $this->assertSame(['current' => 1, 'target' => 1], $badges['first_log']['progress']);
        $this->assertNull($badges['orbit_7']['earned_day']);
        $this->assertSame(1, $badges['orbit_7']['progress']['current']);
    }

    public function test_the_same_action_never_counts_twice(): void
    {
        $this->record(self::ALL_TEN);
        $this->record(self::ALL_TEN);

        $this->assertSame(35, $this->profile()['points']);
    }

    public function test_an_incomplete_day_only_gives_sleep_and_exercise(): void
    {
        $this->record([['indicator' => 'sleep', 'value' => 6.2], ['indicator' => 'exercise_min', 'value' => 90], ['indicator' => 'hr', 'value' => 70]]);

        $profile = $this->profile();
        $this->assertSame(15, $profile['points']);
        $this->assertSame(0, $profile['streak_days']);
        $this->assertSame(['exercise'], array_column($profile['recent'], 'reason'));
    }

    public function test_seven_days_in_a_row_give_the_streak_bonus_and_the_orbit_badge(): void
    {
        $insert = app(InsertPointsRepository::class);

        foreach (range(18, 23) as $day) {
            $insert->add($this->elena->id, PointReason::DailyComplete, (string) $day, 10, $day, CarbonImmutable::now()->subDays(24 - $day));
        }

        $this->record(self::ALL_TEN);

        $profile = $this->profile();
        $references = array_map(fn (array $e): string => $e['reason'].':'.$e['reference'], $profile['recent']);
        $badges = array_column($profile['badges'], null, 'code');

        $this->assertSame(7, $profile['streak_days']);
        $this->assertContains('streak_7:24', $references);
        $this->assertContains('badge:orbit_7', $references);
        $this->assertSame(24, $badges['orbit_7']['earned_day']);
        $this->assertSame(18, $badges['first_log']['earned_day']);
        // 70 (7 días) + 5 + 15 + 50 (racha) + 5 (primer registro) + 20 (7 días en órbita).
        $this->assertSame(165, $profile['points']);
    }

    public function test_a_crew_member_claims_a_test_only_once(): void
    {
        $this->postJson('/api/v1/gamification/claims', ['reason' => 'psych_test', 'reference' => '1-24'], $this->as($this->elenaUser))
            ->assertOk()->assertJsonPath('data.awarded', true);
        $this->postJson('/api/v1/gamification/claims', ['reason' => 'psych_test', 'reference' => '1-24'], $this->as($this->elenaUser))
            ->assertOk()->assertJsonPath('data.awarded', false);

        $this->assertSame(10, $this->profile()['points']);
    }

    public function test_points_from_events_cannot_be_claimed(): void
    {
        foreach (['daily_complete', 'badge', 'streak_7', 'reminder'] as $reason) {
            $this->postJson('/api/v1/gamification/claims', ['reason' => $reason, 'reference' => '24'], $this->as($this->elenaUser))
                ->assertUnprocessable()->assertJsonValidationErrors('reason');
        }
    }

    public function test_only_a_crew_member_claims(): void
    {
        $surgeon = User::factory()->create(['role' => Role::FlightSurgeon, 'mission_id' => $this->mission->id]);

        $this->postJson('/api/v1/gamification/claims', ['reason' => 'psych_test', 'reference' => '1-24'], $this->as($surgeon))
            ->assertForbidden()->assertJsonPath('error_code', 'GAMIFICATION_NOT_CREW');
    }

    public function test_the_leaderboard_shows_only_my_mission_ordered_by_points(): void
    {
        $kenji = $this->astronaut($this->mission);
        $stranger = $this->astronaut();
        $insert = app(InsertPointsRepository::class);
        $insert->add($kenji->id, PointReason::Reminder, 'r0-1', 50, 1, CarbonImmutable::now());
        $insert->add($stranger->id, PointReason::Reminder, 'r0-1', 999, 1, CarbonImmutable::now());
        $this->record(self::ALL_TEN);

        $rows = $this->getJson("/api/v1/gamification/leaderboard?astronauts={$this->elena->id},{$kenji->id},{$stranger->id}", $this->as($this->elenaUser))
            ->assertOk()->json('data');

        $this->assertSame([$kenji->id, $this->elena->id], array_column($rows, 'astronaut_id'));
        $this->assertSame([50, 35], array_column($rows, 'points'));
        $this->assertSame([0, 1], array_column($rows, 'badges'));
    }

    public function test_an_astronaut_cannot_see_another_missions_profile(): void
    {
        $stranger = $this->astronaut();

        $this->getJson("/api/v1/astronauts/{$stranger->id}/gamification", $this->as($this->elenaUser))->assertForbidden();
        $this->getJson("/api/v1/astronauts/{$this->elena->id}/gamification")->assertUnauthorized();
    }
}
