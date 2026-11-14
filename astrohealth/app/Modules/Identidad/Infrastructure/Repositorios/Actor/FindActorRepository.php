<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Infrastructure\Repositorios\Actor;

use App\Models\User;
use App\Modules\Identidad\Contratos\Actor;
use App\Modules\Identidad\Domain\Enums\ActorType;
use App\Modules\Identidad\Infrastructure\Models\Device;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * BUSCAR al actor autenticado (persona o máquina) para el guard JWT.
 *
 * Es el único repositorio que devuelve el modelo: Laravel (Auth, Gates)
 * necesita un Authenticatable. Solo lo usa Infrastructure/Jwt/JwtGuard.
 */
final class FindActorRepository
{
    public function find(ActorType $type, int $id): (Actor&Authenticatable)|null
    {
        return match ($type) {
            ActorType::User => User::query()->find($id),
            ActorType::Device => Device::query()->find($id),
        };
    }
}
