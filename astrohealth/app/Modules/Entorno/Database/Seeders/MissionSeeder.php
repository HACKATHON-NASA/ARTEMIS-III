<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Database\Seeders;

use App\Modules\Entorno\Infrastructure\Models\Mission;
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

    public function run(): void
    {
        $mission = Mission::query()->firstOrCreate(
            ['name' => 'Ares-1'],
            [
                'environment' => 'mars',
                'start_date' => now()->subDays(23)->toDateString(),
                'day_count' => 30,
            ],
        );

        foreach (self::CREW as $member) {
            $mission->astronauts()->firstOrCreate(
                ['name' => $member['name']],
                ['crew_role' => $member['crew_role']],
            );
        }
    }
}
