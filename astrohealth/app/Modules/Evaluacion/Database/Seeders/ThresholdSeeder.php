<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Database\Seeders;

use App\Modules\Evaluacion\Domain\Reglas\RadiationThreshold;
use App\Modules\Evaluacion\Domain\ValueObjects\Threshold;
use App\Modules\Evaluacion\Infrastructure\Repositorios\Threshold\UpsertThresholdRepository;
use Illuminate\Database\Seeder;
use RuntimeException;

/**
 * Los umbrales del plan §2 en los 4 entornos. La radiación se calcula con
 * la dosis de referencia de cada entorno (config/environments.php).
 *
 * Se puede correr varias veces: solo crea lo que falta, así no pisa lo que
 * los médicos de vuelo cambiaron en la Guía de salud.
 *
 * Valores de referencia para la demo, no criterio médico.
 */
final class ThresholdSeeder extends Seeder
{
    /** [verde mín, verde máx, amarillo mín, amarillo máx] */
    private const BASE = [
        'hr' => [60, 100, 50, 120],
        'bp_sys' => [90, 119, 90, 139],
        'bp_dia' => [60, 79, 60, 89],
        'glucose' => [70, 99, 60, 125],
        'temp' => [36.1, 37.2, 35.5, 38.0],
        'spo2' => [95, 100, 90, 100],
        'sleep' => [7, 9, 6, 24],
        'mood' => [4, 5, 3, 5],
        'exercise_min' => [90, 600, 60, 600],
    ];

    public function run(UpsertThresholdRepository $upsert): void
    {
        $environments = config('environments');

        if (! is_array($environments) || $environments === []) {
            throw new RuntimeException('ThresholdSeeder necesita config/environments.php.');
        }

        foreach ($environments as $environment => $data) {
            $environment = (string) $environment;

            foreach (self::BASE as $code => [$greenMin, $greenMax, $yellowMin, $yellowMax]) {
                $upsert->createIfMissing(new Threshold($code, $environment, (float) $greenMin, (float) $greenMax, (float) $yellowMin, (float) $yellowMax));
            }

            $dose = is_array($data) ? ($data['radiation_msv_day'] ?? null) : null;

            if (! is_int($dose) && ! is_float($dose)) {
                throw new RuntimeException("config/environments.php: falta radiation_msv_day en \"{$environment}\".");
            }

            $upsert->createIfMissing(RadiationThreshold::for($environment, (float) $dose));
        }
    }
}
