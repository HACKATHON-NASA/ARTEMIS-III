<?php

namespace Database\Factories;

use App\Models\User;
use App\Modules\Identidad\Domain\Enums\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => ['role' => Role::Admin, 'astronaut_id' => null]);
    }

    public function flightSurgeon(): static
    {
        return $this->state(fn (array $attributes) => ['role' => Role::FlightSurgeon, 'astronaut_id' => null]);
    }

    public function astronaut(int $astronautId): static
    {
        return $this->state(fn (array $attributes) => ['role' => Role::Astronaut, 'astronaut_id' => $astronautId]);
    }

    public function crewMedicalOfficer(int $astronautId): static
    {
        return $this->state(fn (array $attributes) => ['role' => Role::CrewMedicalOfficer, 'astronaut_id' => $astronautId]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['active' => false]);
    }
}
