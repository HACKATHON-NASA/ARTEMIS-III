<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Tests\Feature;

use App\Models\User;
use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Infrastructure\Models\RefreshToken;
use App\Modules\Identidad\Tests\Support\InteractsWithJwt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * /api/v1/users: el administrador gestiona quién tiene acceso.
 */
final class UsersApiTest extends TestCase
{
    use InteractsWithJwt;
    use RefreshDatabase;

    /** @return array<string, string> */
    private function asAdmin(?User $admin = null): array
    {
        return $this->bearer($this->tokenFor($admin ?? $this->userWithRole(Role::Admin)));
    }

    public function test_the_admin_lists_users_with_crew_and_mission_names(): void
    {
        $mission = $this->mission();
        $astronaut = $this->astronaut($mission);
        $user = $this->userWithRole(Role::Astronaut, $astronaut);

        $this->getJson('/api/v1/users', $this->asAdmin())
            ->assertOk()
            ->assertJsonPath('message', 'ok')
            ->assertJsonFragment([
                'id' => $user->id,
                'role' => 'astronaut',
                'astronaut_id' => $astronaut->id,
                'astronaut_name' => $astronaut->name,
                'crew_role' => $astronaut->crew_role,
                'mission_id' => $mission->id,
                'mission_name' => $mission->name,
                'active' => true,
            ]);
    }

    public function test_only_the_admin_can_manage_users(): void
    {
        $this->getJson('/api/v1/users')->assertUnauthorized();

        foreach ([Role::FlightSurgeon, Role::CrewMedicalOfficer, Role::Astronaut] as $role) {
            $this->getJson('/api/v1/users', $this->bearer($this->tokenFor($this->userWithRole($role))))
                ->assertForbidden()
                ->assertJsonPath('error_code', 'FORBIDDEN');
        }
    }

    public function test_the_admin_creates_a_flight_surgeon_following_a_mission(): void
    {
        $mission = $this->mission();

        $this->postJson('/api/v1/users', [
            'name' => 'Dr. Tomás Rivas',
            'email' => ' TRivas@AstroHealth.test ',
            'role' => 'flight_surgeon',
            'mission_id' => $mission->id,
        ], $this->asAdmin())
            ->assertCreated()
            ->assertJsonPath('data.email', 'trivas@astrohealth.test')
            ->assertJsonPath('data.mission_id', $mission->id)
            ->assertJsonPath('data.mission_name', $mission->name)
            ->assertJsonPath('data.last_login_at', null);

        $created = User::query()->where('email', 'trivas@astrohealth.test')->firstOrFail();
        $this->assertSame($mission->id, $created->mission_id);
        $this->assertNotSame('', $created->password, 'Se crea con una contraseña aleatoria, hasheada.');
    }

    public function test_the_flight_surgeon_token_carries_their_mission(): void
    {
        $mission = $this->mission();
        $doctor = User::factory()->create(['role' => Role::FlightSurgeon, 'mission_id' => $mission->id]);

        $this->getJson('/api/v1/auth/me', $this->bearer($this->tokenFor($doctor)))
            ->assertOk()
            ->assertJsonPath('data.mission_id', $mission->id);
    }

    public function test_the_admin_creates_an_astronaut_linked_to_their_crew_member(): void
    {
        $astronaut = $this->astronaut();

        $this->postJson('/api/v1/users', [
            'name' => 'Nadia Petrova',
            'email' => 'npetrova@astrohealth.test',
            'role' => 'astronaut',
            'astronaut_id' => $astronaut->id,
            'mission_id' => 999,
        ], $this->asAdmin())
            ->assertCreated()
            ->assertJsonPath('data.astronaut_id', $astronaut->id)
            ->assertJsonPath('data.mission_id', $astronaut->mission_id);

        $this->assertNull(User::query()->where('email', 'npetrova@astrohealth.test')->value('mission_id'), 'La misión del tripulante sale de su astronauta.');
    }

    public function test_business_rules_answer_422_on_the_right_field(): void
    {
        $admin = $this->userWithRole(Role::Admin);
        $taken = $this->userWithRole(Role::Astronaut);
        $headers = $this->asAdmin($admin);
        $base = ['name' => 'Persona', 'email' => 'nueva@astrohealth.test'];

        $cases = [
            'AUTH_EMAIL_TAKEN' => ['email', [...$base, 'email' => $admin->email, 'role' => 'admin']],
            'AUTH_MISSION_REQUIRED' => ['mission_id', [...$base, 'role' => 'flight_surgeon']],
            'AUTH_UNKNOWN_MISSION' => ['mission_id', [...$base, 'role' => 'flight_surgeon', 'mission_id' => 999]],
            'AUTH_INVALID_ROLE_ASSIGNMENT' => ['role', [...$base, 'role' => 'astronaut']],
            'AUTH_UNKNOWN_ASTRONAUT' => ['astronaut_id', [...$base, 'role' => 'astronaut', 'astronaut_id' => 999]],
            'AUTH_ASTRONAUT_TAKEN' => ['astronaut_id', [...$base, 'role' => 'astronaut', 'astronaut_id' => $taken->astronaut_id]],
        ];

        foreach ($cases as $code => [$field, $body]) {
            $this->postJson('/api/v1/users', $body, $headers)
                ->assertUnprocessable()
                ->assertJsonPath('error_code', $code)
                ->assertJsonValidationErrors([$field]);
        }
    }

    public function test_the_format_is_validated(): void
    {
        $this->postJson('/api/v1/users', ['email' => 'no-es-correo', 'role' => 'device'], $this->asAdmin())
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'role']);
    }

    public function test_the_admin_updates_a_user_but_not_the_email(): void
    {
        $user = $this->userWithRole(Role::Admin);
        $mission = $this->mission();

        $this->patchJson("/api/v1/users/{$user->id}", [
            'name' => 'Nuevo nombre',
            'email' => 'otro@astrohealth.test',
            'role' => 'flight_surgeon',
            'mission_id' => $mission->id,
        ], $this->asAdmin())
            ->assertOk()
            ->assertJsonPath('data.name', 'Nuevo nombre')
            ->assertJsonPath('data.role', 'flight_surgeon')
            ->assertJsonPath('data.email', $user->email);
    }

    public function test_an_astronaut_keeps_their_own_crew_member_when_edited(): void
    {
        $user = $this->userWithRole(Role::Astronaut);

        $this->patchJson("/api/v1/users/{$user->id}", [
            'name' => 'Con otro nombre',
            'role' => 'crew_medical_officer',
            'astronaut_id' => $user->astronaut_id,
        ], $this->asAdmin())
            ->assertOk()
            ->assertJsonPath('data.role', 'crew_medical_officer');
    }

    public function test_an_admin_cannot_lock_themself_out(): void
    {
        $admin = $this->userWithRole(Role::Admin);
        $headers = $this->asAdmin($admin);

        $this->patchJson("/api/v1/users/{$admin->id}", ['name' => 'Yo', 'role' => 'flight_surgeon', 'mission_id' => $this->mission()->id], $headers)
            ->assertStatus(409)
            ->assertJsonPath('error_code', 'AUTH_OWN_ACCESS_LOCKED');

        $this->patchJson("/api/v1/users/{$admin->id}/active", ['active' => false], $headers)
            ->assertStatus(409)
            ->assertJsonPath('error_code', 'AUTH_OWN_ACCESS_LOCKED');
    }

    public function test_deactivating_closes_every_session_and_blocks_the_api(): void
    {
        $user = $this->userWithRole(Role::Astronaut);
        $login = $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => self::PASSWORD])->assertOk();
        $token = (string) $login->json('data.access_token');

        $this->patchJson("/api/v1/users/{$user->id}/active", ['active' => false], $this->asAdmin())
            ->assertOk()
            ->assertJsonPath('data.active', false);

        $this->assertSame(0, RefreshToken::query()->where('user_id', $user->id)->whereNull('revoked_at')->count());
        $this->getJson('/api/v1/auth/me', $this->bearer($token))->assertUnauthorized();
    }

    public function test_an_unknown_user_is_404(): void
    {
        $this->patchJson('/api/v1/users/999/active', ['active' => true], $this->asAdmin())
            ->assertNotFound()
            ->assertJsonPath('error_code', 'AUTH_USER_NOT_FOUND');
    }
}
