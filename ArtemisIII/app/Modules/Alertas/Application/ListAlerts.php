<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Application;

use App\Modules\Alertas\Application\Servicios\CrewScope;
use App\Modules\Alertas\Domain\Entidades\Alert;
use App\Modules\Alertas\Infrastructure\Repositorios\Alert\FindAlertRepository;

/**
 * Caso de uso: las alertas que la persona puede ver (las suyas, las de la
 * tripulación de su misión o todas), de la más reciente a la más antigua.
 */
final readonly class ListAlerts
{
    public function __construct(
        private CrewScope $scope,
        private FindAlertRepository $findAlert,
    ) {}

    /**
     * @return list<Alert>
     */
    public function execute(?int $astronautId, ?int $missionId): array
    {
        return $this->findAlert->ofAstronauts($this->scope->visible($astronautId, $missionId));
    }
}
