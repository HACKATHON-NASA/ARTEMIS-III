<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Tests\Feature;

use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

/**
 * Páginas de acceso (Inertia). Son solo la cáscara: la sesión va por la API con JWT.
 * Con ensure_pages_exist, assertInertia también comprueba que el .vue exista.
 */
final class PagesTest extends TestCase
{
    public function test_the_login_page_is_served_at_acceso(): void
    {
        $this->get('/acceso')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component('Login'));
    }

    public function test_app_pages_do_not_depend_on_a_server_session(): void
    {
        // Sin cookie de sesión no hay redirección del servidor: la página pide
        // la sesión a la API al cargar (useAuthStore.ensureSession).
        $this->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component('Dashboard')->missing('auth'));
    }

    public function test_the_fortify_routes_are_gone(): void
    {
        foreach (['/login', '/register', '/forgot-password', '/settings/profile', '/two-factor-challenge'] as $uri) {
            $this->get($uri)->assertNotFound();
        }
    }
}
