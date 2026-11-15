<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Http\Controllers\Api;

use App\Modules\Dashboard\Application\GetCrewSeries;
use App\Modules\Dashboard\Http\Requests\CrewRequest;
use App\Modules\Dashboard\Http\Resources\DayPoints;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * GET /api/v1/crew/series?astronauts=&indicator=&range=: la serie de un
 * indicador para cada tripulante, con las bandas del semáforo.
 */
final class CrewSeriesController
{
    public function __invoke(CrewRequest $request, GetCrewSeries $getSeries): JsonResponse
    {
        $result = $getSeries->execute($request->astronautIds(), $request->indicator(), $request->days());

        return ApiResponse::ok([
            'bands' => $result['bands'],
            'series' => array_map(
                fn (array $row): array => ['astronaut_id' => $row['astronaut_id'], 'points' => DayPoints::toArray($row['points'])],
                $result['series'],
            ),
        ]);
    }
}
