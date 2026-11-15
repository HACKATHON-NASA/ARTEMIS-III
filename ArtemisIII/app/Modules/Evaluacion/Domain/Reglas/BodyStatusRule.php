<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Domain\Reglas;

use App\Modules\Evaluacion\Domain\Enums\Status;

/**
 * Regla: el estado de cada zona del cuerpo es el PEOR de sus indicadores
 * (si la presión está en rojo, el corazón está en rojo aunque el pulso esté bien).
 */
final class BodyStatusRule
{
    /**
     * @param  list<array{zone: string, status: Status}>  $readings
     * @return array<string, Status> zona → peor estado
     */
    public static function byZone(array $readings): array
    {
        $zones = [];

        foreach ($readings as ['zone' => $zone, 'status' => $status]) {
            $current = $zones[$zone] ?? null;

            if ($current === null || $status->severity() > $current->severity()) {
                $zones[$zone] = $status;
            }
        }

        return $zones;
    }
}
