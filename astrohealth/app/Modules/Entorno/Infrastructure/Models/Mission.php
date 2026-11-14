<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Infrastructure\Models;

use App\Modules\Entorno\Database\Factories\MissionFactory;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $environment
 * @property CarbonImmutable $start_date
 * @property int $day_count
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
final class Mission extends Model
{
    /** @use HasFactory<MissionFactory> */
    use HasFactory;

    protected $fillable = ['name', 'environment', 'start_date', 'day_count'];

    /**
     * @return HasMany<Astronaut, $this>
     */
    public function astronauts(): HasMany
    {
        return $this->hasMany(Astronaut::class);
    }

    protected static function newFactory(): MissionFactory
    {
        return MissionFactory::new();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'immutable_date',
            'day_count' => 'integer',
        ];
    }
}
