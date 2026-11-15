<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Tests\Feature;

use App\Models\User;
use App\Modules\Evaluacion\Database\Seeders\ThresholdSeeder;
use App\Modules\Evaluacion\Domain\Eventos\ThresholdUpdated;
use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Tests\Support\InteractsWithJwt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * /api/v1/thresholds: la Guía de salud lee y edita los umbrales.
 */
final class ThresholdsApiTest extends TestCase
{
    use InteractsWithJwt;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ThresholdSeeder::class);
    }

    /** @return array<string, string> */
    private function as(User $user): array
    {
        return $this->bearer($this->tokenFor($user));
    }

    /**
     * El umbral de un indicador dentro de la respuesta.
     *
     * @return array<string, mixed>
     */
    private function indicator(mixed $data, string $code): array
    {
        $this->assertIsArray($data);

        foreach ($data as $row) {
            if (is_array($row) && ($row['indicator'] ?? null) === $code) {
                return $row;
            }
        }

        $this->fail("No llegó el umbral de {$code}.");
    }

    public function test_anyone_reads_the_ten_thresholds_of_an_environment(): void
    {
        $response = $this->getJson('/api/v1/thresholds?environment=mars', $this->as($this->userWithRole(Role::Astronaut)))
            ->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('data.0.indicator', 'hr')
            ->assertJsonPath('data.0.green_min', 60)
            ->assertJsonPath('data.0.yellow_max', 120);

        $radiation = $this->indicator($response->json('data'), 'radiation');
        $this->assertEquals(['green_max' => 0.6, 'yellow_max' => 1.2], ['green_max' => $radiation['green_max'], 'yellow_max' => $radiation['yellow_max']]);
    }

    public function test_radiation_changes_with_the_environment(): void
    {
        $response = $this->getJson('/api/v1/thresholds?environment=transit', $this->as($this->userWithRole(Role::Admin)))->assertOk();

        $radiation = $this->indicator($response->json('data'), 'radiation');
        $this->assertEquals(1.8, $radiation['green_max']);
        $this->assertEquals(3.6, $radiation['yellow_max']);
    }

    public function test_the_environment_is_required_and_valid(): void
    {
        $headers = $this->as($this->userWithRole(Role::Admin));

        $this->getJson('/api/v1/thresholds', $headers)->assertUnprocessable()->assertJsonValidationErrors(['environment']);
        $this->getJson('/api/v1/thresholds?environment=venus', $headers)->assertUnprocessable()->assertJsonValidationErrors(['environment']);
    }

    public function test_a_flight_surgeon_changes_a_threshold_and_it_is_announced(): void
    {
        Event::fake([ThresholdUpdated::class]);
        $surgeon = User::factory()->create(['role' => Role::FlightSurgeon]);

        $this->putJson('/api/v1/thresholds/sleep?environment=mars', [
            'green_min' => 7.5, 'green_max' => 9, 'yellow_min' => 6.5, 'yellow_max' => 24,
        ], $this->as($surgeon))
            ->assertOk()
            ->assertJsonPath('data.indicator', 'sleep')
            ->assertJsonPath('data.green_min', 7.5);

        Event::assertDispatched(fn (ThresholdUpdated $event): bool => $event->indicatorCode === 'sleep' && $event->environment === 'mars');

        // Solo cambió Marte.
        $iss = $this->indicator($this->getJson('/api/v1/thresholds?environment=iss', $this->as($surgeon))->json('data'), 'sleep');
        $this->assertEquals(7, $iss['green_min']);
    }

    public function test_the_green_must_stay_inside_the_yellow(): void
    {
        $this->putJson('/api/v1/thresholds/hr?environment=mars', [
            'green_min' => 60, 'green_max' => 130, 'yellow_min' => 50, 'yellow_max' => 120,
        ], $this->as($this->userWithRole(Role::Admin)))
            ->assertUnprocessable()
            ->assertJsonPath('error_code', 'EVALUATION_INVALID_THRESHOLD')
            ->assertJsonValidationErrors(['yellow_max']);
    }

    public function test_an_unknown_indicator_is_404(): void
    {
        $this->putJson('/api/v1/thresholds/pressure?environment=mars', [
            'green_min' => 1, 'green_max' => 2, 'yellow_min' => 0, 'yellow_max' => 3,
        ], $this->as($this->userWithRole(Role::Admin)))
            ->assertNotFound()
            ->assertJsonPath('error_code', 'EVALUATION_THRESHOLD_MISSING');
    }

    public function test_the_crew_cannot_change_thresholds(): void
    {
        $this->putJson('/api/v1/thresholds/hr?environment=mars', [], $this->as($this->userWithRole(Role::CrewMedicalOfficer)))
            ->assertForbidden();
    }

    public function test_seeding_again_keeps_what_the_doctors_changed(): void
    {
        $this->putJson('/api/v1/thresholds/hr?environment=mars', [
            'green_min' => 55, 'green_max' => 100, 'yellow_min' => 45, 'yellow_max' => 120,
        ], $this->as($this->userWithRole(Role::Admin)))->assertOk();

        $this->seed(ThresholdSeeder::class);

        $this->assertDatabaseHas('thresholds', ['indicator_code' => 'hr', 'environment' => 'mars', 'green_min' => 55]);
        $this->assertDatabaseCount('thresholds', 40);
    }
}
