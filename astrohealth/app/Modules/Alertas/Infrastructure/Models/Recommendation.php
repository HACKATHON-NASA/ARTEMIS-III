<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $indicator_code
 * @property string $status
 * @property string $title
 * @property string $action_text
 * @property string|null $risk_ref
 */
final class Recommendation extends Model
{
    protected $fillable = ['indicator_code', 'status', 'title', 'action_text', 'risk_ref'];
}
