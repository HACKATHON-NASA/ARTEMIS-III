<?php

declare(strict_types=1);

namespace App\Modules\Registro\Contratos;

/**
 * Un indicador, tal como lo ven los demás módulos.
 */
final readonly class IndicatorInfo
{
    public function __construct(
        public string $code,
        public string $name,
        public string $unit,
        public string $bodyPart,
    ) {}
}
