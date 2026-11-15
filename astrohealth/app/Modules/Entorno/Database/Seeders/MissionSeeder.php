<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Database\Seeders;

use App\Modules\Entorno\Infrastructure\Repositorios\Astronaut\UpsertAstronautRepository;
use App\Modules\Entorno\Infrastructure\Repositorios\Mission\UpsertMissionRepository;
use Illuminate\Database\Seeder;

/**
 * Las misiones de la demo, una por estado:
 * - Ares-1 (en curso): rumbo a Marte, hoy en el día 24 de 30, con su tripulación.
 * - Órbita-7 (terminada): rotación en la Estación Espacial.
 * - Selene-2 (planificada): empieza en 21 días, en la Luna, aún sin tripulación.
 *
 * Ares-1 va primero: su tripulación toma los ids 1 a 4, que usa UserSeeder.
 * Tripulación ficticia. Se puede ejecutar varias veces sin duplicar.
 */
final class MissionSeeder extends Seeder
{
    /** @var list<array{name: string, environment: string, start_offset: int, day_count: int, objective: string, crew: list<array{0: string, 1: string}>}> */
    private const MISSIONS = [
        [
            'name' => 'Ares-1',
            'environment' => 'mars',
            'start_offset' => -23,
            'day_count' => 30,
            'objective' => 'Primera estancia larga en Marte: adaptación a 0,38 g y radiación.',
            'crew' => [
                ['Elena Vargas', 'Comandante'],
                ['Kenji Mori', 'Ingeniero de vuelo'],
                ['Amara Okafor', 'Especialista de misión'],
                ['Lucas Ferreira', 'Médico de la tripulación'],
            ],
        ],
        [
            'name' => 'Órbita-7',
            'environment' => 'iss',
            'start_offset' => -150,
            'day_count' => 90,
            'objective' => 'Rotación de la Estación Espacial: microgravedad.',
            'crew' => [
                ['Marco Bellini', 'Comandante'],
                ['Ana Sato', 'Ingeniero de vuelo'],
            ],
        ],
        [
            'name' => 'Selene-2',
            'environment' => 'moon',
            'start_offset' => 21,
            'day_count' => 14,
            'objective' => 'Base en el polo sur lunar: polvo, frío y radiación.',
            'crew' => [],
        ],
    ];

    public function run(UpsertMissionRepository $upsertMission, UpsertAstronautRepository $upsertAstronaut): void
    {
        foreach (self::MISSIONS as $mission) {
            $missionId = $upsertMission->firstOrCreate(
                name: $mission['name'],
                environment: $mission['environment'],
                startDate: now()->addDays($mission['start_offset'])->toDateString(),
                dayCount: $mission['day_count'],
                objective: $mission['objective'],
            );

            foreach ($mission['crew'] as [$name, $crewRole]) {
                $upsertAstronaut->firstOrCreate($missionId, $name, $crewRole);
            }
        }
    }
}
