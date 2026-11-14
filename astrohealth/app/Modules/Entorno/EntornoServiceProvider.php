<?php

declare(strict_types=1);

namespace App\Modules\Entorno;

use App\Modules\Entorno\Contratos\CrewQuery;
use App\Modules\Entorno\Contratos\MissionQuery;
use App\Modules\Entorno\Infrastructure\Repositorios\AstronautRepository;
use App\Modules\Entorno\Infrastructure\Repositorios\MissionRepository;
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
        $this->app->bind(CrewQuery::class, AstronautRepository::class);
        $this->app->bind(MissionQuery::class, MissionRepository::class);
    }
}
