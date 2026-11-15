<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Application;

use App\Modules\Alertas\Domain\Entidades\Advice;
use App\Modules\Alertas\Domain\Excepciones\AlertProblem;
use App\Modules\Alertas\Infrastructure\Repositorios\Advice\FindAdviceRepository;
use App\Modules\Alertas\Infrastructure\Repositorios\Advice\UpdateAdviceRepository;
use App\Modules\Identidad\Contratos\CurrentActor;
use Carbon\CarbonImmutable;

/**
 * Caso de uso: el tripulante marca una indicación como entendida. Solo la
 * suya y solo cuando ya llegó a la nave.
 */
final readonly class MarkAdviceRead
{
    public function __construct(
        private FindAdviceRepository $findAdvice,
        private UpdateAdviceRepository $updateAdvice,
        private CurrentActor $currentActor,
    ) {}

    public function execute(int $adviceId): Advice
    {
        $advice = $this->findAdvice->findById($adviceId) ?? throw AlertProblem::adviceNotFound();

        if ($this->currentActor->claims()->astronautId !== $advice->astronautId) {
            throw AlertProblem::adviceNotYours();
        }

        $now = CarbonImmutable::now();

        if ($advice->arrivesAt->greaterThan($now)) {
            throw AlertProblem::adviceNotArrived();
        }

        return $this->updateAdvice->markRead($adviceId, $now);
    }
}
