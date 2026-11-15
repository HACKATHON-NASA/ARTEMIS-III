<?php

declare(strict_types=1);

namespace App\Modules\Recordatorios\Infrastructure\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $astronaut_id
 * @property string $type
 * @property string $title
 * @property string $time
 * @property bool $active
 * @property CarbonImmutable|null $last_done_at
 */
final class Reminder extends Model
{
    protected $fillable = ['astronaut_id', 'type', 'title', 'time', 'active', 'last_done_at'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'astronaut_id' => 'integer',
            'active' => 'boolean',
            'last_done_at' => 'immutable_datetime',
        ];
    }
}
