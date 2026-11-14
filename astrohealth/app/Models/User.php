<?php

namespace App\Models;

use App\Modules\Identidad\Contratos\Actor;
use App\Modules\Identidad\Domain\Enums\ActorType;
use App\Modules\Identidad\Domain\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * Una persona que usa AstroHealth. Su dueño es M0 · Identidad
 * (se queda en app/Models porque Laravel y Fortify la esperan aquí).
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
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password', 'role', 'active', 'astronaut_id'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements Actor, MustVerifyEmail, PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

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
            'two_factor_confirmed_at' => 'datetime',
            'role' => Role::class,
            'active' => 'boolean',
            'astronaut_id' => 'integer',
            'last_login_at' => 'datetime',
        ];
    }
}
