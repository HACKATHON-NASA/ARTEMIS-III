<?php

declare(strict_types=1);

namespace App\Modules\Entorno;

use App\Modules\Entorno\Contratos\CrewQuery;
use App\Modules\Entorno\Contratos\MissionQuery;
use App\Modules\Entorno\Infrastructure\Repositorios\Astronaut\FindAstronautRepository;
use App\Modules\Entorno\Infrastructure\Repositorios\Mission\FindMissionRepository;
use App\Shared\ModuleServiceProvider;

/**
 * M6 · Entorno de misión: dueño de missions y astronauts.
 */
final class EntornoServiceProvider extends ModuleServiceProvider
{
    protected function module(): string
    {
        return 'Entorno';
    }

    public function register(): void
    {
        $this->app->bind(CrewQuery::class, FindAstronautRepository::class);
        $this->app->bind(MissionQuery::class, FindMissionRepository::class);
    }
}
