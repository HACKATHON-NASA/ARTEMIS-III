<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Application;

use App\Modules\Alertas\Application\Servicios\CrewScope;
use App\Modules\Alertas\Domain\Entidades\Alert;
use App\Modules\Alertas\Domain\Excepciones\AlertProblem;
use App\Modules\Alertas\Infrastructure\Repositorios\Alert\FindAlertRepository;
use App\Modules\Alertas\Infrastructure\Repositorios\Alert\UpdateAlertRepository;
use App\Modules\Identidad\Contratos\CurrentActor;
use Carbon\CarbonImmutable;

/**
 * Caso de uso: Tierra atiende una alerta de su misión y responde (la
 * respuesta tarda lo mismo en volver a la nave). Se atiende una sola vez.
 */
final readonly class AcknowledgeAlert
{
    public function __construct(
        private FindAlertRepository $findAlert,
        private UpdateAlertRepository $updateAlert,
        private CrewScope $scope,
        private CurrentActor $currentActor,
    ) {}

    public function execute(int $alertId, ?string $reply): Alert
    {
        $alert = $this->findAlert->findById($alertId) ?? throw AlertProblem::notFound();

        if (! $this->scope->canSee($alert->astronautId)) {
            throw AlertProblem::notYourPatient();
        }

        if ($alert->acknowledgedAt !== null) {
            throw AlertProblem::alreadyAttended();
        }

        $reply = $reply === null || trim($reply) === '' ? null : trim($reply);

        return $this->updateAlert->acknowledge($alertId, CarbonImmutable::now(), $this->currentActor->get()->actorName(), $reply);
    }
}
