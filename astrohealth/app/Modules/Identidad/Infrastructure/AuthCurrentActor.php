<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Infrastructure;

use App\Modules\Identidad\Contratos\Actor;
use App\Modules\Identidad\Contratos\CurrentActor;
use App\Modules\Identidad\Domain\ValueObjects\TokenClaims;
use App\Modules\Identidad\Infrastructure\Jwt\JwtGuard;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

/**
 * Implementación de CurrentActor con el guard "api" (JWT).
 */
final readonly class AuthCurrentActor implements CurrentActor
{
    public function __construct(private AuthFactory $auth) {}

    public function get(): Actor
    {
        $actor = $this->guard()->user();

        if (! $actor instanceof Actor) {
            throw new AuthenticationException;
        }

        return $actor;
    }

    public function claims(): TokenClaims
    {
        return $this->guard()->claims() ?? throw new AuthenticationException;
    }

    private function guard(): JwtGuard
    {
        $guard = $this->auth->guard('api');

        if (! $guard instanceof JwtGuard) {
            throw new AuthenticationException('El guard "api" no es JWT.');
        }

        return $guard;
    }
}
