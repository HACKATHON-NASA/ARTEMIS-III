<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Infrastructure\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $astronaut_id
 * @property int|null $author_id
 * @property string $author_name
 * @property string $kind
 * @property string $text
 * @property bool $follow_up
 * @property CarbonImmutable|null $resolved_at
 * @property int|null $resolved_by_id
 * @property string|null $resolved_by_name
 * @property CarbonImmutable $created_at
 */
final class ClinicalNote extends Model
{
    /** Las notas no se editan: no hay updated_at. */
    public const UPDATED_AT = null;

    protected $fillable = ['astronaut_id', 'author_id', 'author_name', 'kind', 'text', 'follow_up', 'resolved_at', 'resolved_by_id', 'resolved_by_name'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'astronaut_id' => 'integer',
            'follow_up' => 'boolean',
            'resolved_at' => 'immutable_datetime',
            'created_at' => 'immutable_datetime',
        ];
    }
}
