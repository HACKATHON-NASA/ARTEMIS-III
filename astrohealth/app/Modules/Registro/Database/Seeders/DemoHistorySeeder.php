<?php

declare(strict_types=1);

namespace App\Modules\Registro\Database\Seeders;

use App\Modules\Entorno\Contratos\CrewMember;
use App\Modules\Entorno\Contratos\CrewQuery;
use App\Modules\Entorno\Contratos\MissionQuery;
use App\Modules\Evaluacion\Contratos\Evaluator;
use App\Modules\Registro\Domain\Entidades\Indicator;
use App\Modules\Registro\Domain\Enums\Source;
use App\Modules\Registro\Infrastructure\Repositorios\Indicator\FindIndicatorRepository;
use App\Modules\Registro\Infrastructure\Repositorios\Measurement\FindMeasurementRepository;
use App\Modules\Registro\Infrastructure\Repositorios\Measurement\InsertMeasurementRepository;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

/**
 * La historia diaria de Ares-1 para las gráficas de la demo (hasta que el
 * simulador envíe los 30 días): un valor por día, del día 1 a ayer, que
 * termina en el último valor de cada indicador (DemoReadingsSeeder). Cada
 * valor pasa por el motor de M2. Determinista: siempre los mismos datos.
 *
 * Solo fuera de producción, y solo para quien todavía no tiene historia.
 */
final class DemoHistorySeeder extends Seeder
{
    /** Cuánto se mueve cada indicador de un día a otro, y sus decimales. */
    private const NOISE = [
        'hr' => [4, 0], 'bp_sys' => [4, 0], 'bp_dia' => [3, 0], 'glucose' => [4, 0], 'temp' => [0.15, 1],
        'spo2' => [0.6, 0], 'sleep' => [0.35, 1], 'mood' => [0.45, 0], 'exercise_min' => [10, 0], 'radiation' => [0.05, 2],
    ];

    /** Las historias de la tripulación (en orden de id): [valor de partida, en cuántos días llegó al de hoy]. */
    private const STORIES = [
        ['bp_sys' => [117, 6], 'bp_dia' => [75, 6], 'sleep' => [7.5, 10], 'exercise_min' => [105, 8]],
        ['sleep' => [7.3, 14], 'mood' => [4.4, 9]],
        ['spo2' => [98, 5], 'glucose' => [91, 10], 'radiation' => [0.6, 6]],
    ];

    /** Una tormenta solar sube la radiación de toda la nave unos días antes de hoy. */
    private const SOLAR_EVENT_BOOST = 0.35;

    public function run(
        CrewQuery $crew,
        MissionQuery $missions,
        FindIndicatorRepository $indicators,
        FindMeasurementRepository $measurements,
        InsertMeasurementRepository $insert,
        Evaluator $evaluator,
    ): void {
        if (app()->isProduction()) {
            return;
        }

        $all = $crew->all();
        $members = array_values(array_filter($all, fn (CrewMember $member): bool => $member->missionId === ($all[0]->missionId ?? 0)));
        $now = CarbonImmutable::now()->utc();

        foreach ($members as $index => $member) {
            $start = $missions->startDateOf($member->missionId);
            $environment = $missions->environmentOf($member->missionId);
            $latest = $measurements->latestPerIndicator($member->id);

            if ($start === null || $environment === null || $latest === [] || $this->hasHistory($measurements, $member->id, $now)) {
                continue;
            }

            $today = (int) $start->utc()->startOfDay()->diffInDays($now->startOfDay()) + 1;
            $rows = [];

            foreach ($latest as $last) {
                $indicator = $indicators->byCode($last->indicatorCode);

                if ($indicator === null) {
                    continue;
                }

                for ($day = 1; $day < $today; $day++) {
                    $value = $this->valueOn($index, $indicator, $last->value, $day, $today);
                    $rows[] = [
                        'astronaut_id' => $member->id,
                        'indicator_id' => $indicator->id,
                        'value' => $value,
                        'status' => $evaluator->evaluate($indicator->code, $value, $environment),
                        'source' => Source::SimulatedReference->value,
                        'measured_at' => $start->utc()->startOfDay()->addDays($day - 1)->addHours(8),
                    ];
                }
            }

            $insert->insertMany($rows);
        }
    }

    /**
     * ¿Ya tiene historia? Algunos "últimos valores" son de ayer (el ejercicio,
     * el sueño), así que cuenta como historia solo lo de hace más de 2 días.
     */
    private function hasHistory(FindMeasurementRepository $measurements, int $astronautId, CarbonImmutable $now): bool
    {
        $series = $measurements->series($astronautId, $now->subDays(400));

        return $series !== [] && $series[0]->measuredAt->lessThan($now->subDays(2));
    }

    private function valueOn(int $crewIndex, Indicator $indicator, float $end, int $day, int $today): float
    {
        [$amplitude, $decimals] = self::NOISE[$indicator->code] ?? [0, 2];
        [$start, $span] = self::STORIES[$crewIndex][$indicator->code] ?? [$end, 1];

        $progress = min(1, max(0, ($day - ($today - $span)) / $span));
        $value = $start + ($end - $start) * $progress;
        $value += ($this->noise($crewIndex * 1000 + crc32($indicator->code) % 997 * 37 + $day) - 0.5) * 2 * $amplitude;

        if ($indicator->code === 'radiation' && $day >= $today - 4 && $day <= $today - 2) {
            $value += self::SOLAR_EVENT_BOOST;
        }

        $value = round($value, $decimals);

        if ($indicator->code === 'mood') {
            $value = min(5, max(1, $value));
        }

        if ($indicator->code === 'spo2') {
            $value = min(100, $value);
        }

        $floor = $indicator->code === 'radiation' ? 0.05 : $indicator->minPossible;

        return (float) min($indicator->maxPossible, max($floor, $value));
    }

    /** Un número entre 0 y 1, siempre el mismo para la misma semilla. */
    private function noise(int $seed): float
    {
        $x = sin($seed * 12.9898) * 43758.5453;

        return $x - floor($x);
    }
}
