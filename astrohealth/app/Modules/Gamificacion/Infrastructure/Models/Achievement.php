<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $icon
 * @property string $condition
 * @property int $bonus
 * @property int $target
 * @property int $sort_order
 */
final class Achievement extends Model
{
    protected $fillable = ['code', 'name', 'icon', 'condition', 'bonus', 'target', 'sort_order'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['bonus' => 'integer', 'target' => 'integer', 'sort_order' => 'integer'];
    }
}
