<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Database\Seeders;

use App\Modules\Entorno\Infrastructure\Repositorios\Astronaut\UpsertAstronautRepository;
use App\Modules\Entorno\Infrastructure\Repositorios\Mission\UpsertMissionRepository;
use Illuminate\Database\Seeder;

/**
 * La misión de la demo: Ares-1, rumbo a Marte, hoy en el día 24 de 30.
 * Tripulación ficticia. Se puede ejecutar varias veces sin duplicar.
 */
final class MissionSeeder extends Seeder
{
    /** @var list<array{name: string, crew_role: string}> */
    private const CREW = [
        ['name' => 'Elena Vargas', 'crew_role' => 'Comandante'],
        ['name' => 'Kenji Mori', 'crew_role' => 'Ingeniero de vuelo'],
        ['name' => 'Amara Okafor', 'crew_role' => 'Especialista de misión'],
        ['name' => 'Lucas Ferreira', 'crew_role' => 'Médico de la tripulación'],
    ];

    public function run(UpsertMissionRepository $upsertMission, UpsertAstronautRepository $upsertAstronaut): void
    {
        $missionId = $upsertMission->firstOrCreate(
            name: 'Ares-1',
            environment: 'mars',
            startDate: now()->subDays(23)->toDateString(),
            dayCount: 30,
        );

        foreach (self::CREW as $member) {
            $upsertAstronaut->firstOrCreate($missionId, $member['name'], $member['crew_role']);
        }
    }
}
