<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Contratos;

use App\Modules\Identidad\Domain\ValueObjects\TokenClaims;
use Illuminate\Auth\AuthenticationException;

/**
 * El actor autenticado de la petición actual (por JWT).
 *
 * Lo usan los casos de uso de cualquier módulo para saber quién llama,
 * sin tocar el guard ni el request.
 */
interface CurrentActor
{
    /** @throws AuthenticationException si no hay nadie autenticado. */
    public function get(): Actor;

    /** @throws AuthenticationException si no hay nadie autenticado. */
    public function claims(): TokenClaims;
}
