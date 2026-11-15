<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Infrastructure\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $astronaut_id
 * @property int $score
 * @property array<string, string> $body_status
 * @property string $summary
 * @property CarbonImmutable $created_at
 */
final class Assessment extends Model
{
    /** Cada evaluación es una fila nueva: no hay updated_at. */
    public const UPDATED_AT = null;

    protected $fillable = ['astronaut_id', 'score', 'body_status', 'summary'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'astronaut_id' => 'integer',
            'score' => 'integer',
            'body_status' => 'array',
            'created_at' => 'immutable_datetime',
        ];
    }
}
