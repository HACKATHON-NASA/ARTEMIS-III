<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Infrastructure\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $astronaut_id
 * @property int $achievement_id
 * @property int $earned_day
 * @property CarbonImmutable $earned_at
 */
final class AstronautAchievement extends Model
{
    public $timestamps = false;

    protected $fillable = ['astronaut_id', 'achievement_id', 'earned_day', 'earned_at'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['astronaut_id' => 'integer', 'achievement_id' => 'integer', 'earned_day' => 'integer', 'earned_at' => 'immutable_datetime'];
    }
}
