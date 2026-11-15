<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Console;

use Illuminate\Console\Command;

/**
 * php artisan identidad:jwt-secret [--force] [--show]
 *
 * Genera un secreto de 256 bits para firmar los JWT y lo escribe en .env.
 * No reemplaza uno existente sin --force (cambiarlo cierra todas las sesiones).
 */
final class JwtSecretCommand extends Command
{
    protected $signature = 'identidad:jwt-secret
        {--force : Reemplaza el secreto existente (invalida todos los tokens)}
        {--show : Solo muestra un secreto nuevo, sin escribir .env}';

    protected $description = 'Genera JWT_SECRET (256 bits) y lo escribe en .env';

    public function handle(): int
    {
        $secret = 'base64:'.base64_encode(random_bytes(32));

        if ($this->option('show') === true) {
            $this->line($secret);

            return self::SUCCESS;
        }

        $path = $this->laravel->environmentFilePath();

        if (! is_file($path)) {
            $this->error("No existe {$path}. Copie .env.example a .env primero.");

            return self::FAILURE;
        }

        $env = (string) file_get_contents($path);
        $hasSecret = preg_match('/^JWT_SECRET=(.+)$/m', $env) === 1;

        if ($hasSecret && $this->option('force') !== true) {
            $this->warn('JWT_SECRET ya existe. Use --force para reemplazarlo (cerrará todas las sesiones).');

            return self::SUCCESS;
        }

        $line = 'JWT_SECRET='.$secret;
        $env = preg_match('/^JWT_SECRET=.*$/m', $env) === 1
            ? (string) preg_replace('/^JWT_SECRET=.*$/m', $line, $env)
            : rtrim($env, "\r\n").PHP_EOL.PHP_EOL.$line.PHP_EOL;

        file_put_contents($path, $env);

        $this->info('JWT_SECRET generado y guardado en .env.');

        return self::SUCCESS;
    }
}
