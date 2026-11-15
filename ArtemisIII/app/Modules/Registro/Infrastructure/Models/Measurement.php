<?php

declare(strict_types=1);

namespace App\Modules\Registro\Infrastructure\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $astronaut_id
 * @property int $indicator_id
 * @property float $value
 * @property string $status
 * @property string $source
 * @property CarbonImmutable $measured_at
 * @property string|null $reading_id
 * @property int|null $ingest_batch_id
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property Indicator $indicator
 */
final class Measurement extends Model
{
    protected $fillable = ['astronaut_id', 'indicator_id', 'value', 'status', 'source', 'measured_at', 'reading_id', 'ingest_batch_id'];

    /**
     * @return BelongsTo<Indicator, $this>
     */
    public function indicator(): BelongsTo
    {
        return $this->belongsTo(Indicator::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'astronaut_id' => 'integer',
            'indicator_id' => 'integer',
            'value' => 'float',
            'measured_at' => 'immutable_datetime',
            'ingest_batch_id' => 'integer',
        ];
    }
}
