<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Infrastructure\Models;

use App\Modules\Identidad\Contratos\Actor;
use App\Modules\Identidad\Domain\Enums\ActorType;
use App\Modules\Identidad\Domain\Enums\Role;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;

/**
 * Una máquina que envía lecturas: el simulador o un sensor real.
 *
 * Con astronaut_id = null es un gateway de toda su misión.
 *
 * @property int $id
 * @property int $mission_id
 * @property int|null $astronaut_id
 * @property string $name
 * @property string $client_id
 * @property string $secret_hash
 * @property bool $active
 * @property CarbonImmutable|null $last_seen_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
final class Device extends Model implements Actor, AuthenticatableContract
{
    use Authenticatable;

    protected $fillable = ['mission_id', 'astronaut_id', 'name', 'client_id', 'secret_hash', 'active', 'last_seen_at'];

    protected $hidden = ['secret_hash'];

    public function getAuthPasswordName(): string
    {
        return 'secret_hash';
    }

    public function actorId(): int
    {
        return $this->id;
    }

    public function actorType(): ActorType
    {
        return ActorType::Device;
    }

    public function actorRole(): Role
    {
        return Role::Device;
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
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'mission_id' => 'integer',
            'astronaut_id' => 'integer',
            'active' => 'boolean',
            'last_seen_at' => 'immutable_datetime',
        ];
    }
}
