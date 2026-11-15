<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Infrastructure\Listeners;

use App\Modules\Evaluacion\Domain\Eventos\PsychTestTaken;
use App\Modules\Gamificacion\Application\AwardForPsychTest;

/**
 * M2: un tripulante hizo un test psicológico. M7 suma sus puntos.
 */
final readonly class OnPsychTestTaken
{
    public function __construct(private AwardForPsychTest $award) {}

    public function handle(PsychTestTaken $event): void
    {
        $this->award->execute($event);
    }
}
