<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Infrastructure\Repositorios\BodyPart;

use App\Modules\Cuerpo\Domain\Entidades\BodyPart;
use App\Modules\Cuerpo\Infrastructure\Models\BodyPart as BodyPartModel;

/**
 * BUSCAR zonas del cuerpo (tabla body_parts). Solo lectura.
 */
final class FindBodyPartRepository
{
    /**
     * @return list<BodyPart>
     */
    public function all(): array
    {
        return array_values(BodyPartModel::query()->orderBy('sort_order')->get()
            ->map(fn (BodyPartModel $row): BodyPart => new BodyPart($row->code, $row->name, $row->function_text, $row->space_risk_text, $row->hrp_hazard, $row->sort_order))
            ->all());
    }
}
