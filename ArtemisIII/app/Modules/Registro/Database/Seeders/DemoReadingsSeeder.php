<?php

declare(strict_types=1);

namespace App\Modules\Registro\Database\Seeders;

use App\Modules\Entorno\Contratos\CrewMember;
use App\Modules\Entorno\Contratos\CrewQuery;
use App\Modules\Evaluacion\Contratos\Evaluator;
use App\Modules\Registro\Application\Servicios\AfterSaving;
use App\Modules\Registro\Application\Servicios\CrewContext;
use App\Modules\Registro\Domain\Enums\Source;
use App\Modules\Registro\Infrastructure\Repositorios\Indicator\FindIndicatorRepository;
use App\Modules\Registro\Infrastructure\Repositorios\Measurement\FindMeasurementRepository;
use App\Modules\Registro\Infrastructure\Repositorios\Measurement\InsertMeasurementRepository;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

/**
 * Los últimos valores de la tripulación de Ares-1 para la demo (hasta que
 * el simulador envíe los 30 días). Pasan por el motor de M2 como cualquier
 * medición: el color y el puntaje los calcula el backend.
 *
 * Solo fuera de producción. Solo agrega los indicadores que el tripulante
 * todavía no tiene, así no pisa lo registrado de verdad.
 */
final class DemoReadingsSeeder extends Seeder
{
    /** [indicador, valor, minutos atrás] por tripulante de Ares-1 (en orden de id). */
    private const READINGS = [
        // Elena Vargas: la presión alta de la historia de la demo.
        [['hr', 88, 6], ['bp_sys', 148, 6], ['bp_dia', 94, 6], ['sleep', 6.2, 420], ['mood', 4, 90], ['spo2', 97, 6], ['temp', 36.7, 6], ['glucose', 92, 180], ['exercise_min', 70, 1440], ['radiation', 0.52, 60]],
        // Kenji Mori: el sueño que viene cayendo.
        [['hr', 64, 30], ['bp_sys', 112, 30], ['bp_dia', 72, 30], ['sleep', 5.8, 400], ['mood', 3, 120], ['spo2', 98, 30], ['temp', 36.5, 30], ['glucose', 88, 200], ['exercise_min', 110, 600], ['radiation', 0.48, 60]],
        // Amara Okafor: oxígeno, glucosa y radiación en precaución.
        [['hr', 76, 15], ['bp_sys', 118, 15], ['bp_dia', 76, 15], ['sleep', 7.4, 380], ['mood', 4, 60], ['spo2', 93, 15], ['temp', 36.9, 15], ['glucose', 104, 240], ['exercise_min', 95, 900], ['radiation', 0.94, 45]],
        // Lucas Ferreira: todo en orden.
        [['hr', 62, 10], ['bp_sys', 108, 10], ['bp_dia', 70, 10], ['sleep', 7.8, 360], ['mood', 5, 40], ['spo2', 99, 10], ['temp', 36.6, 10], ['glucose', 86, 150], ['exercise_min', 120, 500], ['radiation', 0.5, 60]],
    ];

    public function run(
        CrewQuery $crew,
        CrewContext $context,
        FindIndicatorRepository $indicators,
        FindMeasurementRepository $measurements,
        InsertMeasurementRepository $insert,
        Evaluator $evaluator,
        AfterSaving $afterSaving,
    ): void {
        if (app()->isProduction()) {
            $this->command->warn('DemoReadingsSeeder no carga datos de demo en producción.');

            return;
        }

        $members = array_values(array_filter($crew->all(), fn (CrewMember $member): bool => $member->missionId === ($crew->all()[0]->missionId ?? 0)));
        $now = CarbonImmutable::now();

        foreach (self::READINGS as $index => $readings) {
            $member = $members[$index] ?? null;

            if ($member === null) {
                continue;
            }

            [, $environment] = $context->of($member->id);
            $existing = array_map(fn ($m): string => $m->indicatorCode, $measurements->latestPerIndicator($member->id));
            $rows = [];

            foreach ($readings as [$code, $value, $minutesAgo]) {
                $indicator = $indicators->byCode($code);

                if ($indicator === null || in_array($code, $existing, true)) {
                    continue;
                }

                $rows[] = [
                    'astronaut_id' => $member->id,
                    'indicator_id' => $indicator->id,
                    'value' => (float) $value,
                    'status' => $evaluator->evaluate($code, (float) $value, $environment),
                    'source' => Source::SimulatedReference->value,
                    'measured_at' => $now->subMinutes($minutesAgo),
                ];
            }

            if ($rows !== []) {
                $insert->insertMany($rows);
                $afterSaving->reassess($member->id);
            }
        }
    }
}
