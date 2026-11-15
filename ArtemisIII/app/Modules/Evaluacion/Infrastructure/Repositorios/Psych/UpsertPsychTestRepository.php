<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Infrastructure\Repositorios\Psych;

use App\Modules\Evaluacion\Domain\Entidades\PsychTest;
use App\Modules\Evaluacion\Infrastructure\Models\PsychTest as PsychTestModel;

/**
 * CREAR o CAMBIAR tests de la guía psicológica (y sembrar los de base por código).
 */
final class UpsertPsychTestRepository
{
    /**
     * @param  array<string, mixed>  $data  Columnas de psych_tests.
     */
    public function create(array $data): PsychTest
    {
        return PsychMapper::test(PsychTestModel::query()->create($data)->refresh());
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(int $id, array $data): PsychTest
    {
        $row = PsychTestModel::query()->findOrFail($id);
        $row->update($data);

        return PsychMapper::test($row->refresh());
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function upsertByCode(string $code, array $data): void
    {
        PsychTestModel::query()->updateOrCreate(['code' => $code], $data);
    }
}
