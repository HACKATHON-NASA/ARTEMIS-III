<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Tests\Feature;

use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Tests\Support\InteractsWithJwt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * auth:api (JwtGuard) en GET /api/v1/auth/me.
 */
final class GuardTest extends TestCase
{
    use InteractsWithJwt;
    use RefreshDatabase;

    public function test_without_token_it_is_401_unauthenticated(): void
    {
        $this->getJson('/api/v1/auth/me')
            ->assertUnauthorized()
            ->assertJsonPath('error_code', 'UNAUTHENTICATED');
    }

    public function test_with_a_valid_token_it_returns_the_actor_and_permissions(): void
    {
        $user = $this->userWithRole(Role::Astronaut);

        $this->getJson('/api/v1/auth/me', $this->bearer($this->tokenFor($user)))
            ->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.role', 'astronaut')
            ->assertJsonPath('data.permissions', [
                'crew.read', 'health.read.own', 'measurements.write.own', 'alerts.read',
                'alerts.send', 'reminders.manage.own', 'gamification.read',
            ]);
    }

    public function test_a_malformed_token_is_401_token_invalid(): void
    {
        $this->getJson('/api/v1/auth/me', $this->bearer('abc.def.ghi'))
            ->assertUnauthorized()
            ->assertJsonPath('error_code', 'AUTH_TOKEN_INVALID');
    }

    public function test_an_expired_token_is_401_token_expired(): void
    {
        $token = $this->tokenFor($this->userWithRole(Role::Astronaut));

        $this->travel(16)->minutes();

        $this->getJson('/api/v1/auth/me', $this->bearer($token))
            ->assertUnauthorized()
            ->assertJsonPath('error_code', 'AUTH_TOKEN_EXPIRED')
            ->assertJsonPath('message', 'La sesión expiró.');
    }

    public function test_a_user_deactivated_after_login_is_rejected_immediately(): void
    {
        $user = $this->userWithRole(Role::Astronaut);
        $token = $this->tokenFor($user);

        $user->forceFill(['active' => false])->save();

        $this->getJson('/api/v1/auth/me', $this->bearer($token))
            ->assertUnauthorized()
            ->assertJsonPath('error_code', 'AUTH_TOKEN_INVALID');
    }

    public function test_a_role_change_applies_immediately_with_the_same_token(): void
    {
        $user = $this->userWithRole(Role::FlightSurgeon);
        $token = $this->tokenFor($user);

        $user->forceFill(['role' => Role::Admin])->save();

        $this->getJson('/api/v1/auth/me', $this->bearer($token))
            ->assertOk()
            ->assertJsonPath('data.role', 'admin');
    }

    public function test_a_token_of_a_deleted_user_is_rejected(): void
    {
        $user = $this->userWithRole(Role::Admin);
        $token = $this->tokenFor($user);
        $user->delete();

        $this->getJson('/api/v1/auth/me', $this->bearer($token))
            ->assertUnauthorized()
            ->assertJsonPath('error_code', 'AUTH_TOKEN_INVALID');
    }

    public function test_each_request_resolves_its_own_actor(): void
    {
        $astronaut = $this->userWithRole(Role::Astronaut);
        $admin = $this->userWithRole(Role::Admin);

        $this->getJson('/api/v1/auth/me', $this->bearer($this->tokenFor($astronaut)))->assertJsonPath('data.id', $astronaut->id);
        $this->getJson('/api/v1/auth/me', $this->bearer($this->tokenFor($admin)))->assertJsonPath('data.id', $admin->id);
        $this->getJson('/api/v1/auth/me')->assertUnauthorized();
    }
}
