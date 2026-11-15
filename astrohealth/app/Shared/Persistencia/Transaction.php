<?php

declare(strict_types=1);

namespace App\Shared\Persistencia;

/**
 * Transacción de base de datos sin que los casos de uso toquen DB::.
 */
interface Transaction
{
    /**
     * Ejecuta el callback dentro de una transacción y devuelve su resultado.
     *
     * @template T
     *
     * @param  callable(): T  $callback
     * @param  positive-int  $attempts  Intentos ante un deadlock (1 = sin reintentos).
     * @return T
     */
    public function run(callable $callback, int $attempts = 1): mixed;
}
