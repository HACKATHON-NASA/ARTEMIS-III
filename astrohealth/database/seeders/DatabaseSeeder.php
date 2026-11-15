<?php

namespace Database\Seeders;

use App\Modules\Cuerpo\Database\Seeders\BodyPartSeeder;
use App\Modules\Cuerpo\Database\Seeders\DemoClinicalNotesSeeder;
use App\Modules\Entorno\Database\Seeders\MissionSeeder;
use App\Modules\Evaluacion\Database\Seeders\ThresholdSeeder;
use App\Modules\Identidad\Database\Seeders\DeviceSeeder;
use App\Modules\Identidad\Database\Seeders\UserSeeder;
use App\Modules\Registro\Database\Seeders\DemoReadingsSeeder;
use App\Modules\Registro\Database\Seeders\IndicatorSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Datos de la demo. El orden importa: cada seeder usa lo del anterior.
     */
    public function run(): void
    {
        $this->call([
            MissionSeeder::class,   // M6: misión Ares-1 y 4 astronautas
            UserSeeder::class,      // M0: un usuario por rol, enlazados a sus astronautas
            DeviceSeeder::class,    // M0: el dispositivo del simulador
            ThresholdSeeder::class, // M2: umbrales del semáforo en los 4 entornos
            IndicatorSeeder::class, // M1: el catálogo de los 10 indicadores
            BodyPartSeeder::class,  // M3: las 9 zonas del cuerpo
            // Solo fuera de producción: los últimos valores y las notas de Ares-1.
            DemoReadingsSeeder::class,
            DemoClinicalNotesSeeder::class,
        ]);
    }
}
