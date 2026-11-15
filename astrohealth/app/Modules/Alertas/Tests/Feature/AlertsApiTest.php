<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Tests\Feature;

use App\Models\User;
use App\Modules\Entorno\Infrastructure\Models\Astronaut;
use App\Modules\Entorno\Infrastructure\Models\Mission;
use App\Modules\Evaluacion\Database\Seeders\ThresholdSeeder;
use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Tests\Support\InteractsWithJwt;
use App\Modules\Registro\Database\Seeders\IndicatorSeeder;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * /api/v1/alerts: las alertas vitales salen solas; el tripulante habla con
 * Tierra; Tierra atiende. Cada quien ve solo lo de su misión.
 */
final class AlertsApiTest extends TestCase
{
    use InteractsWithJwt;
    use RefreshDatabase;

    private Mission $mission;

    private Astronaut $elena;

    private User $elenaUser;

    private User $surgeon;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([ThresholdSeeder::class, IndicatorSeeder::class]);
        $this->mission = $this->mission();
        $this->elena = $this->astronaut($this->mission);
        $this->elenaUser = $this->userWithRole(Role::Astronaut, $this->elena);
        $this->surgeon = User::factory()->create(['name' => 'Dra. Sofía Ramírez', 'role' => Role::FlightSurgeon, 'mission_id' => $this->mission->id]);
    }

    /** @return array<string, string> */
    private function as(User $user): array
    {
        return $this->bearer($this->tokenFor($user));
    }

    public function test_a_red_or_yellow_value_creates_its_alert_on_its_own(): void
    {
        $this->postJson('/api/v1/measurements', ['measurements' => [
            ['indicator' => 'bp_sys', 'value' => 148],
            ['indicator' => 'sleep', 'value' => 6.2],
            ['indicator' => 'hr', 'value' => 72],
        ]], $this->as($this->elenaUser))->assertCreated();

        $alerts = $this->getJson('/api/v1/alerts', $this->as($this->elenaUser))
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->json('data');

        $byIndicator = array_column($alerts, null, 'indicator');
        $this->assertSame('red', $byIndicator['bp_sys']['level']);
        $this->assertSame('Presión sistólica 148 mmHg (crítico)', $byIndicator['bp_sys']['message']);
        $this->assertSame('manual', $byIndicator['bp_sys']['source']);
        $this->assertSame(1200, $byIndicator['bp_sys']['delay_seconds']);
        $this->assertSame('Sueño 6,2 h (precaución)', $byIndicator['sleep']['message']);
        $this->assertSame(
            1200,
            (int) CarbonImmutable::parse($byIndicator['bp_sys']['sent_at'])->diffInSeconds(CarbonImmutable::parse($byIndicator['bp_sys']['ground_eta_at'])),
        );
    }

    public function test_old_values_do_not_create_alerts(): void
    {
        $this->postJson('/api/v1/measurements', [
            'measured_at' => now()->subDays(3)->toIso8601String(),
            'measurements' => [['indicator' => 'bp_sys', 'value' => 160]],
        ], $this->as($this->elenaUser))->assertCreated();

        $this->assertDatabaseCount('alerts', 0);
    }

    public function test_the_crew_member_sends_sos_help_and_news(): void
    {
        $headers = $this->as($this->elenaUser);

        $this->postJson('/api/v1/alerts', ['kind' => 'sos'], $headers)
            ->assertCreated()
            ->assertJsonPath('data.level', 'red')
            ->assertJsonPath('data.source', 'astronaut')
            ->assertJsonPath('data.astronaut_id', $this->elena->id);

        $this->postJson('/api/v1/alerts', ['kind' => 'help', 'category' => 'Síntoma o malestar', 'note' => 'Me falta el aire.'], $headers)
            ->assertJsonPath('data.level', 'yellow')
            ->assertJsonPath('data.note', 'Me falta el aire.');

        $this->postJson('/api/v1/alerts', ['kind' => 'help', 'note' => 'Riesgo en el test.', 'urgent' => true], $headers)
            ->assertJsonPath('data.level', 'red');

        $this->postJson('/api/v1/alerts', ['kind' => 'report', 'note' => 'Todo tranquilo.'], $headers)
            ->assertJsonPath('data.level', 'info');

        $this->postJson('/api/v1/alerts', ['kind' => 'help'], $headers)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['note']);
    }

    public function test_earth_cannot_send_messages_as_crew(): void
    {
        $this->postJson('/api/v1/alerts', ['kind' => 'sos'], $this->as($this->surgeon))->assertForbidden();
    }

    public function test_each_one_sees_only_what_belongs_to_them(): void
    {
        $mate = $this->astronaut($this->mission);
        $this->postJson('/api/v1/alerts', ['kind' => 'sos'], $this->as($this->elenaUser))->assertCreated();
        $this->postJson('/api/v1/alerts', ['kind' => 'report', 'note' => 'Hola.'], $this->as($this->userWithRole(Role::Astronaut, $mate)))->assertCreated();

        $this->getJson('/api/v1/alerts', $this->as($this->elenaUser))->assertJsonCount(1, 'data');
        $this->getJson('/api/v1/alerts', $this->as($this->surgeon))->assertJsonCount(2, 'data');
        $this->getJson("/api/v1/alerts?astronaut={$mate->id}", $this->as($this->surgeon))->assertJsonCount(1, 'data');

        $stranger = User::factory()->create(['role' => Role::FlightSurgeon, 'mission_id' => $this->mission()->id]);
        $this->getJson('/api/v1/alerts', $this->as($stranger))->assertJsonCount(0, 'data');
    }

    public function test_earth_attends_once_and_only_its_own_mission(): void
    {
        $alert = $this->postJson('/api/v1/alerts', ['kind' => 'sos'], $this->as($this->elenaUser))->json('data.id');

        $stranger = User::factory()->create(['role' => Role::FlightSurgeon, 'mission_id' => $this->mission()->id]);
        $this->patchJson("/api/v1/alerts/{$alert}/acknowledge", [], $this->as($stranger))
            ->assertForbidden()
            ->assertJsonPath('error_code', 'ALERTS_NOT_YOUR_PATIENT');

        $this->patchJson("/api/v1/alerts/{$alert}/acknowledge", ['reply' => 'Recibido, vamos.'], $this->as($this->surgeon))
            ->assertOk()
            ->assertJsonPath('data.reply', 'Recibido, vamos.');

        $this->patchJson("/api/v1/alerts/{$alert}/acknowledge", [], $this->as($this->surgeon))
            ->assertStatus(409)
            ->assertJsonPath('error_code', 'ALERTS_ALREADY_ATTENDED');

        $this->patchJson("/api/v1/alerts/{$alert}/acknowledge", [], $this->as($this->elenaUser))->assertForbidden();
        $this->patchJson('/api/v1/alerts/999/acknowledge', [], $this->as($this->surgeon))->assertNotFound();
    }
}
