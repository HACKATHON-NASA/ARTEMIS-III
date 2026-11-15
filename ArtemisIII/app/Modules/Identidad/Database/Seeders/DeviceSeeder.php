<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Database\Seeders;

use App\Modules\Entorno\Contratos\CrewQuery;
use App\Modules\Identidad\Application\CreateDevice;
use App\Modules\Identidad\Infrastructure\Repositorios\Device\FindDeviceRepository;
use Illuminate\Database\Seeder;

/**
 * El dispositivo del simulador (gateway de la misión Ares-1), con
 * SIMULADOR_CLIENT_ID y SIMULADOR_CLIENT_SECRET del .env.
 * Sin secreto configurado no crea nada: nunca inventa uno.
 */
final class DeviceSeeder extends Seeder
{
    public function run(CreateDevice $createDevice, FindDeviceRepository $findDevice, CrewQuery $crew): void
    {
        $clientId = config('identidad.simulator_device.client_id');
        $secret = config('identidad.simulator_device.client_secret');

        if (! is_string($clientId) || $clientId === '' || ! is_string($secret) || $secret === '') {
            $this->command->warn('DeviceSeeder: falta SIMULADOR_CLIENT_SECRET en .env; no se creó el dispositivo del simulador.');

            return;
        }

        if ($findDevice->findByClientId($clientId) !== null) {
            return;
        }

        $missionId = $crew->all()[0]->missionId ?? null;

        if ($missionId === null) {
            $this->command->warn('DeviceSeeder: no hay misión; ejecute MissionSeeder primero.');

            return;
        }

        $createDevice->execute(
            missionId: $missionId,
            astronautId: null,
            name: 'Simulador Ares-1',
            clientId: $clientId,
            clientSecret: $secret,
        );
    }
}
