<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Infrastructure\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $astronaut_id
 * @property int $points
 * @property string $reason
 * @property string $reference
 * @property int $day
 * @property CarbonImmutable $created_at
 */
final class PointLog extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'points_log';

    protected $fillable = ['astronaut_id', 'points', 'reason', 'reference', 'day', 'created_at'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['astronaut_id' => 'integer', 'points' => 'integer', 'day' => 'integer', 'created_at' => 'immutable_datetime'];
    }
}
