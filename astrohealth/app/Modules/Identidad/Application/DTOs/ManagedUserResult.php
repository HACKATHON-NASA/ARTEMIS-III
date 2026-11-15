<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application\DTOs;

use App\Modules\Identidad\Domain\Entidades\UserAccount;

/**
 * Una persona en la vista de administración: su cuenta más los nombres de
 * su tripulante y su misión (de M6). missionId es la efectiva: la del
 * médico de vuelo o la del astronauta enlazado.
 */
final readonly class ManagedUserResult
{
    public function __construct(
        public UserAccount $account,
        public ?string $astronautName,
        public ?string $crewRole,
        public ?int $missionId,
        public ?string $missionName,
    ) {}
}
