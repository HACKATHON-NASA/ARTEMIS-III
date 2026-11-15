<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Tests\Feature;

use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Infrastructure\Models\Device;
use App\Modules\Identidad\Tests\Support\InteractsWithJwt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * /api/v1/devices: el administrador registra y apaga las máquinas.
 */
final class DevicesApiTest extends TestCase
{
    use InteractsWithJwt;
    use RefreshDatabase;

    /** @return array<string, string> */
    private function asAdmin(): array
    {
        return $this->bearer($this->tokenFor($this->userWithRole(Role::Admin)));
    }

    public function test_the_admin_lists_devices_without_their_secret(): void
    {
        [$device] = $this->device();

        $response = $this->getJson('/api/v1/devices', $this->asAdmin())
            ->assertOk()
            ->assertJsonPath('data.0.id', $device->id)
            ->assertJsonPath('data.0.client_id', $device->client_id)
            ->assertJsonPath('data.0.astronaut_name', null);

        $this->assertStringNotContainsString('secret', (string) $response->getContent());
    }

    public function test_only_the_admin_can_manage_devices(): void
    {
        $this->getJson('/api/v1/devices')->assertUnauthorized();
        $this->getJson('/api/v1/devices', $this->bearer($this->tokenFor($this->userWithRole(Role::FlightSurgeon))))
            ->assertForbidden();
    }

    public function test_creating_a_device_returns_its_secret_once_and_it_works(): void
    {
        $mission = $this->mission();
        $astronaut = $this->astronaut($mission);

        $response = $this->postJson('/api/v1/devices', [
            'name' => 'Pulsera biométrica',
            'mission_id' => $mission->id,
            'astronaut_id' => $astronaut->id,
        ], $this->asAdmin())
            ->assertCreated()
            ->assertJsonPath('data.device.name', 'Pulsera biométrica')
            ->assertJsonPath('data.device.astronaut_name', $astronaut->name)
            ->assertJsonPath('data.device.active', true);

        $this->postJson('/api/v1/auth/device-token', [
            'client_id' => $response->json('data.device.client_id'),
            'client_secret' => $response->json('data.client_secret'),
        ])->assertOk();
    }

    public function test_a_wearable_must_belong_to_the_device_mission(): void
    {
        $other = $this->astronaut();

        $this->postJson('/api/v1/devices', [
            'name' => 'Pulsera',
            'mission_id' => $this->mission()->id,
            'astronaut_id' => $other->id,
        ], $this->asAdmin())
            ->assertUnprocessable()
            ->assertJsonPath('error_code', 'AUTH_ASTRONAUT_NOT_IN_MISSION')
            ->assertJsonValidationErrors(['astronaut_id']);

        $this->postJson('/api/v1/devices', ['name' => 'X', 'mission_id' => 999], $this->asAdmin())
            ->assertUnprocessable()
            ->assertJsonPath('error_code', 'AUTH_UNKNOWN_MISSION');
    }

    public function test_a_deactivated_device_cannot_get_a_token(): void
    {
        [$device, $secret] = $this->device();

        $this->patchJson("/api/v1/devices/{$device->id}/active", ['active' => false], $this->asAdmin())
            ->assertOk()
            ->assertJsonPath('data.active', false);

        $this->assertFalse(Device::query()->findOrFail($device->id)->active);
        $this->postJson('/api/v1/auth/device-token', ['client_id' => $device->client_id, 'client_secret' => $secret])
            ->assertUnauthorized();
    }

    public function test_an_unknown_device_is_404(): void
    {
        $this->patchJson('/api/v1/devices/999/active', ['active' => true], $this->asAdmin())
            ->assertNotFound()
            ->assertJsonPath('error_code', 'AUTH_DEVICE_NOT_FOUND');
    }
}
