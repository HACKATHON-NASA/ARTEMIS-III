<?php

declare(strict_types=1);

namespace App\Modules\Dashboard;

use App\Shared\ModuleServiceProvider;

/**
 * M4 · Dashboard: las gráficas, las tendencias y la comparación de la
 * tripulación. No tiene tablas: lee por los contratos de M1, M2 y M6.
 */
final class DashboardServiceProvider extends ModuleServiceProvider
{
    protected function module(): string
    {
        return 'Dashboard';
    }
}
