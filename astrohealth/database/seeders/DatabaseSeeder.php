<?php

namespace Database\Seeders;

use App\Modules\Entorno\Database\Seeders\MissionSeeder;
use App\Modules\Identidad\Database\Seeders\DeviceSeeder;
use App\Modules\Identidad\Database\Seeders\UserSeeder;
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
        ]);
    }
}
