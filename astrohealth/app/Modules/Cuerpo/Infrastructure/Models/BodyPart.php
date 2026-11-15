<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $function_text
 * @property string $space_risk_text
 * @property string $hrp_hazard
 * @property int $sort_order
 */
final class BodyPart extends Model
{
    protected $fillable = ['code', 'name', 'function_text', 'space_risk_text', 'hrp_hazard', 'sort_order'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }
}
