<?php

namespace App\Models;

use App\Modules\Identidad\Contratos\Actor;
use App\Modules\Identidad\Domain\Enums\ActorType;
use App\Modules\Identidad\Domain\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * Una persona que usa AstroHealth. Su dueño es M0 · Identidad
 * (se queda en app/Models porque Laravel la espera aquí). Entra solo por
 * la API con JWT (POST /api/v1/auth/login).
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Role|null $role
 * @property bool $active
 * @property int|null $astronaut_id
 * @property Carbon|null $last_login_at
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password', 'role', 'active', 'astronaut_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements Actor
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** Igual que el valor por defecto de la columna, para los modelos recién creados. */
    protected $attributes = ['active' => true];

    public function actorId(): int
    {
        return $this->id;
    }

    public function actorType(): ActorType
    {
        return ActorType::User;
    }

    public function actorRole(): ?Role
    {
        return $this->role;
    }

    public function actorAstronautId(): ?int
    {
        return $this->astronaut_id;
    }

    public function actorIsActive(): bool
    {
        return $this->active;
    }

    public function actorName(): string
    {
        return $this->name;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'active' => 'boolean',
            'astronaut_id' => 'integer',
            'last_login_at' => 'datetime',
        ];
    }
}
