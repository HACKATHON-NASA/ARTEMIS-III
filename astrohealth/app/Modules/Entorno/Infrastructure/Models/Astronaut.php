<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Infrastructure\Models;

use App\Modules\Entorno\Database\Factories\AstronautFactory;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $mission_id
 * @property string $name
 * @property string $crew_role
 * @property string|null $avatar
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
final class Astronaut extends Model
{
    /** @use HasFactory<AstronautFactory> */
    use HasFactory;

    protected $fillable = ['mission_id', 'name', 'crew_role', 'avatar'];

    /**
     * @return BelongsTo<Mission, $this>
     */
    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }

    protected static function newFactory(): AstronautFactory
    {
        return AstronautFactory::new();
    }
}
