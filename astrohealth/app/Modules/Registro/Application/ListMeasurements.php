<?php

declare(strict_types=1);

namespace App\Modules\Registro\Application;

use App\Modules\Registro\Domain\Entidades\Measurement;
use App\Modules\Registro\Infrastructure\Repositorios\Measurement\FindMeasurementRepository;
use Carbon\CarbonImmutable;

/**
 * Caso de uso: las mediciones de un astronauta en un rango (24 h, 7 o 30
 * días), de todos los indicadores o de uno. Quién puede verlas lo decide la
 * ruta con el Gate health.read.
 */
final readonly class ListMeasurements
{
    public const RANGES = ['24h' => 1, '7d' => 7, '30d' => 30];

    public function __construct(private FindMeasurementRepository $findMeasurement) {}

    /**
     * @return list<Measurement>
     */
    public function execute(int $astronautId, string $range, ?string $indicatorCode): array
    {
        $from = CarbonImmutable::now()->subDays(self::RANGES[$range] ?? 7);

        return $this->findMeasurement->series($astronautId, $from, $indicatorCode);
    }
}
