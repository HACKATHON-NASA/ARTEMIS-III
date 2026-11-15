<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Tests\Feature;

use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Tests\Support\InteractsWithJwt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * /api/v1/astronauts: el admin suma y edita tripulantes. Con eso, crear un
 * usuario de la nave (M0) funciona de punta a punta.
 */
final class AstronautsApiTest extends TestCase
{
    use InteractsWithJwt;
    use RefreshDatabase;

    /** @return array<string, string> */
    private function asAdmin(): array
    {
        return $this->bearer($this->tokenFor($this->userWithRole(Role::Admin)));
    }

    public function test_the_admin_adds_a_crew_member_and_then_their_user(): void
    {
        $mission = $this->mission();
        $headers = $this->asAdmin();

        $astronaut = $this->postJson('/api/v1/astronauts', [
            'mission_id' => $mission->id,
            'name' => ' Nadia Petrova ',
            'crew_role' => 'Piloto',
        ], $headers)
            ->assertCreated()
            ->assertJsonPath('data.name', 'Nadia Petrova')
            ->assertJsonPath('data.mission_id', $mission->id)
            ->json('data.id');

        $this->postJson('/api/v1/users', [
            'name' => 'Nadia Petrova',
            'email' => 'npetrova@astrohealth.test',
            'role' => 'astronaut',
            'astronaut_id' => $astronaut,
        ], $headers)
            ->assertCreated()
            ->assertJsonPath('data.crew_role', 'Piloto')
            ->assertJsonPath('data.mission_name', $mission->name);
    }

    public function test_the_admin_moves_a_crew_member_to_another_mission(): void
    {
        $astronaut = $this->astronaut();
        $other = $this->mission();

        $this->patchJson("/api/v1/astronauts/{$astronaut->id}", [
            'mission_id' => $other->id,
            'name' => $astronaut->name,
            'crew_role' => 'Comandante',
        ], $this->asAdmin())
            ->assertOk()
            ->assertJsonPath('data.mission_id', $other->id)
            ->assertJsonPath('data.crew_role', 'Comandante');
    }

    public function test_unknown_mission_or_crew_member(): void
    {
        $headers = $this->asAdmin();

        $this->postJson('/api/v1/astronauts', ['mission_id' => 999, 'name' => 'Ana', 'crew_role' => 'Piloto'], $headers)
            ->assertNotFound()
            ->assertJsonPath('error_code', 'ENV_MISSION_NOT_FOUND');

        $this->patchJson('/api/v1/astronauts/999', ['mission_id' => $this->mission()->id, 'name' => 'Ana', 'crew_role' => 'Piloto'], $headers)
            ->assertNotFound()
            ->assertJsonPath('error_code', 'ENV_ASTRONAUT_NOT_FOUND');
    }

    public function test_the_format_is_validated(): void
    {
        $this->postJson('/api/v1/astronauts', [], $this->asAdmin())
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['mission_id', 'name', 'crew_role']);
    }

    public function test_only_the_admin_manages_the_crew(): void
    {
        $this->postJson('/api/v1/astronauts', [], $this->bearer($this->tokenFor($this->userWithRole(Role::CrewMedicalOfficer))))
            ->assertForbidden();
    }
}
