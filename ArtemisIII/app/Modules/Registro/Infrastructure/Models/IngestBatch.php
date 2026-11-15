<?php

declare(strict_types=1);

namespace App\Modules\Registro\Infrastructure\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $batch_id
 * @property int $device_id
 * @property int $received
 * @property int $stored
 * @property int $duplicates
 * @property list<array{reading_id: string, error_code: string, message: string}> $rejected
 * @property CarbonImmutable $created_at
 */
final class IngestBatch extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['batch_id', 'device_id', 'received', 'stored', 'duplicates', 'rejected'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'device_id' => 'integer',
            'received' => 'integer',
            'stored' => 'integer',
            'duplicates' => 'integer',
            'rejected' => 'array',
            'created_at' => 'immutable_datetime',
        ];
    }
}
