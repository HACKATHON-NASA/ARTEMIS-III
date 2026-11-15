<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Tests\Feature;

use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Tests\Support\InteractsWithJwt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * POST /api/v1/auth/refresh y /logout: rotación, detección de reuso y cierre de sesión.
 */
final class RefreshAndLogoutTest extends TestCase
{
    use InteractsWithJwt;
    use RefreshDatabase;

    /**
     * @return array{access: string, refresh: string}
     */
    private function login(): array
    {
        $user = $this->userWithRole(Role::Astronaut);
        $response = $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => self::PASSWORD]);

        return ['access' => (string) $response->json('data.access_token'), 'refresh' => $this->refreshCookie($response)];
    }

    /**
     * @param  TestResponse<Response>  $response
     */
    private function refreshCookie(TestResponse $response): string
    {
        return (string) $response->getCookie('refresh_token', false)?->getValue();
    }

    /**
     * @return TestResponse<Response>
     */
    private function refresh(string $refreshToken): TestResponse
    {
        return $this->withRefreshCookie($refreshToken)->postJson('/api/v1/auth/refresh');
    }

    /**
     * Como el navegador con credentials: 'include': sin withCredentials()
     * Laravel no envía cookies en las peticiones JSON de prueba.
     */
    private function withRefreshCookie(string $refreshToken): static
    {
        return $this->withCredentials()->withUnencryptedCookie('refresh_token', $refreshToken);
    }

    public function test_refresh_rotates_the_token_and_returns_a_new_access_token(): void
    {
        $session = $this->login();

        $response = $this->refresh($session['refresh']);

        $response->assertOk()->assertJsonPath('data.token_type', 'Bearer');
        $newRefresh = $this->refreshCookie($response);
        $this->assertNotSame($session['refresh'], $newRefresh);
        $this->assertNotSame($session['access'], $response->json('data.access_token'));

        $this->refresh($newRefresh)->assertOk();
    }

    public function test_reusing_an_old_refresh_token_revokes_the_whole_family(): void
    {
        $session = $this->login();
        $newRefresh = $this->refreshCookie($this->refresh($session['refresh']));

        $this->refresh($session['refresh'])
            ->assertUnauthorized()
            ->assertJsonPath('error_code', 'AUTH_REFRESH_REUSED');

        // El refresh "bueno" también quedó revocado: el ladrón y la víctima vuelven a iniciar sesión.
        $this->refresh($newRefresh)
            ->assertUnauthorized()
            ->assertJsonPath('error_code', 'AUTH_REFRESH_INVALID');
    }

    public function test_refresh_without_cookie_is_401(): void
    {
        $this->postJson('/api/v1/auth/refresh')
            ->assertUnauthorized()
            ->assertJsonPath('error_code', 'AUTH_REFRESH_INVALID');
    }

    public function test_an_expired_refresh_token_is_rejected(): void
    {
        $session = $this->login();

        $this->travel(7)->days();
        $this->travel(1)->minutes();

        $this->refresh($session['refresh'])
            ->assertUnauthorized()
            ->assertJsonPath('error_code', 'AUTH_REFRESH_INVALID');
    }

    public function test_refresh_from_a_foreign_origin_is_refused(): void
    {
        $session = $this->login();

        $this->withRefreshCookie($session['refresh'])
            ->withHeader('Origin', 'https://sitio-malo.test')
            ->postJson('/api/v1/auth/refresh')
            ->assertForbidden()
            ->assertJsonPath('error_code', 'AUTH_ORIGIN_NOT_ALLOWED');
    }

    public function test_refresh_from_the_app_origin_is_accepted(): void
    {
        $session = $this->login();

        $this->withRefreshCookie($session['refresh'])
            ->withHeader('Origin', (string) config('app.url'))
            ->postJson('/api/v1/auth/refresh')
            ->assertOk();
    }

    public function test_logout_kills_both_the_access_token_and_the_refresh_token(): void
    {
        $session = $this->login();

        $logout = $this->withRefreshCookie($session['refresh'])
            ->postJson('/api/v1/auth/logout', [], $this->bearer($session['access']));

        $logout->assertNoContent();
        $this->assertSame('', (string) $logout->getCookie('refresh_token', false)?->getValue());

        $this->getJson('/api/v1/auth/me', $this->bearer($session['access']))
            ->assertUnauthorized()
            ->assertJsonPath('error_code', 'AUTH_TOKEN_INVALID');

        $this->refresh($session['refresh'])->assertUnauthorized();
    }

    public function test_a_logged_out_token_stays_dead_during_the_clock_leeway(): void
    {
        $session = $this->login();

        $this->postJson('/api/v1/auth/logout', [], $this->bearer($session['access']))->assertNoContent();

        // Pasó el exp (15 min), pero sigue dentro de la tolerancia de relojes (30 s):
        // el verificador aún aceptaría la firma, así que la revocación debe seguir viva.
        $this->travel(15)->minutes();
        $this->travel(10)->seconds();

        $this->getJson('/api/v1/auth/me', $this->bearer($session['access']))
            ->assertUnauthorized()
            ->assertJsonPath('error_code', 'AUTH_TOKEN_INVALID');
    }

    public function test_logout_requires_a_token(): void
    {
        $this->postJson('/api/v1/auth/logout')->assertUnauthorized();
    }
}
