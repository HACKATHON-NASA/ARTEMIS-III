<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Application;

use App\Modules\Alertas\Application\Servicios\CrewScope;
use App\Modules\Alertas\Domain\Enums\AlertKind;
use App\Modules\Alertas\Domain\Enums\AlertLevel;
use App\Modules\Alertas\Domain\Enums\AlertSource;
use App\Modules\Alertas\Domain\Reglas\GroundLink;
use App\Modules\Alertas\Infrastructure\Repositorios\Alert\InsertAlertRepository;
use App\Modules\Evaluacion\Domain\Eventos\PsychCriticalAnswered;
use Carbon\CarbonImmutable;

/**
 * Caso de uso (seguridad clínica): un test marcó una respuesta de riesgo
 * (PHQ-9 ítem 9). Sale a Tierra una petición de ayuda URGENTE, en rojo, a
 * nombre del tripulante. No lleva la respuesta: el médico la ve en Resultados.
 */
final readonly class CreatePsychRiskAlert
{
    public function __construct(
        private CrewScope $scope,
        private InsertAlertRepository $insertAlert,
    ) {}

    public function execute(PsychCriticalAnswered $event): void
    {
        $sentAt = CarbonImmutable::now();
        $delay = $this->scope->delayFor($event->astronautId);
        $category = 'Ánimo o estrés';

        $this->insertAlert->insert([
            'astronaut_id' => $event->astronautId,
            'kind' => AlertKind::Help->value,
            'level' => AlertLevel::Red->value,
            'message' => mb_substr("{$this->scope->firstName($event->astronautId)} pide ayuda · {$category}", 0, 255),
            'category' => $category,
            'note' => "Alerta automática: el test {$event->testCode} marcó una respuesta de riesgo. Contactar con prioridad.",
            'source' => AlertSource::Astronaut->value,
            'delay_seconds' => $delay,
            'sent_at' => $sentAt,
            'ground_eta_at' => GroundLink::arrival($sentAt, $delay),
        ]);
    }
}
