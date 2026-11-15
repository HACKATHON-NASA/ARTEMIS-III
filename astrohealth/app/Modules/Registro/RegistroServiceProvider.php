<?php

declare(strict_types=1);

namespace App\Modules\Registro;

use App\Modules\Entorno\Domain\Eventos\MissionEnvironmentChanged;
use App\Modules\Evaluacion\Domain\Eventos\ThresholdUpdated;
use App\Modules\Registro\Contratos\IndicatorCatalog;
use App\Modules\Registro\Contratos\MeasurementQuery;
use App\Modules\Registro\Infrastructure\Listeners\OnMissionEnvironmentChanged;
use App\Modules\Registro\Infrastructure\Listeners\OnThresholdUpdated;
use App\Modules\Registro\Infrastructure\MeasurementQueryAdapter;
use App\Modules\Registro\Infrastructure\Repositorios\Indicator\FindIndicatorRepository;
use App\Shared\ModuleServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;

/**
 * M1 · Registro: dueño de indicators, measurements e ingest_batches. La
 * puerta de entrada de los datos de salud (a mano y por las máquinas).
 */
final class RegistroServiceProvider extends ModuleServiceProvider
{
    protected function module(): string
    {
        return 'Registro';
    }

    public function register(): void
    {
        $this->app->bind(IndicatorCatalog::class, FindIndicatorRepository::class);
        $this->app->bind(MeasurementQuery::class, MeasurementQueryAdapter::class);
    }

    public function boot(): void
    {
        parent::boot();

        Event::listen(MissionEnvironmentChanged::class, OnMissionEnvironmentChanged::class);
        Event::listen(ThresholdUpdated::class, OnThresholdUpdated::class);

        // Las máquinas envían muchos lotes: un límite amplio, por dispositivo.
        RateLimiter::for('api-ingest', fn (Request $request): Limit => Limit::perMinute(120)
            ->by('device:'.($request->user()?->getAuthIdentifier() ?? $request->ip())));
    }
}
