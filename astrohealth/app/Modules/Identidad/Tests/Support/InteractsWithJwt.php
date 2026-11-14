<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Tests\Support;

use App\Models\User;
use App\Modules\Entorno\Infrastructure\Models\Astronaut;
use App\Modules\Entorno\Infrastructure\Models\Mission;
use App\Modules\Identidad\Application\CreateDevice;
use App\Modules\Identidad\Application\Servicios\ActorIdentityLoader;
use App\Modules\Identidad\Application\Servicios\TokenFactory;
use App\Modules\Identidad\Contratos\Actor;
use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Infrastructure\Models\Device;

/**
 * Ayudas para las pruebas de M0: usuarios por rol, dispositivos y tokens firmados.
 */
trait InteractsWithJwt
{
    protected const PASSWORD = 'password';

    protected function mission(): Mission
    {
        return Mission::factory()->create();
    }

    protected function astronaut(?Mission $mission = null): Astronaut
    {
        return Astronaut::factory()->create(['mission_id' => ($mission ?? $this->mission())->id]);
    }

    protected function userWithRole(Role $role, ?Astronaut $astronaut = null): User
    {
        $astronautId = $role->requiresAstronaut() ? ($astronaut ?? $this->astronaut())->id : null;

        return User::factory()->create([
            'role' => $role,
            'astronaut_id' => $astronautId,
            'password' => self::PASSWORD,
        ]);
    }

    /**
     * @return array{0: Device, 1: string} el dispositivo y su secreto en texto plano
     */
    protected function device(?Mission $mission = null): array
    {
        $new = app(CreateDevice::class)->execute(($mission ?? $this->mission())->id, null, 'Sensor de prueba');

        $device = Device::query()->findOrFail($new->id);

        return [$device, $new->clientSecret];
    }

    protected function tokenFor(Actor $actor): string
    {
        $identity = app(ActorIdentityLoader::class)->load($actor->actorType(), $actor->actorId());

        $this->assertNotNull($identity, 'El actor de la prueba no existe en la base.');

        return app(TokenFactory::class)->accessFor($identity)->token;
    }

    /**
     * @return array<string, string>
     */
    protected function bearer(string $token): array
    {
        return ['Authorization' => 'Bearer '.$token];
    }
}
