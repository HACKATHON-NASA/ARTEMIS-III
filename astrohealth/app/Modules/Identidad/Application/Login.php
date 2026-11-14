<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application;

use App\Modules\Identidad\Application\DTOs\Credentials;
use App\Modules\Identidad\Application\DTOs\SessionResult;
use App\Modules\Identidad\Domain\Excepciones\InvalidCredentials;
use App\Modules\Identidad\Domain\Excepciones\UserInactive;
use App\Modules\Identidad\Domain\Excepciones\UserWithoutRole;
use App\Modules\Identidad\Domain\Reglas\RoleAssignment;
use App\Modules\Identidad\Infrastructure\Repositorios\UserRepository;
use App\Shared\Persistencia\Transaction;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Hashing\Hasher;

/**
 * Caso de uso: una persona inicia sesión con correo y contraseña.
 *
 * Orden: credenciales (sin revelar cuál falló) → activo → rol válido →
 * en una transacción: último acceso + refresh token nuevo → access token.
 */
final readonly class Login
{
    public function __construct(
        private UserRepository $users,
        private SessionIssuer $sessions,
        private Transaction $transaction,
        private Hasher $hasher,
    ) {}

    /**
     * @throws InvalidCredentials
     * @throws UserInactive
     * @throws UserWithoutRole
     */
    public function execute(Credentials $credentials): SessionResult
    {
        $user = $this->users->findByEmail($credentials->email);

        if ($user === null) {
            DummyHash::check($this->hasher, $credentials->password);

            throw new InvalidCredentials;
        }

        if (! $this->hasher->check($credentials->password, $user->password)) {
            throw new InvalidCredentials;
        }

        if (! $user->actorIsActive()) {
            throw new UserInactive;
        }

        $role = $user->actorRole() ?? throw new UserWithoutRole;

        RoleAssignment::assert($role, $user->actorAstronautId());

        return $this->transaction->run(function () use ($user, $credentials): SessionResult {
            $this->users->recordLogin($user, CarbonImmutable::now());

            return $this->sessions->start($user, $credentials->ip, $credentials->userAgent);
        });
    }
}
