<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Tests\Feature;

use App\Modules\Identidad\Tests\Support\InteractsWithJwt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * POST /api/v1/auth/device-token: la máquina (el simulador) obtiene su token.
 */
final class DeviceTokenTest extends TestCase
{
    use InteractsWithJwt;
    use RefreshDatabase;

    public function test_a_device_gets_a_token_with_only_the_ingest_permission(): void
    {
        [$device, $secret] = $this->device();

        $response = $this->postJson('/api/v1/auth/device-token', ['client_id' => $device->client_id, 'client_secret' => $secret]);

        $response->assertOk()
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonPath('data.actor.type', 'device')
            ->assertJsonPath('data.actor.role', 'device')
            ->assertJsonPath('data.actor.mission_id', $device->mission_id)
            ->assertJsonPath('data.actor.permissions', ['measurements.ingest']);
        $this->assertNull($response->getCookie('refresh_token', false), 'Un dispositivo no recibe refresh token.');
        $this->assertNotNull($device->fresh()?->last_seen_at);

        $this->getJson('/api/v1/auth/me', $this->bearer((string) $response->json('data.access_token')))
            ->assertOk()
            ->assertJsonPath('data.type', 'device');
    }

    public function test_the_secret_is_stored_hashed(): void
    {
        [$device, $secret] = $this->device();

        $this->assertNotSame($secret, $device->secret_hash);
        $this->assertArrayNotHasKey('secret_hash', $device->toArray());
    }

    public function test_a_wrong_secret_is_401(): void
    {
        [$device] = $this->device();

        $this->postJson('/api/v1/auth/device-token', ['client_id' => $device->client_id, 'client_secret' => 'no-es-el-secreto'])
            ->assertUnauthorized()
            ->assertJsonPath('error_code', 'AUTH_DEVICE_INVALID');
    }

    public function test_an_unknown_client_is_401_with_the_same_answer(): void
    {
        $this->postJson('/api/v1/auth/device-token', ['client_id' => 'no-existe', 'client_secret' => 'x'])
            ->assertUnauthorized()
            ->assertJsonPath('error_code', 'AUTH_DEVICE_INVALID');
    }

    public function test_an_inactive_device_is_401_and_its_old_token_stops_working(): void
    {
        [$device, $secret] = $this->device();
        $token = $this->tokenFor($device);

        $device->forceFill(['active' => false])->save();

        $this->postJson('/api/v1/auth/device-token', ['client_id' => $device->client_id, 'client_secret' => $secret])
            ->assertUnauthorized();
        $this->getJson('/api/v1/auth/me', $this->bearer($token))->assertUnauthorized();
    }
}
