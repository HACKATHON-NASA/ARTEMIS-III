<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Database\Seeders;

use App\Modules\Cuerpo\Domain\Enums\NoteKind;
use App\Modules\Cuerpo\Infrastructure\Models\ClinicalNote;
use App\Modules\Cuerpo\Infrastructure\Repositorios\ClinicalNote\InsertClinicalNoteRepository;
use App\Modules\Entorno\Contratos\CrewMember;
use App\Modules\Entorno\Contratos\CrewQuery;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

/**
 * Notas clínicas de la demo, coherentes con la historia de Ares-1 (la
 * presión de Elena, el sueño y el ánimo de Kenji). Solo fuera de
 * producción y solo si todavía no hay notas.
 */
final class DemoClinicalNotesSeeder extends Seeder
{
    private const AUTHOR = 'Dra. Sofía Ramírez';

    /** [tripulante (0 = Elena, 1 = Kenji), tipo, texto, horas atrás, seguimiento] */
    private const NOTES = [
        [0, 'impression', 'Presión en ascenso desde el día 18, coincide con menos ejercicio y peor sueño. Probable efecto de la redistribución de líquidos + estrés del turno de mando.', 30, false],
        [0, 'plan', 'Control de presión mañana y tarde por 3 días. Bajar sal. Si supera 160/100, valorar medicación con el médico de la tripulación.', 2, true],
        [1, 'evolution', 'Sueño bajando de forma sostenida (7,3 → 5,8 h) y PHQ-9 de 3 a 11. Refiere ruido en el módulo de descanso.', 20, false],
        [1, 'follow_up', 'Revisar el PHQ-9 de la próxima semana. Si pasa de 14, llamada con psicología de Tierra.', 19, true],
    ];

    public function run(CrewQuery $crew, InsertClinicalNoteRepository $insert): void
    {
        if (app()->isProduction() || ClinicalNote::query()->exists()) {
            return;
        }

        $all = $crew->all();
        $members = array_values(array_filter($all, fn (CrewMember $member): bool => $member->missionId === ($all[0]->missionId ?? 0)));

        foreach (self::NOTES as [$index, $kind, $text, $hoursAgo, $followUp]) {
            $member = $members[$index] ?? null;

            if ($member !== null) {
                $insert->insert($member->id, null, self::AUTHOR, NoteKind::from($kind), $text, $followUp, CarbonImmutable::now()->subHours($hoursAgo));
            }
        }
    }
}
