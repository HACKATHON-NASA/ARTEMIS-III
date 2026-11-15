<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Tests\Feature;

use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Tests\Support\InteractsWithJwt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * /api/v1/auth/quick-access: se entra eligiendo a una persona, sin
 * contraseña, con la misma sesión que el login. Se puede apagar.
 */
final class QuickAccessTest extends TestCase
{
    use InteractsWithJwt;
    use RefreshDatabase;

    public function test_it_lists_active_people_crew_first_without_emails(): void
    {
        $admin = $this->userWithRole(Role::Admin);
        $elena = $this->userWithRole(Role::Astronaut, $this->astronaut());
        $this->userWithRole(Role::Astronaut, $this->astronaut())->forceFill(['active' => false])->save();

        $list = $this->getJson('/api/v1/auth/quick-access')->assertOk()->json('data');

        $this->assertSame([$elena->id, $admin->id], array_column($list, 'id'));
        $this->assertSame('astronaut', $list[0]['role']);
        $this->assertArrayNotHasKey('email', $list[0]);
    }

    public function test_choosing_someone_starts_their_session(): void
    {
        $astronaut = $this->astronaut();
        $elena = $this->userWithRole(Role::Astronaut, $astronaut);

        $token = $this->postJson('/api/v1/auth/quick-access', ['user_id' => $elena->id])
            ->assertOk()
            ->assertCookie('refresh_token', null, false)
            ->assertJsonPath('data.actor.role', 'astronaut')
            ->json('data.access_token');

        $this->getJson('/api/v1/auth/me', $this->bearer($token))
            ->assertOk()
            ->assertJsonPath('data.astronaut_id', $astronaut->id);
        $this->assertNotNull($elena->fresh()?->last_login_at);
    }

    public function test_an_inactive_or_unknown_person_cannot_be_chosen(): void
    {
        $inactive = $this->userWithRole(Role::FlightSurgeon);
        $inactive->forceFill(['active' => false])->save();

        $this->postJson('/api/v1/auth/quick-access', ['user_id' => $inactive->id])
            ->assertNotFound()->assertJsonPath('error_code', 'AUTH_QUICK_ACCESS_OFF');
        $this->postJson('/api/v1/auth/quick-access', ['user_id' => 999])->assertNotFound();
        $this->postJson('/api/v1/auth/quick-access', [])->assertUnprocessable();
    }

    public function test_it_can_be_turned_off(): void
    {
        config(['identidad.quick_access' => false]);
        $admin = $this->userWithRole(Role::Admin);

        $this->getJson('/api/v1/auth/quick-access')->assertNotFound();
        $this->postJson('/api/v1/auth/quick-access', ['user_id' => $admin->id])->assertNotFound();
    }
}
