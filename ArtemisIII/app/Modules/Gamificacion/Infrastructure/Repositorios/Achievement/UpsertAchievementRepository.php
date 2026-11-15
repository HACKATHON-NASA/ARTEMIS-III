<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Infrastructure\Repositorios\Achievement;

use App\Modules\Gamificacion\Infrastructure\Models\Achievement as AchievementModel;
use App\Modules\Gamificacion\Infrastructure\Models\AstronautAchievement;
use Carbon\CarbonImmutable;

/**
 * INSERTAR O ACTUALIZAR insignias (seeder) y registrar las que gana cada tripulante.
 */
final class UpsertAchievementRepository
{
    /**
     * @param  array{code: string, name: string, icon: string, condition: string, bonus: int, target: int, sort_order: int}  $data
     */
    public function upsertByCode(array $data): void
    {
        AchievementModel::query()->updateOrCreate(['code' => $data['code']], $data);
    }

    /** Una insignia se gana una sola vez: si ya la tenía, no hace nada. Devuelve si es nueva. */
    public function earn(int $astronautId, int $achievementId, int $day, CarbonImmutable $at): bool
    {
        return AstronautAchievement::query()->insertOrIgnore([
            'astronaut_id' => $astronautId,
            'achievement_id' => $achievementId,
            'earned_day' => $day,
            'earned_at' => $at,
        ]) > 0;
    }
}
