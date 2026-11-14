<?php

declare(strict_types=1);

namespace App\Shared\Persistencia;

use Illuminate\Support\Facades\DB;

/**
 * Implementación con la conexión por defecto (MySQL en la app, SQLite en las pruebas).
 * Es el único lugar, fuera de los repositorios, que usa DB::.
 */
final class DatabaseTransaction implements Transaction
{
    /**
     * @template T
     *
     * @param  callable(): T  $callback
     * @param  positive-int  $attempts  PHPStan exige que quien llama pase 1 o más.
     * @return T
     */
    public function run(callable $callback, int $attempts = 1): mixed
    {
        return DB::transaction($callback(...), $attempts);
    }
}
