<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Infrastructure\Repositorios\Psych;

use App\Modules\Evaluacion\Domain\Entidades\PsychTest;
use App\Modules\Evaluacion\Infrastructure\Models\PsychTest as PsychTestModel;

/**
 * BUSCAR tests de la guía psicológica.
 */
final class FindPsychTestRepository
{
    /**
     * @return list<PsychTest>
     */
    public function all(): array
    {
        return array_values(PsychTestModel::query()->orderBy('id')->get()->map(PsychMapper::test(...))->all());
    }

    public function findById(int $id): ?PsychTest
    {
        $row = PsychTestModel::query()->find($id);

        return $row === null ? null : PsychMapper::test($row);
    }

    public function findByCode(string $code): ?PsychTest
    {
        $row = PsychTestModel::query()->where('code', $code)->first();

        return $row === null ? null : PsychMapper::test($row);
    }
}
