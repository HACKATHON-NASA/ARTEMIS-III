<?php

declare(strict_types=1);

namespace App\Modules\Registro\Infrastructure;

use App\Modules\Registro\Contratos\MeasurementQuery;
use App\Modules\Registro\Contratos\MeasurementView;
use App\Modules\Registro\Domain\Entidades\Measurement;
use App\Modules\Registro\Infrastructure\Repositorios\Measurement\FindMeasurementRepository;
use Carbon\CarbonImmutable;

/**
 * Implementa el contrato MeasurementQuery con el repositorio de lectura:
 * convierte las entidades de M1 en el DTO público.
 */
final readonly class MeasurementQueryAdapter implements MeasurementQuery
{
    public function __construct(private FindMeasurementRepository $find) {}

    public function latestPerIndicator(int $astronautId): array
    {
        return array_map(self::view(...), $this->find->latestPerIndicator($astronautId));
    }

    public function series(int $astronautId, CarbonImmutable $from, ?string $indicatorCode = null): array
    {
        return array_map(self::view(...), $this->find->series($astronautId, $from, $indicatorCode));
    }

    private static function view(Measurement $m): MeasurementView
    {
        return new MeasurementView($m->id, $m->astronautId, $m->indicatorCode, $m->value, $m->unit, $m->status, $m->bodyPart, $m->measuredAt, $m->source);
    }
}
