<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Application;

use App\Modules\Evaluacion\Domain\ValueObjects\Threshold;
use App\Modules\Evaluacion\Infrastructure\Repositorios\Threshold\FindThresholdRepository;

/**
 * Caso de uso: los umbrales de un entorno (la Guía de salud los muestra).
 */
final readonly class ListThresholds
{
    public function __construct(private FindThresholdRepository $findThreshold) {}

    /**
     * @return list<Threshold>
     */
    public function execute(string $environment): array
    {
        return $this->findThreshold->forEnvironment($environment);
    }
}
