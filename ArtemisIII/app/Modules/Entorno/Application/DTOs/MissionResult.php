<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Application\DTOs;

use App\Modules\Entorno\Contratos\CrewMember;
use App\Modules\Entorno\Domain\Entidades\Mission;
use App\Modules\Entorno\Domain\Enums\MissionStatus;
use Carbon\CarbonImmutable;

/**
 * Una misión con lo que sale de sus fechas (estado, día, fin) y su tripulación.
 */
final readonly class MissionResult
{
    /**
     * @param  list<CrewMember>  $crew
     */
    public function __construct(
        public Mission $mission,
        public MissionStatus $status,
        public ?int $currentDay,
        public CarbonImmutable $endDate,
        public array $crew,
    ) {}
}
