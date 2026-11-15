<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Application;

use App\Modules\Evaluacion\Domain\Entidades\PsychTest;
use App\Modules\Evaluacion\Domain\Excepciones\PsychProblem;
use App\Modules\Evaluacion\Infrastructure\Repositorios\Psych\FindPsychTestRepository;
use App\Modules\Evaluacion\Infrastructure\Repositorios\Psych\UpsertPsychTestRepository;

/**
 * Caso de uso: el equipo médico crea (sin id) o cambia un test del
 * catálogo. Los resultados viejos conservan el puntaje y la banda con que
 * se calcularon.
 */
final readonly class SavePsychTest
{
    public function __construct(
        private FindPsychTestRepository $findTest,
        private UpsertPsychTestRepository $upsertTest,
    ) {}

    /**
     * @param  array<string, mixed>  $data  Columnas de psych_tests, ya validadas.
     */
    public function execute(?int $id, array $data): PsychTest
    {
        if ($id !== null && $this->findTest->findById($id) === null) {
            throw PsychProblem::testNotFound();
        }

        $sameCode = $this->findTest->findByCode((string) $data['code']);

        if ($sameCode !== null && $sameCode->id !== $id) {
            throw PsychProblem::codeTaken();
        }

        return $id === null ? $this->upsertTest->create($data) : $this->upsertTest->update($id, $data);
    }
}
