<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Infrastructure\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $indicator_code
 * @property string $environment
 * @property float $green_min
 * @property float $green_max
 * @property float $yellow_min
 * @property float $yellow_max
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
final class Threshold extends Model
{
    protected $fillable = ['indicator_code', 'environment', 'green_min', 'green_max', 'yellow_min', 'yellow_max'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'green_min' => 'float',
            'green_max' => 'float',
            'yellow_min' => 'float',
            'yellow_max' => 'float',
        ];
    }
}
