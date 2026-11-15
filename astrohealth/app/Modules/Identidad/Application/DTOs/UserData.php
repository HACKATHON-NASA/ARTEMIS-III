<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application\DTOs;

use App\Modules\Identidad\Domain\Enums\Role;

/**
 * Lo que el administrador escribe al crear o editar a una persona.
 * Al editar, el correo no cambia (se ignora).
 */
final readonly class UserData
{
    public function __construct(
        public string $name,
        public string $email,
        public Role $role,
        public ?int $astronautId,
        public ?int $missionId,
    ) {}
}
