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
    public function test_opening_the_app_goes_straight_to_the_login_screen(): void
    {
        $this->get('/')->assertRedirect('/acceso');
    }

    public function test_the_login_page_is_the_identidad_module_principal(): void
    {
        $this->get('/acceso')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component('Identidad/Login'));
    }

    public function test_admin_opens_the_crew_health_panel(): void
    {
        $this->get('/admin')->assertRedirect('/salud');
    }

    public function test_the_users_page_is_the_identidad_module_principal(): void
    {
        // La página es una cáscara: el permiso users.manage lo comprueban la
        // pantalla (al cargar) y, cuando existan, /api/v1/users y /devices.
        $this->get('/admin/usuarios')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component('Identidad/Admin'));
    }

    public function test_the_missions_page_is_the_entorno_module_principal(): void
    {
        // Cáscara: el permiso missions.manage lo comprueba la pantalla al cargar.
        $this->get('/admin/misiones')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component('Entorno/Missions'));
    }

    public function test_the_health_panel_and_crew_pages_exist(): void
    {
        $this->get('/salud')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component('Dashboard/HealthPanel'));

        $this->get('/tripulacion')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component('Entorno/Crew'));
    }

    public function test_the_astronaut_home_is_the_cuerpo_module_principal(): void
    {
        $this->get('/inicio')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component('Cuerpo/Home'));
    }

    public function test_the_record_page_is_the_registro_module_principal(): void
    {
        $this->get('/registrar')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component('Registro/Record'));
    }

    public function test_the_health_guide_is_the_evaluacion_module_principal(): void
    {
        $this->get('/guia')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component('Evaluacion/Guide'));

        $this->get('/guia/psicologica')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component('Evaluacion/PsychGuide'));

        $this->get('/guia/resultados')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component('Evaluacion/PsychResults'));
    }

    public function test_my_evolution_is_the_dashboard_module_principal(): void
    {
        $this->get('/evolucion')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component('Dashboard/MyEvolution'));
    }

    public function test_the_alerts_center_is_the_alertas_module_principal(): void
    {
        $this->get('/alertas')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component('Alertas/Alerts'));
    }

    public function test_app_pages_do_not_depend_on_a_server_session(): void
    {
        // Sin cookie de sesión no hay redirección del servidor: la página pide
        // la sesión a la API al cargar (useAuthStore.ensureSession).
        $this->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component('Dashboard/Dashboard')->missing('auth'));
    }

    public function test_the_fortify_routes_are_gone(): void
    {
        foreach (['/login', '/register', '/forgot-password', '/settings/profile', '/two-factor-challenge'] as $uri) {
            $this->get($uri)->assertNotFound();
        }
    }
}
