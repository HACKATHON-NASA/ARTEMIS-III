<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application;

use App\Modules\Identidad\Application\DTOs\ManagedUserResult;
use App\Modules\Identidad\Application\Servicios\AccessDirectory;
use App\Modules\Identidad\Contratos\CurrentActor;
use App\Modules\Identidad\Domain\Entidades\UserAccount;
use App\Modules\Identidad\Domain\Enums\ActorType;
use App\Modules\Identidad\Domain\Excepciones\OwnAccessLocked;
use App\Modules\Identidad\Domain\Excepciones\UserNotFound;
use App\Modules\Identidad\Infrastructure\Repositorios\RefreshToken\UpdateRefreshTokenRepository;
use App\Modules\Identidad\Infrastructure\Repositorios\User\FindUserRepository;
use App\Modules\Identidad\Infrastructure\Repositorios\User\UpdateUserRepository;
use App\Shared\Persistencia\Transaction;
use Carbon\CarbonImmutable;

/**
 * Caso de uso: activar o desactivar a una persona. Desactivar cierra todas
 * sus sesiones (revoca sus refresh tokens); su access token deja de servir
 * en la siguiente petición porque los permisos exigen estar activo.
 */
final readonly class SetUserActive
{
    public function __construct(
        private FindUserRepository $findUser,
        private UpdateUserRepository $updateUser,
        private UpdateRefreshTokenRepository $updateTokens,
        private AccessDirectory $directory,
        private CurrentActor $currentActor,
        private Transaction $transaction,
    ) {}

    public function execute(int $userId, bool $active): ManagedUserResult
    {
        $this->findUser->findById($userId) ?? throw new UserNotFound;
        $claims = $this->currentActor->claims();

        if (! $active && $claims->type === ActorType::User && $claims->actorId === $userId) {
            throw new OwnAccessLocked;
        }

        $account = $this->transaction->run(function () use ($userId, $active): UserAccount {
            $account = $this->updateUser->setActive($userId, $active);

            if (! $active) {
                $this->updateTokens->revokeAllOfUser($userId, CarbonImmutable::now());
            }

            return $account;
        });

        return $this->directory->user($account);
    }
}
