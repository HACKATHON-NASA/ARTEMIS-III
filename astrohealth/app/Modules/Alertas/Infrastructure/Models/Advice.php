<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Infrastructure\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $astronaut_id
 * @property int|null $author_id
 * @property string $author_name
 * @property string $category
 * @property string $title
 * @property string $body
 * @property bool $important
 * @property int|null $valid_days
 * @property int $delay_seconds
 * @property CarbonImmutable $sent_at
 * @property CarbonImmutable $arrives_at
 * @property CarbonImmutable|null $read_at
 */
final class Advice extends Model
{
    protected $table = 'advice';

    protected $fillable = [
        'astronaut_id', 'author_id', 'author_name', 'category', 'title', 'body', 'important',
        'valid_days', 'delay_seconds', 'sent_at', 'arrives_at', 'read_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'astronaut_id' => 'integer',
            'important' => 'boolean',
            'valid_days' => 'integer',
            'delay_seconds' => 'integer',
            'sent_at' => 'immutable_datetime',
            'arrives_at' => 'immutable_datetime',
            'read_at' => 'immutable_datetime',
        ];
    }
}
