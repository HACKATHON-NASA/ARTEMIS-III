<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application;

use App\Modules\Identidad\Application\DTOs\ManagedUserResult;
use App\Modules\Identidad\Application\Servicios\AccessDirectory;
use App\Modules\Identidad\Infrastructure\Repositorios\User\FindUserRepository;

/**
 * Caso de uso: las personas con acceso, para la vista de administración.
 */
final readonly class ListUsers
{
    public function __construct(
        private FindUserRepository $findUser,
        private AccessDirectory $directory,
    ) {}

    /**
     * @return list<ManagedUserResult>
     */
    public function execute(): array
    {
        return $this->directory->users($this->findUser->all());
    }
}
