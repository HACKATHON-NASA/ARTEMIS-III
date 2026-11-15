<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Application;

use App\Modules\Dashboard\Application\Servicios\DailySeries;
use App\Modules\Registro\Contratos\IndicatorCatalog;
use App\Modules\Registro\Contratos\MeasurementQuery;
use Illuminate\Contracts\Auth\Access\Gate;

/**
 * Caso de uso: la comparación de una tripulación (Panel de salud y
 * Tripulación). Solo con los tripulantes que la persona puede ver.
 *
 * - snapshot: el último valor de cada indicador y cuántos piden atención.
 * - doses: la radiación acumulada en la misión (suma de un valor por día).
 * - changes: lo que cambió de color en los últimos días (lo que empeoró, primero).
 */
final readonly class GetCrewOverview
{
    private const CHANGE_DAYS = 3;

    private const RANK = ['green' => 0, 'yellow' => 1, 'red' => 2];

    public function __construct(
        private DailySeries $series,
        private MeasurementQuery $measurements,
        private IndicatorCatalog $indicators,
        private Gate $gate,
    ) {}

    /**
     * @param  list<int>  $astronautIds
     * @return array{snapshot: list<array<string, mixed>>, doses: list<array<string, mixed>>, changes: list<array<string, mixed>>}
     */
    public function execute(array $astronautIds): array
    {
        $visible = array_values(array_filter($astronautIds, fn (int $id): bool => $this->gate->allows('health.read', [$id])));
        $codes = array_map(fn ($indicator): string => $indicator->code, $this->indicators->all());

        $snapshot = [];
        $doses = [];
        $changes = [];

        foreach ($visible as $id) {
            $values = [];

            foreach ($this->measurements->latestPerIndicator($id) as $m) {
                $values[$m->indicator] = ['value' => $m->value, 'status' => $m->status];
            }

            $snapshot[] = [
                'astronaut_id' => $id,
                'values' => $values,
                'attention' => count(array_filter($values, fn (array $v): bool => $v['status'] !== 'green')),
            ];

            $radiation = $this->series->of($id, 'radiation');
            $doses[] = [
                'astronaut_id' => $id,
                'total' => round(array_sum(array_map(fn ($p): float => $p->value, $radiation)), 2),
                'days' => count($radiation),
            ];

            $today = $this->series->today($id);

            foreach ($codes as $code) {
                $recent = $this->series->of($id, $code, $today - self::CHANGE_DAYS);
                $first = $recent[0] ?? null;
                $last = $recent === [] ? null : $recent[count($recent) - 1];

                if ($first !== null && $last !== null && $first->status !== $last->status) {
                    $changes[] = [
                        'astronaut_id' => $id,
                        'indicator' => $code,
                        'from' => ['value' => $first->value, 'status' => $first->status],
                        'to' => ['value' => $last->value, 'status' => $last->status],
                        'worse' => (self::RANK[$last->status] ?? 0) > (self::RANK[$first->status] ?? 0),
                    ];
                }
            }
        }

        usort($changes, fn (array $a, array $b): int => ((int) $b['worse'] <=> (int) $a['worse'])
            ?: ((self::RANK[$b['to']['status']] ?? 0) <=> (self::RANK[$a['to']['status']] ?? 0)));

        return ['snapshot' => $snapshot, 'doses' => $doses, 'changes' => $changes];
    }
}
