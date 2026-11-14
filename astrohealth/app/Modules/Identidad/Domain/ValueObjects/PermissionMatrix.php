<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\ValueObjects;

use App\Modules\Identidad\Domain\Enums\Role;
use InvalidArgumentException;

/**
 * La matriz de permisos por rol (viene de config/permissions.php).
 *
 * Es la única fuente: los Gates y GET /auth/me salen de aquí.
 */
final readonly class PermissionMatrix
{
    /**
     * @param  array<string, list<string>>  $matrix  permiso => roles que lo tienen
     */
    public function __construct(private array $matrix)
    {
        foreach ($matrix as $permission => $roles) {
            foreach ($roles as $role) {
                if (Role::tryFrom($role) === null) {
                    throw new InvalidArgumentException("El permiso \"{$permission}\" nombra un rol que no existe: \"{$role}\".");
                }
            }
        }
    }

    public function allows(Role $role, string $permission): bool
    {
        return in_array($role->value, $this->matrix[$permission] ?? [], true);
    }

    /**
     * @return list<string>
     */
    public function permissionsFor(Role $role): array
    {
        return array_values(array_filter(
            array_keys($this->matrix),
            fn (string $permission): bool => $this->allows($role, $permission),
        ));
    }

    /**
     * @return list<string>
     */
    public function permissions(): array
    {
        return array_keys($this->matrix);
    }
}
