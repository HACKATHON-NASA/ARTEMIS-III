<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application;

use App\Modules\Identidad\Application\DTOs\SessionResult;
use App\Modules\Identidad\Application\Servicios\SessionIssuer;
use App\Modules\Identidad\Domain\Entidades\UserAccount;
use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Domain\Excepciones\QuickAccessOff;
use App\Modules\Identidad\Domain\Reglas\RoleAssignment;
use App\Modules\Identidad\Infrastructure\Repositorios\User\FindUserRepository;
use App\Modules\Identidad\Infrastructure\Repositorios\User\UpdateUserRepository;
use App\Shared\Persistencia\Transaction;
use Carbon\CarbonImmutable;

/**
 * Casos de uso del acceso rápido (config identidad.quick_access): la lista
 * de quién puede entrar y entrar eligiendo a uno, sin contraseña. La
 * sesión es la misma del login normal (JWT + refresh token en la cookie),
 * así los permisos y los Gates no cambian.
 */
final readonly class QuickAccess
{
    /** El orden de la lista: primero la tripulación, al final la administración. */
    private const ORDER = [Role::Astronaut, Role::CrewMedicalOfficer, Role::FlightSurgeon, Role::Admin];

    public function __construct(
        private FindUserRepository $findUser,
        private UpdateUserRepository $updateUser,
        private SessionIssuer $sessions,
        private Transaction $transaction,
        private bool $enabled,
    ) {}

    public function enabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Los usuarios activos con rol, en el orden de ORDER y luego por nombre.
     *
     * @return list<UserAccount>
     */
    public function users(): array
    {
        if (! $this->enabled) {
            throw new QuickAccessOff;
        }

        $users = array_values(array_filter($this->findUser->all(), fn (UserAccount $u): bool => $u->active && $u->role !== null && $u->role->isHuman()));

        usort($users, fn (UserAccount $a, UserAccount $b): int => [self::rank($a), $a->name] <=> [self::rank($b), $b->name]);

        return $users;
    }

    public function enter(int $userId, ?string $ip, ?string $userAgent): SessionResult
    {
        if (! $this->enabled) {
            throw new QuickAccessOff;
        }

        $user = $this->findUser->findById($userId);

        if ($user === null || ! $user->active || $user->role === null || ! $user->role->isHuman()) {
            throw new QuickAccessOff;
        }

        RoleAssignment::assert($user->role, $user->astronautId);

        return $this->transaction->run(function () use ($user, $ip, $userAgent): SessionResult {
            $this->updateUser->recordLogin($user->id, CarbonImmutable::now());

            return $this->sessions->start($user, $ip, $userAgent);
        });
    }

    private static function rank(UserAccount $user): int
    {
        $index = array_search($user->role, self::ORDER, true);

        return $index === false ? count(self::ORDER) : $index;
    }
}
