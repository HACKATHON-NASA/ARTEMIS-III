<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Application;

use App\Modules\Alertas\Application\DTOs\AdviceData;
use App\Modules\Alertas\Application\Servicios\CrewScope;
use App\Modules\Alertas\Domain\Entidades\Advice;
use App\Modules\Alertas\Domain\Excepciones\AlertProblem;
use App\Modules\Alertas\Domain\Reglas\GroundLink;
use App\Modules\Alertas\Infrastructure\Repositorios\Advice\InsertAdviceRepository;
use App\Modules\Identidad\Contratos\CurrentActor;
use Carbon\CarbonImmutable;

/**
 * Caso de uso: un médico (advice.send) le da una indicación a un tripulante
 * de su misión. Viaja a la nave con el retraso del entorno. El autor sale de la sesión.
 */
final readonly class SendAdvice
{
    public function __construct(
        private CrewScope $scope,
        private CurrentActor $currentActor,
        private InsertAdviceRepository $insertAdvice,
    ) {}

    public function execute(int $astronautId, AdviceData $data): Advice
    {
        if (! $this->scope->canSee($astronautId)) {
            throw AlertProblem::notYourPatient();
        }

        $author = $this->currentActor->get();
        $delay = $this->scope->delayFor($astronautId);
        $sentAt = CarbonImmutable::now();

        return $this->insertAdvice->insert([
            'astronaut_id' => $astronautId,
            'author_id' => $author->actorId(),
            'author_name' => $author->actorName(),
            'category' => $data->category->value,
            'title' => trim($data->title),
            'body' => trim($data->body),
            'important' => $data->important,
            'valid_days' => $data->validDays,
            'delay_seconds' => $delay,
            'sent_at' => $sentAt,
            'arrives_at' => GroundLink::arrival($sentAt, $delay),
        ]);
    }
}
