<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Application;

use App\Modules\Cuerpo\Application\DTOs\BodyPartResult;
use App\Modules\Cuerpo\Domain\Entidades\BodyPart;
use App\Modules\Cuerpo\Infrastructure\Repositorios\BodyPart\FindBodyPartRepository;
use App\Modules\Registro\Contratos\IndicatorCatalog;
use App\Modules\Registro\Contratos\IndicatorInfo;

/**
 * Caso de uso: las zonas del cuerpo con su función, su riesgo en el espacio
 * y los indicadores que las miden.
 */
final readonly class ListBodyParts
{
    public function __construct(
        private FindBodyPartRepository $findBodyPart,
        private IndicatorCatalog $indicators,
    ) {}

    /**
     * @return list<BodyPartResult>
     */
    public function execute(): array
    {
        $indicators = $this->indicators->all();

        return array_map(fn (BodyPart $part): BodyPartResult => new BodyPartResult(
            $part,
            array_values(array_map(
                fn (IndicatorInfo $indicator): string => $indicator->code,
                array_filter($indicators, fn (IndicatorInfo $indicator): bool => $indicator->bodyPart === $part->code),
            )),
        ), $this->findBodyPart->all());
    }
}
