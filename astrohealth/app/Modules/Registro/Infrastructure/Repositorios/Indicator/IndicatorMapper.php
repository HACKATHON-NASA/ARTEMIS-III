<?php

declare(strict_types=1);

namespace App\Modules\Registro\Infrastructure\Repositorios\Indicator;

use App\Modules\Registro\Domain\Entidades\Indicator;
use App\Modules\Registro\Infrastructure\Models\Indicator as IndicatorModel;

/**
 * Fila de indicators (Eloquent) → Indicator. El modelo no sale de Infrastructure.
 */
final class IndicatorMapper
{
    public static function toEntity(IndicatorModel $row): Indicator
    {
        return new Indicator(
            id: $row->id,
            code: $row->code,
            name: $row->name,
            unit: $row->unit,
            bodyPart: $row->body_part,
            minPossible: $row->min_possible,
            maxPossible: $row->max_possible,
            step: $row->step,
            sortOrder: $row->sort_order,
            helpHow: $row->help_how,
            helpWhy: $row->help_why,
        );
    }
}
