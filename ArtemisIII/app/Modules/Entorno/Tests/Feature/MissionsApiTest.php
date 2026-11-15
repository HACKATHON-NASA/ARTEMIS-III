<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Tests\Feature;

use App\Models\User;
use App\Modules\Entorno\Domain\Eventos\MissionEnvironmentChanged;
use App\Modules\Entorno\Infrastructure\Models\Mission;
use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Tests\Support\InteractsWithJwt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * /api/v1/missions: listar, la misión actual, crear y cambiar el entorno.
 */
final class MissionsApiTest extends TestCase
{
    use InteractsWithJwt;
    use RefreshDatabase;

    /** @return array<string, string> */
    private function as(User $user): array
    {
        return $this->bearer($this->tokenFor($user));
    }

    private function surgeonOf(Mission $mission): User
    {
        return User::factory()->create(['role' => Role::FlightSurgeon, 'mission_id' => $mission->id]);
    }

    public function test_the_admin_sees_every_mission_with_status_day_and_crew(): void
    {
        $active = $this->mission();
        $crew = $this->astronaut($active);
        Mission::factory()->create(['name' => 'Futura', 'start_date' => now()->addDays(10)->toDateString()]);
        Mission::factory()->create(['name' => 'Vieja', 'start_date' => now()->subDays(100)->toDateString(), 'day_count' => 30]);

        $this->getJson('/api/v1/missions', $this->as($this->userWithRole(Role::Admin)))
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.0.id', $active->id)
            ->assertJsonPath('data.0.status', 'active')
            ->assertJsonPath('data.0.current_day', 24)
            ->assertJsonPath('data.0.crew.0.id', $crew->id)
            ->assertJsonPath('data.1.status', 'planned')
            ->assertJsonPath('data.1.current_day', null)
            ->assertJsonPath('data.2.status', 'finished');
    }

    public function test_everyone_else_sees_only_their_own_mission(): void
    {
        $mine = $this->mission();
        $this->mission();

        foreach ([$this->surgeonOf($mine), $this->userWithRole(Role::Astronaut, $this->astronaut($mine))] as $person) {
            $this->getJson('/api/v1/missions', $this->as($person))
                ->assertOk()
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.id', $mine->id);
        }
    }

    public function test_the_current_mission_has_the_numbers_of_its_environment(): void
    {
        $mission = $this->mission();

        $this->getJson('/api/v1/missions/current', $this->as($this->userWithRole(Role::Astronaut, $this->astronaut($mission))))
            ->assertOk()
            ->assertJsonPath('data.id', $mission->id)
            ->assertJsonPath('data.environment', 'mars')
            ->assertJsonPath('data.environment_label', 'Rumbo a Marte')
            ->assertJsonPath('data.gravity_g', 0.38)
            ->assertJsonPath('data.ground_delay_seconds', 1200)
            ->assertJsonPath('data.day', 24)
            ->assertJsonPath('data.day_count', 30);
    }

    public function test_without_any_mission_there_is_no_current_one(): void
    {
        $this->getJson('/api/v1/missions/current', $this->as($this->userWithRole(Role::FlightSurgeon)))
            ->assertNotFound()
            ->assertJsonPath('error_code', 'ENV_NO_CURRENT_MISSION');
    }

    public function test_the_admin_creates_a_mission(): void
    {
        $this->postJson('/api/v1/missions', [
            'name' => 'Selene-3',
            'environment' => 'moon',
            'start_date' => now()->addDays(5)->toDateString(),
            'day_count' => 14,
            'objective' => '  Polvo lunar  ',
        ], $this->as($this->userWithRole(Role::Admin)))
            ->assertCreated()
            ->assertJsonPath('data.name', 'Selene-3')
            ->assertJsonPath('data.status', 'planned')
            ->assertJsonPath('data.objective', 'Polvo lunar')
            ->assertJsonPath('data.crew', []);
    }

    public function test_creating_validates_and_the_name_is_unique(): void
    {
        $admin = $this->as($this->userWithRole(Role::Admin));
        $taken = $this->mission();

        $this->postJson('/api/v1/missions', ['name' => 'X', 'environment' => 'venus', 'start_date' => '15/11/2026', 'day_count' => 0], $admin)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'environment', 'start_date', 'day_count']);

        $this->postJson('/api/v1/missions', ['name' => $taken->name, 'environment' => 'iss', 'start_date' => '2026-12-01', 'day_count' => 10], $admin)
            ->assertUnprocessable()
            ->assertJsonPath('error_code', 'ENV_MISSION_NAME_TAKEN')
            ->assertJsonValidationErrors(['name']);
    }

    public function test_only_the_admin_creates_missions(): void
    {
        $this->postJson('/api/v1/missions', [], $this->as($this->surgeonOf($this->mission())))->assertForbidden();
    }

    public function test_the_flight_surgeon_changes_the_environment_of_their_mission(): void
    {
        Event::fake([MissionEnvironmentChanged::class]);
        $mission = $this->mission();

        $this->patchJson("/api/v1/missions/{$mission->id}/environment", ['environment' => 'iss'], $this->as($this->surgeonOf($mission)))
            ->assertOk()
            ->assertJsonPath('data.environment', 'iss')
            ->assertJsonPath('data.ground_delay_seconds', 1);

        $this->assertSame('iss', $mission->fresh()?->environment);
        Event::assertDispatched(fn (MissionEnvironmentChanged $event): bool => $event->missionId === $mission->id
            && $event->from->value === 'mars' && $event->to->value === 'iss');
    }

    public function test_choosing_the_same_environment_publishes_nothing(): void
    {
        Event::fake([MissionEnvironmentChanged::class]);
        $mission = $this->mission();

        $this->patchJson("/api/v1/missions/{$mission->id}/environment", ['environment' => 'mars'], $this->as($this->userWithRole(Role::Admin)))
            ->assertOk();

        Event::assertNotDispatched(MissionEnvironmentChanged::class);
    }

    public function test_a_flight_surgeon_cannot_change_another_mission(): void
    {
        $other = $this->mission();

        $this->patchJson("/api/v1/missions/{$other->id}/environment", ['environment' => 'moon'], $this->as($this->surgeonOf($this->mission())))
            ->assertForbidden()
            ->assertJsonPath('error_code', 'ENV_NOT_YOUR_MISSION');

        $this->assertSame('mars', $other->fresh()?->environment);
    }

    public function test_the_crew_cannot_change_the_environment(): void
    {
        $mission = $this->mission();

        $this->patchJson("/api/v1/missions/{$mission->id}/environment", ['environment' => 'moon'], $this->as($this->userWithRole(Role::Astronaut, $this->astronaut($mission))))
            ->assertForbidden()
            ->assertJsonPath('error_code', 'FORBIDDEN');
    }

    public function test_an_unknown_mission_is_404(): void
    {
        $this->patchJson('/api/v1/missions/999/environment', ['environment' => 'moon'], $this->as($this->userWithRole(Role::Admin)))
            ->assertNotFound()
            ->assertJsonPath('error_code', 'ENV_MISSION_NOT_FOUND');
    }
}
