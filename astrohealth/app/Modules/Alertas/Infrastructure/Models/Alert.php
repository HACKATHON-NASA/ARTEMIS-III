<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Infrastructure\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $astronaut_id
 * @property int|null $measurement_id
 * @property string $kind
 * @property string|null $indicator_code
 * @property float|null $value
 * @property string $level
 * @property string $message
 * @property string|null $category
 * @property string|null $note
 * @property string $source
 * @property int $delay_seconds
 * @property CarbonImmutable $sent_at
 * @property CarbonImmutable $ground_eta_at
 * @property CarbonImmutable|null $acknowledged_at
 * @property string|null $acknowledged_by_name
 * @property string|null $reply
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable|null $updated_at
 */
final class Alert extends Model
{
    protected $fillable = [
        'astronaut_id', 'measurement_id', 'kind', 'indicator_code', 'value', 'level', 'message', 'category', 'note',
        'source', 'delay_seconds', 'sent_at', 'ground_eta_at', 'acknowledged_at', 'acknowledged_by_name', 'reply',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'astronaut_id' => 'integer',
            'measurement_id' => 'integer',
            'value' => 'float',
            'delay_seconds' => 'integer',
            'sent_at' => 'immutable_datetime',
            'ground_eta_at' => 'immutable_datetime',
            'acknowledged_at' => 'immutable_datetime',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }
}
