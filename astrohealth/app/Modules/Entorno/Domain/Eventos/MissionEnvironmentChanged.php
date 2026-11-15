<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Domain\Eventos;

use App\Modules\Entorno\Domain\Enums\Environment;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;

/**
 * Hecho: una misión cambió de entorno. Lo escucharán M2/M1 para volver a
 * evaluar las últimas mediciones con los umbrales del entorno nuevo.
 * Se publica después de confirmar la transacción.
 */
final readonly class MissionEnvironmentChanged implements ShouldDispatchAfterCommit
{
    public function __construct(
        public int $missionId,
        public Environment $from,
        public Environment $to,
    ) {}
}
