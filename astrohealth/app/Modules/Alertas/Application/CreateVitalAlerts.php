<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Application;

use App\Modules\Alertas\Application\Servicios\CrewScope;
use App\Modules\Alertas\Domain\Enums\AlertKind;
use App\Modules\Alertas\Domain\Enums\AlertSource;
use App\Modules\Alertas\Domain\Reglas\GroundLink;
use App\Modules\Alertas\Infrastructure\Configuracion\GroundDelays;
use App\Modules\Alertas\Infrastructure\Repositorios\Alert\InsertAlertRepository;
use App\Modules\Registro\Contratos\IndicatorCatalog;
use App\Modules\Registro\Contratos\IndicatorInfo;
use App\Modules\Registro\Domain\Eventos\MeasurementsRecorded;
use App\Modules\Registro\Domain\Eventos\RecordedMeasurement;
use Carbon\CarbonImmutable;

/**
 * Caso de uso: cada valor en amarillo o en rojo que se guarda crea su
 * alerta, que sale sola hacia Tierra (una por medición, nunca dos).
 */
final readonly class CreateVitalAlerts
{
    public function __construct(
        private CrewScope $scope,
        private InsertAlertRepository $insertAlert,
        private IndicatorCatalog $indicators,
        private GroundDelays $delays,
    ) {}

    public function execute(MeasurementsRecorded $event): int
    {
        // Solo lo reciente: la historia que carga el simulador no llena el centro de avisos viejos.
        $since = CarbonImmutable::now()->subHours($this->delays->vitalMaxAgeHours());
        $flagged = array_filter($event->items, fn (RecordedMeasurement $item): bool => ($item->status === 'yellow' || $item->status === 'red')
            && $item->measuredAt->greaterThanOrEqualTo($since));

        if ($flagged === []) {
            return 0;
        }

        $catalog = [];

        foreach ($this->indicators->all() as $indicator) {
            $catalog[$indicator->code] = $indicator;
        }

        $delay = $this->scope->delayFor($event->astronautId);
        $created = 0;

        foreach ($flagged as $item) {
            if ($this->insertAlert->existsForMeasurement($item->measurementId)) {
                continue;
            }

            $info = $catalog[$item->indicator] ?? new IndicatorInfo($item->indicator, $item->indicator, '', $item->bodyPart);
            $sentAt = $item->measuredAt;

            $this->insertAlert->insert([
                'astronaut_id' => $event->astronautId,
                'measurement_id' => $item->measurementId,
                'kind' => AlertKind::Vital->value,
                'indicator_code' => $item->indicator,
                'value' => $item->value,
                'level' => $item->status,
                'message' => GroundLink::vitalMessage($info->name, $item->value, $info->unit, $item->status === 'red'),
                'source' => AlertSource::fromMeasurement($item->source)->value,
                'delay_seconds' => $delay,
                'sent_at' => $sentAt,
                'ground_eta_at' => GroundLink::arrival($sentAt, $delay),
            ]);
            $created++;
        }

        return $created;
    }
}
