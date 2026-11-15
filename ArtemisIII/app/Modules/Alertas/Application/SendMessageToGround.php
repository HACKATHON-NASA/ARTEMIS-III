<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Application;

use App\Modules\Alertas\Application\Servicios\CrewScope;
use App\Modules\Alertas\Domain\Entidades\Alert;
use App\Modules\Alertas\Domain\Enums\AlertKind;
use App\Modules\Alertas\Domain\Enums\AlertSource;
use App\Modules\Alertas\Domain\Excepciones\AlertProblem;
use App\Modules\Alertas\Domain\Reglas\GroundLink;
use App\Modules\Alertas\Infrastructure\Repositorios\Alert\InsertAlertRepository;
use App\Modules\Identidad\Contratos\CurrentActor;
use Carbon\CarbonImmutable;

/**
 * Caso de uso: el tripulante manda un SOS, pide ayuda o cuenta una novedad.
 * Sale hacia Tierra al instante y llega con el retraso de su entorno.
 * Quien lo manda sale de la sesión (nunca del cuerpo de la petición).
 */
final readonly class SendMessageToGround
{
    public function __construct(
        private CurrentActor $currentActor,
        private CrewScope $scope,
        private InsertAlertRepository $insertAlert,
    ) {}

    public function execute(AlertKind $kind, ?string $category, string $note, bool $urgent): Alert
    {
        $astronautId = $this->currentActor->claims()->astronautId ?? throw AlertProblem::notCrew();
        $name = $this->scope->firstName($astronautId);
        $delay = $this->scope->delayFor($astronautId);
        $sentAt = CarbonImmutable::now();
        $category = $category === null || trim($category) === '' ? null : trim($category);

        $message = match ($kind) {
            AlertKind::Sos => "SOS de {$name}",
            AlertKind::Help => "{$name} pide ayuda".($category === null ? '' : " · {$category}"),
            default => "Novedad de {$name}",
        };

        return $this->insertAlert->insert([
            'astronaut_id' => $astronautId,
            'kind' => $kind->value,
            'level' => $kind->messageLevel($urgent)->value,
            'message' => mb_substr($message, 0, 255),
            'category' => $category,
            'note' => trim($note) === '' ? null : trim($note),
            'source' => AlertSource::Astronaut->value,
            'delay_seconds' => $delay,
            'sent_at' => $sentAt,
            'ground_eta_at' => GroundLink::arrival($sentAt, $delay),
        ]);
    }
}
