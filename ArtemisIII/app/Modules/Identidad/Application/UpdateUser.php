<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application;

use App\Modules\Identidad\Application\DTOs\ManagedUserResult;
use App\Modules\Identidad\Application\DTOs\UserData;
use App\Modules\Identidad\Application\Servicios\AccessDirectory;
use App\Modules\Identidad\Application\Servicios\UserLinks;
use App\Modules\Identidad\Contratos\CurrentActor;
use App\Modules\Identidad\Domain\Enums\ActorType;
use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Domain\Excepciones\OwnAccessLocked;
use App\Modules\Identidad\Domain\Excepciones\UserNotFound;
use App\Modules\Identidad\Infrastructure\Repositorios\User\FindUserRepository;
use App\Modules\Identidad\Infrastructure\Repositorios\User\UpdateUserRepository;

/**
 * Caso de uso: el administrador cambia el nombre, el rol o los enlaces de
 * una persona. El correo no cambia. Nadie se quita su propio rol de admin.
 */
final readonly class UpdateUser
{
    public function __construct(
        private FindUserRepository $findUser,
        private UpdateUserRepository $updateUser,
        private UserLinks $links,
        private AccessDirectory $directory,
        private CurrentActor $currentActor,
    ) {}

    public function execute(int $userId, UserData $data): ManagedUserResult
    {
        $this->findUser->findById($userId) ?? throw new UserNotFound;

        if ($this->isMe($userId) && $data->role !== Role::Admin) {
            throw new OwnAccessLocked;
        }

        $missionId = $this->links->check($data, $userId);

        $account = $this->updateUser->updateAccess($userId, trim($data->name), $data->role, $data->astronautId, $missionId);

        return $this->directory->user($account);
    }

    private function isMe(int $userId): bool
    {
        $claims = $this->currentActor->claims();

        return $claims->type === ActorType::User && $claims->actorId === $userId;
    }
}
