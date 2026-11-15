<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Database\Factories;

use App\Modules\Entorno\Infrastructure\Models\Mission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mission>
 */
final class MissionFactory extends Factory
{
    protected $model = Mission::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Misión '.fake()->unique()->numberBetween(1, 9999),
            'environment' => 'mars',
            'start_date' => now()->subDays(23)->toDateString(),
            'day_count' => 30,
        ];
    }
}
