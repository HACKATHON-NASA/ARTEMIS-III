<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Infrastructure\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $test_id
 * @property int $astronaut_id
 * @property int $day
 * @property CarbonImmutable $taken_at
 * @property int $score
 * @property string $status
 * @property string $band
 * @property string|null $detail
 * @property bool $flagged
 * @property array<string, mixed> $answers
 */
final class PsychResult extends Model
{
    /** Un resultado no se edita: no hay updated_at. */
    public const UPDATED_AT = null;

    protected $fillable = ['test_id', 'astronaut_id', 'day', 'taken_at', 'score', 'status', 'band', 'detail', 'flagged', 'answers'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'test_id' => 'integer',
            'astronaut_id' => 'integer',
            'day' => 'integer',
            'taken_at' => 'immutable_datetime',
            'score' => 'integer',
            'flagged' => 'boolean',
            'answers' => 'array',
        ];
    }
}
