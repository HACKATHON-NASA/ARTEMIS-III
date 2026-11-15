<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Tests\Feature;

use App\Models\User;
use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Tests\Support\InteractsWithJwt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * POST /api/v1/auth/login
 */
final class LoginTest extends TestCase
{
    use InteractsWithJwt;
    use RefreshDatabase;

    public function test_a_crew_member_logs_in_and_gets_token_profile_and_refresh_cookie(): void
    {
        $user = $this->userWithRole(Role::CrewMedicalOfficer);

        $response = $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => self::PASSWORD]);

        $response->assertOk()
            ->assertJsonPath('message', 'ok')
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonPath('data.expires_in', 900)
            ->assertJsonPath('data.actor.type', 'user')
            ->assertJsonPath('data.actor.id', $user->id)
            ->assertJsonPath('data.actor.role', 'crew_medical_officer')
            ->assertJsonPath('data.actor.role_label', 'Médico de la tripulación')
            ->assertJsonPath('data.actor.location', 'space')
            ->assertJsonPath('data.actor.astronaut_id', $user->astronaut_id)
            ->assertJsonStructure(['data' => ['access_token', 'actor' => ['mission_id', 'permissions']]])
            ->assertJsonMissingPath('data.refresh_token');

        $this->assertContains('measurements.write.crew', (array) $response->json('data.actor.permissions'));

        $cookie = $response->getCookie('refresh_token', false);
        $this->assertNotNull($cookie);
        $this->assertTrue($cookie->isHttpOnly());
        $this->assertSame('strict', $cookie->getSameSite());
        $this->assertSame('/api/v1/auth', $cookie->getPath());
        $this->assertSame(64, strlen((string) $cookie->getValue()));

        $this->assertDatabaseCount('refresh_tokens', 1);
        $this->assertDatabaseMissing('refresh_tokens', ['token_hash' => $cookie->getValue()]);
        $this->assertNotNull($user->fresh()?->last_login_at);
    }

    public function test_an_earth_role_logs_in_without_astronaut(): void
    {
        $user = $this->userWithRole(Role::FlightSurgeon);

        $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => self::PASSWORD])
            ->assertOk()
            ->assertJsonPath('data.actor.location', 'earth')
            ->assertJsonPath('data.actor.astronaut_id', null)
            ->assertJsonPath('data.actor.mission_id', null);
    }

    public function test_wrong_password_and_unknown_email_get_the_exact_same_answer(): void
    {
        $user = $this->userWithRole(Role::Astronaut);

        $wrongPassword = $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'no-es']);
        $unknownEmail = $this->postJson('/api/v1/auth/login', ['email' => 'nadie@astrohealth.test', 'password' => 'no-es']);

        $expected = ['data' => null, 'message' => 'Credenciales inválidas.', 'error_code' => 'AUTH_INVALID_CREDENTIALS', 'errors' => []];
        $wrongPassword->assertUnauthorized()->assertExactJson($expected);
        $unknownEmail->assertUnauthorized()->assertExactJson($expected);
        $this->assertDatabaseCount('refresh_tokens', 0);
    }

    public function test_an_inactive_user_cannot_log_in(): void
    {
        $user = $this->userWithRole(Role::Astronaut);
        $user->forceFill(['active' => false])->save();

        $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => self::PASSWORD])
            ->assertForbidden()
            ->assertJsonPath('error_code', 'AUTH_USER_INACTIVE');
    }

    public function test_a_user_without_role_cannot_use_the_api(): void
    {
        $user = User::factory()->create(['password' => self::PASSWORD]);

        $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => self::PASSWORD])
            ->assertForbidden()
            ->assertJsonPath('error_code', 'AUTH_USER_WITHOUT_ROLE');
    }

    public function test_validation_errors_are_in_spanish_and_by_field(): void
    {
        $this->postJson('/api/v1/auth/login', ['email' => 'no-es-correo'])
            ->assertUnprocessable()
            ->assertJsonPath('error_code', 'VALIDATION_FAILED')
            ->assertJsonPath('errors.email.0', 'El correo no tiene un formato válido.')
            ->assertJsonPath('errors.password.0', 'La contraseña es obligatoria.');
    }

    public function test_the_sixth_attempt_in_a_minute_is_throttled(): void
    {
        $user = $this->userWithRole(Role::Astronaut);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'mala'])->assertUnauthorized();
        }

        $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => self::PASSWORD])
            ->assertStatus(429)
            ->assertJsonPath('error_code', 'TOO_MANY_REQUESTS')
            ->assertHeader('Retry-After');
    }
}
