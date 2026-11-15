<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Console;

use App\Modules\Identidad\Application\SetUserPassword;
use App\Shared\Excepciones\BusinessError;
use Illuminate\Console\Command;

/**
 * php artisan identidad:user-password persona@astrohealth.test
 *
 * Asigna la contraseña de una persona que el admin creó desde la pantalla
 * (se crea sin contraseña conocida). La pide sin mostrarla.
 */
final class SetUserPasswordCommand extends Command
{
    private const MIN_LENGTH = 8;

    protected $signature = 'identidad:user-password {email : Correo de la persona}';

    protected $description = 'Asigna la contraseña de una persona y cierra sus sesiones abiertas';

    public function handle(SetUserPassword $setPassword): int
    {
        $password = (string) $this->secret('Contraseña nueva (mínimo '.self::MIN_LENGTH.' caracteres)');

        if (mb_strlen($password) < self::MIN_LENGTH) {
            $this->error('La contraseña debe tener al menos '.self::MIN_LENGTH.' caracteres.');

            return self::FAILURE;
        }

        try {
            $setPassword->execute((string) $this->argument('email'), $password);
        } catch (BusinessError $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info('Contraseña asignada.');

        return self::SUCCESS;
    }
}
