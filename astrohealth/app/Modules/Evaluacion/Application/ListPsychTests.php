<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Application;

use App\Modules\Evaluacion\Domain\Entidades\PsychTest;
use App\Modules\Evaluacion\Infrastructure\Repositorios\Psych\FindPsychTestRepository;

/**
 * Caso de uso: el catálogo de la guía psicológica.
 */
final readonly class ListPsychTests
{
    public function __construct(private FindPsychTestRepository $findTest) {}

    /**
     * @return list<PsychTest>
     */
    public function execute(): array
    {
        return $this->findTest->all();
    }
}
