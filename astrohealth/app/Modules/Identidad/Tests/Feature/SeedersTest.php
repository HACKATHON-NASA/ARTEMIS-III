<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Tests\Feature;

use App\Models\User;
use App\Modules\Entorno\Infrastructure\Models\Mission;
use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Infrastructure\Models\Device;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * php artisan db:seed: misión, un usuario por rol y el dispositivo del simulador.
 */
final class SeedersTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_demo_data_has_one_user_per_role_linked_to_the_crew(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('missions', 3);
        $this->assertDatabaseCount('astronauts', 6);
        $this->assertSame(
            ['admin' => 1, 'astronaut' => 3, 'crew_medical_officer' => 1, 'flight_surgeon' => 1],
            User::query()->get()->countBy(fn (User $user): string => $user->role === null ? 'sin rol' : $user->role->value)->sortKeys()->all(),
        );

        $cmo = User::query()->where('email', 'cmo@astrohealth.test')->firstOrFail();
        $this->assertSame(Role::CrewMedicalOfficer, $cmo->role);
        $this->assertNotNull($cmo->astronaut_id);

        $this->assertNull(User::query()->where('email', 'tierra@astrohealth.test')->firstOrFail()->astronaut_id);
        $this->assertSame(1, Device::query()->where('client_id', 'sim-test')->count());
    }

    public function test_seeding_twice_does_not_duplicate_anything(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('missions', 3);
        $this->assertDatabaseCount('astronauts', 6);
        $this->assertDatabaseCount('users', 6);
        $this->assertDatabaseCount('devices', 1);
    }

    public function test_seeded_users_and_simulator_can_authenticate(): void
    {
        $this->seed(DatabaseSeeder::class);

        // En MySQL los ids no vuelven a 1 entre pruebas: se compara con el id real.
        $missionId = Mission::query()->where('name', 'Ares-1')->value('id');

        $this->postJson('/api/v1/auth/login', ['email' => 'astro2@astrohealth.test', 'password' => 'password'])
            ->assertOk()
            ->assertJsonPath('data.actor.role', 'astronaut')
            ->assertJsonPath('data.actor.mission_id', $missionId);

        $this->postJson('/api/v1/auth/device-token', [
            'client_id' => 'sim-test',
            'client_secret' => 'secreto-de-prueba-del-simulador-0123456789abcdef',
        ])->assertOk()->assertJsonPath('data.actor.role', 'device');
    }
}
