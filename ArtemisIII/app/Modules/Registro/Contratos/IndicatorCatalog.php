<?php

declare(strict_types=1);

namespace App\Modules\Registro\Contratos;

/**
 * Lo que M1 ofrece sobre el catálogo de indicadores (nombre, unidad y zona).
 */
interface IndicatorCatalog
{
    /**
     * Los indicadores en el orden del formulario.
     *
     * @return list<IndicatorInfo>
     */
    public function all(): array;
}
