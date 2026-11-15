<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $kind
 * @property string $description
 * @property string $instructions
 * @property int $frequency_days
 * @property string $unit
 * @property list<array{label: string, points: int}> $scale
 * @property list<array{id: string, text: string, reverse: bool, critical: bool}> $questions
 * @property list<array{max: int, label: string, status: string}> $bands
 * @property string|null $source
 */
final class PsychTest extends Model
{
    protected $fillable = ['code', 'name', 'kind', 'description', 'instructions', 'frequency_days', 'unit', 'scale', 'questions', 'bands', 'source'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'frequency_days' => 'integer',
            'scale' => 'array',
            'questions' => 'array',
            'bands' => 'array',
        ];
    }
}
