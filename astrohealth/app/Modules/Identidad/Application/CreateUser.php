<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application;

use App\Modules\Identidad\Application\DTOs\ManagedUserResult;
use App\Modules\Identidad\Application\DTOs\UserData;
use App\Modules\Identidad\Application\Servicios\AccessDirectory;
use App\Modules\Identidad\Application\Servicios\UserLinks;
use App\Modules\Identidad\Domain\Excepciones\EmailTaken;
use App\Modules\Identidad\Infrastructure\Repositorios\User\FindUserRepository;
use App\Modules\Identidad\Infrastructure\Repositorios\User\InsertUserRepository;
use Illuminate\Support\Str;

/**
 * Caso de uso: el administrador da acceso a una persona.
 *
 * Se crea con una contraseña aleatoria que nadie conoce: la contraseña real
 * se asigna aparte (php artisan identidad:user-password <correo>).
 */
final readonly class CreateUser
{
    public function __construct(
        private FindUserRepository $findUser,
        private InsertUserRepository $insertUser,
        private UserLinks $links,
        private AccessDirectory $directory,
    ) {}

    public function execute(UserData $data): ManagedUserResult
    {
        $email = Str::lower(trim($data->email));

        if ($this->findUser->findByEmail($email) !== null) {
            throw new EmailTaken;
        }

        $missionId = $this->links->check($data);

        $account = $this->insertUser->insert(
            name: trim($data->name),
            email: $email,
            password: Str::password(40),
            role: $data->role,
            astronautId: $data->astronautId,
            missionId: $missionId,
        );

        return $this->directory->user($account);
    }
}
