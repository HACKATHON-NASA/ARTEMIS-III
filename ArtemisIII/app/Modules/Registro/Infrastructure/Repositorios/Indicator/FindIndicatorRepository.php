<?php

declare(strict_types=1);

namespace App\Modules\Registro\Infrastructure\Repositorios\Indicator;

use App\Modules\Registro\Contratos\IndicatorCatalog;
use App\Modules\Registro\Contratos\IndicatorInfo;
use App\Modules\Registro\Domain\Entidades\Indicator;
use App\Modules\Registro\Infrastructure\Models\Indicator as IndicatorModel;

/**
 * BUSCAR indicadores (tabla indicators). Solo lectura. El catálogo es
 * pequeño y no cambia en una petición: se lee una vez. Implementa IndicatorCatalog.
 */
final class FindIndicatorRepository implements IndicatorCatalog
{
    /** @var array<string, Indicator>|null */
    private ?array $byCode = null;

    /**
     * Los indicadores en el orden del formulario.
     *
     * @return list<Indicator>
     */
    public function ordered(): array
    {
        return array_values($this->load());
    }

    public function byCode(string $code): ?Indicator
    {
        return $this->load()[$code] ?? null;
    }

    public function all(): array
    {
        return array_map(
            fn (Indicator $indicator): IndicatorInfo => new IndicatorInfo($indicator->code, $indicator->name, $indicator->unit, $indicator->bodyPart),
            $this->ordered(),
        );
    }

    /**
     * @return array<string, Indicator>
     */
    private function load(): array
    {
        if ($this->byCode === null) {
            $this->byCode = [];

            foreach (IndicatorModel::query()->orderBy('sort_order')->get() as $row) {
                $this->byCode[$row->code] = IndicatorMapper::toEntity($row);
            }
        }

        return $this->byCode;
    }
}
