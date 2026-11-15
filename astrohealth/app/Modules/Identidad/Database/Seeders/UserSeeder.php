<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Database\Seeders;

use App\Modules\Entorno\Contratos\CrewQuery;
use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Domain\Reglas\RoleAssignment;
use App\Modules\Identidad\Infrastructure\Repositorios\User\UpsertUserRepository;
use Illuminate\Database\Seeder;
use RuntimeException;

/**
 * Usuarios de la demo, uno por rol (contraseña "password", SOLO fuera de producción).
 * Va después de MissionSeeder (M6): los tripulantes se enlazan a sus astronautas.
 */
final class UserSeeder extends Seeder
{
    public function run(CrewQuery $crew, UpsertUserRepository $upsertUser): void
    {
        if (app()->isProduction()) {
            $this->command->warn('UserSeeder no crea usuarios de demo en producción.');

            return;
        }

        $members = $crew->all();

        if (count($members) < 4) {
            throw new RuntimeException('UserSeeder necesita los 4 astronautas de MissionSeeder.');
        }

        $users = [
            ['email' => 'admin@astrohealth.test', 'name' => 'Administración AstroHealth', 'role' => Role::Admin, 'astronaut_id' => null],
            ['email' => 'tierra@astrohealth.test', 'name' => 'Dra. Sofía Ramírez', 'role' => Role::FlightSurgeon, 'astronaut_id' => null],
            ['email' => 'astro1@astrohealth.test', 'name' => $members[0]->name, 'role' => Role::Astronaut, 'astronaut_id' => $members[0]->id],
            ['email' => 'astro2@astrohealth.test', 'name' => $members[1]->name, 'role' => Role::Astronaut, 'astronaut_id' => $members[1]->id],
            ['email' => 'astro3@astrohealth.test', 'name' => $members[2]->name, 'role' => Role::Astronaut, 'astronaut_id' => $members[2]->id],
            ['email' => 'cmo@astrohealth.test', 'name' => $members[3]->name, 'role' => Role::CrewMedicalOfficer, 'astronaut_id' => $members[3]->id],
        ];

        foreach ($users as $data) {
            RoleAssignment::assert($data['role'], $data['astronaut_id']);

            $upsertUser->upsertByEmail(
                email: $data['email'],
                name: $data['name'],
                password: 'password',
                role: $data['role'],
                astronautId: $data['astronaut_id'],
            );
        }
    }
}
