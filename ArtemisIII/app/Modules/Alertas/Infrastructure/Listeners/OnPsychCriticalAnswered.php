<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Infrastructure\Listeners;

use App\Modules\Alertas\Application\CreatePsychRiskAlert;
use App\Modules\Evaluacion\Domain\Eventos\PsychCriticalAnswered;

/**
 * M2: un test marcó una respuesta de riesgo. M5 avisa a Tierra con urgencia.
 */
final readonly class OnPsychCriticalAnswered
{
    public function __construct(private CreatePsychRiskAlert $create) {}

    public function handle(PsychCriticalAnswered $event): void
    {
        $this->create->execute($event);
    }
}
