<?php

declare(strict_types=1);

namespace App\Modules\Registro\Application;

use App\Modules\Registro\Domain\Entidades\Indicator;
use App\Modules\Registro\Infrastructure\Repositorios\Indicator\FindIndicatorRepository;

/**
 * Caso de uso: el catálogo de indicadores para el formulario y la ayuda.
 */
final readonly class ListIndicators
{
    public function __construct(private FindIndicatorRepository $findIndicator) {}

    /**
     * @return list<Indicator>
     */
    public function execute(): array
    {
        return $this->findIndicator->ordered();
    }
}
