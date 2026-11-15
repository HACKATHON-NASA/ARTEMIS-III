<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Los mensajes de la API salen en el idioma de Accept-Language: inglés o
 * español (sin traducción, el texto original en español).
 */
final class LocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_errors_come_in_english_when_asked(): void
    {
        $this->getJson('/api/v1/auth/me', ['Accept-Language' => 'en-US,en;q=0.9'])
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Sign in to continue.');
    }

    public function test_validation_messages_are_translated_too(): void
    {
        $this->postJson('/api/v1/auth/login', [], ['Accept-Language' => 'en'])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'The data is not valid.')
            ->assertJsonPath('errors.email.0', 'The email is required.');
    }

    public function test_spanish_stays_spanish(): void
    {
        $this->getJson('/api/v1/auth/me', ['Accept-Language' => 'es'])
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Inicia sesión para continuar.');
    }
}
