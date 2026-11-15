<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Console;

use App\Modules\Identidad\Application\CreateDevice;
use App\Shared\Excepciones\BusinessError;
use Illuminate\Console\Command;

/**
 * php artisan identidad:device-create --mission=1 --name="Simulador Ares-1" [--astronaut=] [--client-id=]
 *
 * Registra una máquina y muestra su secreto UNA sola vez.
 */
final class CreateDeviceCommand extends Command
{
    protected $signature = 'identidad:device-create
        {--mission= : Id de la misión}
        {--name= : Nombre del dispositivo}
        {--astronaut= : Id del astronauta (vacío = gateway de toda la misión)}
        {--client-id= : client_id (vacío = se genera)}';

    protected $description = 'Registra un dispositivo (máquina) y muestra su client_id y secreto';

    public function handle(CreateDevice $createDevice): int
    {
        $mission = $this->option('mission');
        $name = $this->option('name');
        $astronaut = $this->option('astronaut');
        $clientId = $this->option('client-id');

        // Las opciones con valor llegan como string|null.
        if ($mission === null || ! ctype_digit($mission) || $name === null || trim($name) === '') {
            $this->error('Indique --mission (número) y --name.');

            return self::FAILURE;
        }

        if ($astronaut !== null && ! ctype_digit($astronaut)) {
            $this->error('--astronaut debe ser un número.');

            return self::FAILURE;
        }

        try {
            $device = $createDevice->execute(
                missionId: (int) $mission,
                astronautId: $astronaut === null ? null : (int) $astronaut,
                name: trim($name),
                clientId: $clientId === null || $clientId === '' ? null : $clientId,
            );
        } catch (BusinessError $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info("Dispositivo #{$device->id} \"{$device->name}\" creado.");
        $this->line("client_id:     {$device->clientId}");
        $this->line("client_secret: {$device->clientSecret}");
        $this->warn('Guarde el secreto ahora: no se volverá a mostrar.');

        return self::SUCCESS;
    }
}
