<?php

declare(strict_types=1);

namespace App\Modules\Registro\Infrastructure\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $unit
 * @property string $body_part
 * @property float $min_possible
 * @property float $max_possible
 * @property float $step
 * @property int $sort_order
 * @property string $help_how
 * @property string $help_why
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
final class Indicator extends Model
{
    protected $fillable = ['code', 'name', 'unit', 'body_part', 'min_possible', 'max_possible', 'step', 'sort_order', 'help_how', 'help_why'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'min_possible' => 'float',
            'max_possible' => 'float',
            'step' => 'float',
            'sort_order' => 'integer',
        ];
    }
}
