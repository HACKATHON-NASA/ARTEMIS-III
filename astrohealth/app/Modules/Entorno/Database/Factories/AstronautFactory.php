<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Database\Factories;

use App\Modules\Entorno\Infrastructure\Models\Astronaut;
use App\Modules\Entorno\Infrastructure\Models\Mission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Astronaut>
 */
final class AstronautFactory extends Factory
{
    protected $model = Astronaut::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mission_id' => Mission::factory(),
            'name' => fake()->name(),
            'crew_role' => 'Especialista de misión',
            'avatar' => null,
        ];
    }
}
