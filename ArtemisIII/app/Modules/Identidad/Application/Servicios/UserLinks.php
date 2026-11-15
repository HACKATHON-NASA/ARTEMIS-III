<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application\Servicios;

use App\Modules\Entorno\Contratos\CrewQuery;
use App\Modules\Entorno\Contratos\MissionQuery;
use App\Modules\Identidad\Application\DTOs\UserData;
use App\Modules\Identidad\Domain\Excepciones\AstronautTaken;
use App\Modules\Identidad\Domain\Excepciones\UnknownAstronaut;
use App\Modules\Identidad\Domain\Excepciones\UnknownMission;
use App\Modules\Identidad\Domain\Reglas\RoleAssignment;
use App\Modules\Identidad\Infrastructure\Repositorios\User\FindUserRepository;

/**
 * Comprueba a qué se enlaza una persona antes de crearla o editarla: rol
 * válido, su tripulante (existe y no tiene otro usuario) y su misión.
 * Devuelve la misión que se guarda en users.mission_id.
 */
final readonly class UserLinks
{
    public function __construct(
        private FindUserRepository $findUser,
        private CrewQuery $crew,
        private MissionQuery $missions,
    ) {}

    public function check(UserData $data, ?int $userId = null): ?int
    {
        RoleAssignment::assert($data->role, $data->astronautId);
        $missionId = RoleAssignment::missionFor($data->role, $data->missionId);

        if ($data->astronautId !== null) {
            if (! $this->crew->exists($data->astronautId)) {
                throw new UnknownAstronaut;
            }

            $owner = $this->findUser->findByAstronautId($data->astronautId);

            if ($owner !== null && $owner->id !== $userId) {
                throw new AstronautTaken;
            }
        }

        if ($missionId !== null && ! $this->missions->exists($missionId)) {
            throw new UnknownMission;
        }

        return $missionId;
    }
}
