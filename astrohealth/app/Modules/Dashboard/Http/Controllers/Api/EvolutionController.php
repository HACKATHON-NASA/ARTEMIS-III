<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Http\Controllers\Api;

use App\Modules\Dashboard\Application\GetEvolution;
use App\Modules\Dashboard\Http\Resources\DayPoints;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * GET /api/v1/astronauts/{astronaut}/evolution: la historia, la tendencia y
 * las bandas de cada indicador (Gate health.read).
 */
final class EvolutionController
{
    public function __invoke(int $astronaut, GetEvolution $getEvolution): JsonResponse
    {
        return ApiResponse::ok(array_map(
            fn (array $item): array => [...$item, 'points' => DayPoints::toArray($item['points'])],
            $getEvolution->execute($astronaut),
        ));
    }
}
