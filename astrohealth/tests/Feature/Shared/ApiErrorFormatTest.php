<?php

declare(strict_types=1);

namespace Tests\Feature\Shared;

use App\Shared\Excepciones\BusinessError;
use App\Shared\Http\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use RuntimeException;
use Tests\TestCase;

/**
 * Toda respuesta de /api/* sale con el formato único:
 * éxito {data, message} · error {data: null, message, error_code, errors}.
 */
final class ApiErrorFormatTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware('api')->prefix('api/v1/_test')->group(function (): void {
            Route::get('ok', fn () => ApiResponse::ok(['a' => 1]));
            Route::get('created', fn () => ApiResponse::created(['id' => 7]));
            Route::get('empty', fn () => ApiResponse::noContent());
            Route::post('validation', function (Request $request): void {
                $request->validate(['email' => ['required', 'email']]);
            });
            Route::get('business', fn () => throw new TestRuleBroken);
            Route::get('business-field', fn () => throw new TestFieldRuleBroken);
            Route::get('unauthenticated', fn () => throw new AuthenticationException);
            Route::get('forbidden', fn () => throw new AuthorizationException);
            Route::get('boom', fn () => throw new RuntimeException('secreto interno de la base'));
        });
    }

    public function test_ok_response_has_data_and_message(): void
    {
        $this->getJson('/api/v1/_test/ok')
            ->assertOk()
            ->assertExactJson(['data' => ['a' => 1], 'message' => 'ok']);
    }

    public function test_created_response_is_201(): void
    {
        $this->getJson('/api/v1/_test/created')
            ->assertCreated()
            ->assertExactJson(['data' => ['id' => 7], 'message' => 'ok']);
    }

    public function test_no_content_response_is_204_without_body(): void
    {
        $this->getJson('/api/v1/_test/empty')->assertNoContent();
    }

    public function test_unknown_route_is_404_even_without_accept_header(): void
    {
        $response = $this->get('/api/v1/no-existe');

        $response->assertNotFound()
            ->assertHeader('Content-Type', 'application/json')
            ->assertExactJson([
                'data' => null,
                'message' => 'El recurso no existe.',
                'error_code' => 'NOT_FOUND',
                'errors' => [],
            ]);
        $this->assertStringContainsString('"errors":{}', (string) $response->getContent());
    }

    public function test_validation_error_is_422_with_errors_by_field(): void
    {
        $this->postJson('/api/v1/_test/validation', ['email' => 'no-es-correo'])
            ->assertUnprocessable()
            ->assertJsonPath('data', null)
            ->assertJsonPath('error_code', 'VALIDATION_FAILED')
            ->assertJsonPath('message', 'Los datos no son válidos.')
            ->assertJsonStructure(['errors' => ['email']]);
    }

    public function test_business_error_uses_its_own_code_and_status(): void
    {
        $this->getJson('/api/v1/_test/business')
            ->assertStatus(409)
            ->assertExactJson([
                'data' => null,
                'message' => 'La regla de prueba no se cumple.',
                'error_code' => 'TEST_RULE_BROKEN',
                'errors' => [],
            ]);
    }

    public function test_business_error_with_field_goes_into_errors(): void
    {
        $this->getJson('/api/v1/_test/business-field')
            ->assertUnprocessable()
            ->assertJsonPath('error_code', 'TEST_FIELD_RULE')
            ->assertJsonPath('errors.value.0', 'El valor no sirve.');
    }

    public function test_unauthenticated_is_401(): void
    {
        $this->getJson('/api/v1/_test/unauthenticated')
            ->assertUnauthorized()
            ->assertJsonPath('error_code', 'UNAUTHENTICATED');
    }

    public function test_forbidden_is_403(): void
    {
        $this->getJson('/api/v1/_test/forbidden')
            ->assertForbidden()
            ->assertJsonPath('error_code', 'FORBIDDEN');
    }

    public function test_wrong_method_is_405(): void
    {
        $this->postJson('/api/v1/_test/ok')
            ->assertStatus(405)
            ->assertJsonPath('error_code', 'METHOD_NOT_ALLOWED');
    }

    public function test_unexpected_error_is_500_and_hides_internals_in_production(): void
    {
        config(['app.debug' => false]);

        $response = $this->getJson('/api/v1/_test/boom');

        $response->assertStatus(500)
            ->assertExactJson([
                'data' => null,
                'message' => 'Ocurrió un error inesperado.',
                'error_code' => 'INTERNAL_ERROR',
                'errors' => [],
            ]);
        $this->assertStringNotContainsString('secreto interno', (string) $response->getContent());
    }

    public function test_unexpected_error_shows_debug_only_in_local(): void
    {
        config(['app.debug' => true]);

        $this->getJson('/api/v1/_test/boom')
            ->assertStatus(500)
            ->assertJsonPath('debug.exception', RuntimeException::class)
            ->assertJsonPath('debug.message', 'secreto interno de la base');
    }

    public function test_web_routes_keep_their_normal_behaviour(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }
}

final class TestRuleBroken extends BusinessError
{
    public function __construct()
    {
        parent::__construct('La regla de prueba no se cumple.');
    }

    public function errorCode(): string
    {
        return 'TEST_RULE_BROKEN';
    }

    public function httpStatus(): int
    {
        return 409;
    }
}

final class TestFieldRuleBroken extends BusinessError
{
    public function __construct()
    {
        parent::__construct('El valor no sirve.');
    }

    public function errorCode(): string
    {
        return 'TEST_FIELD_RULE';
    }

    public function field(): ?string
    {
        return 'value';
    }
}
