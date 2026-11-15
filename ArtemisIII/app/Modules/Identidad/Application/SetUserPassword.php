<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application;

use App\Modules\Identidad\Domain\Excepciones\UserNotFound;
use App\Modules\Identidad\Infrastructure\Repositorios\RefreshToken\UpdateRefreshTokenRepository;
use App\Modules\Identidad\Infrastructure\Repositorios\User\FindUserRepository;
use App\Modules\Identidad\Infrastructure\Repositorios\User\UpdateUserRepository;
use App\Shared\Persistencia\Transaction;
use Carbon\CarbonImmutable;

/**
 * Caso de uso: asignar la contraseña de una persona (la crea el admin sin
 * contraseña conocida). Cierra las sesiones que tuviera abiertas.
 */
final readonly class SetUserPassword
{
    public function __construct(
        private FindUserRepository $findUser,
        private UpdateUserRepository $updateUser,
        private UpdateRefreshTokenRepository $updateTokens,
        private Transaction $transaction,
    ) {}

    public function execute(string $email, #[\SensitiveParameter] string $password): void
    {
        $user = $this->findUser->findByEmail(trim($email)) ?? throw new UserNotFound;

        $this->transaction->run(function () use ($user, $password): void {
            $this->updateUser->setPassword($user->id, $password);
            $this->updateTokens->revokeAllOfUser($user->id, CarbonImmutable::now());
        });
    }
}
