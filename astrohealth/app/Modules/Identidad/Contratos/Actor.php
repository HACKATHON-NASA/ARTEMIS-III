<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Contratos;

use App\Modules\Identidad\Domain\Enums\ActorType;
use App\Modules\Identidad\Domain\Enums\Role;

/**
 * Quien llama a la API: una persona (App\Models\User) o una máquina (Device).
 *
 * Los demás módulos solo conocen esta interfaz.
 */
interface Actor
{
    public function actorId(): int;

    public function actorType(): ActorType;

    /** Null solo en un usuario al que aún no se le asignó rol (no puede usar la API). */
    public function actorRole(): ?Role;

    public function actorAstronautId(): ?int;

    public function actorIsActive(): bool;

    public function actorName(): string;
}
