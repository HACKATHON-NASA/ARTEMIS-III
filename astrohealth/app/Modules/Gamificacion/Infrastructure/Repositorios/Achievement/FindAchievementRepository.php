<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Infrastructure\Repositorios\Achievement;

use App\Modules\Gamificacion\Domain\Entidades\Achievement;
use App\Modules\Gamificacion\Infrastructure\Models\Achievement as AchievementModel;
use App\Modules\Gamificacion\Infrastructure\Models\AstronautAchievement;

/**
 * BUSCAR insignias y las que ganó cada tripulante. Solo lectura.
 */
final class FindAchievementRepository
{
    /**
     * @return list<Achievement>
     */
    public function all(): array
    {
        return array_values(AchievementModel::query()->orderBy('sort_order')->get()
            ->map(fn (AchievementModel $row): Achievement => new Achievement($row->id, $row->code, $row->name, $row->icon, $row->condition, $row->bonus, $row->target))
            ->all());
    }

    /**
     * Las que ganó un tripulante: id de la insignia → día de misión.
     *
     * @return array<int, int>
     */
    public function earnedBy(int $astronautId): array
    {
        /** @var array<int, int> $earned */
        $earned = AstronautAchievement::query()
            ->where('astronaut_id', $astronautId)
            ->pluck('earned_day', 'achievement_id')
            ->all();

        return $earned;
    }
}
