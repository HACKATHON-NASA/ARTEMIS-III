<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Application;

use App\Modules\Alertas\Application\Servicios\CrewScope;
use App\Modules\Alertas\Domain\Entidades\Advice;
use App\Modules\Alertas\Infrastructure\Repositorios\Advice\FindAdviceRepository;

/**
 * Caso de uso: las indicaciones que la persona puede ver (las suyas, las de
 * su misión o todas), de la más reciente a la más antigua.
 */
final readonly class ListAdvice
{
    public function __construct(
        private CrewScope $scope,
        private FindAdviceRepository $findAdvice,
    ) {}

    /**
     * @return list<Advice>
     */
    public function execute(?int $astronautId, ?int $missionId): array
    {
        return $this->findAdvice->ofAstronauts($this->scope->visible($astronautId, $missionId));
    }
}
