<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\ValueObjects;

use App\Modules\Identidad\Domain\Enums\ActorType;
use App\Modules\Identidad\Domain\Enums\Role;
use InvalidArgumentException;

/**
 * Los claims del access token, tipados y sin depender de la librería JWT.
 *
 * Los claims estándar iss y aud los agrega y verifica Infrastructure/Jwt.
 */
final readonly class TokenClaims
{
    public function __construct(
        public ActorType $type,
        public int $actorId,
        public Role $role,
        public ?int $astronautId,
        public ?int $missionId,
        public string $jti,
        public int $issuedAt,
        public int $notBefore,
        public int $expiresAt,
    ) {}

    /** "user:7" o "device:1". */
    public function subject(): string
    {
        return $this->type->value.':'.$this->actorId;
    }

    /**
     * @return array<string, int|string|null>
     */
    public function toPayload(): array
    {
        return [
            'sub' => $this->subject(),
            'typ' => $this->type->value,
            'role' => $this->role->value,
            'astronaut_id' => $this->astronautId,
            'mission_id' => $this->missionId,
            'jti' => $this->jti,
            'iat' => $this->issuedAt,
            'nbf' => $this->notBefore,
            'exp' => $this->expiresAt,
        ];
    }

    /**
     * Reconstruye los claims a partir del payload ya verificado.
     *
     * @param  array<string, mixed>  $payload
     *
     * @throws InvalidArgumentException si falta un claim o tiene otro tipo.
     */
    public static function fromPayload(array $payload): self
    {
        $sub = self::string($payload, 'sub');
        $type = ActorType::tryFrom(self::string($payload, 'typ'));
        $role = Role::tryFrom(self::string($payload, 'role'));

        if ($type === null || $role === null) {
            throw new InvalidArgumentException('Tipo de actor o rol desconocido.');
        }

        [$subType, $subId] = array_pad(explode(':', $sub, 2), 2, '');

        if ($subType !== $type->value || ! ctype_digit($subId)) {
            throw new InvalidArgumentException('El claim sub no coincide con typ.');
        }

        return new self(
            type: $type,
            actorId: (int) $subId,
            role: $role,
            astronautId: self::nullableInt($payload, 'astronaut_id'),
            missionId: self::nullableInt($payload, 'mission_id'),
            jti: self::string($payload, 'jti'),
            issuedAt: self::int($payload, 'iat'),
            notBefore: self::int($payload, 'nbf'),
            expiresAt: self::int($payload, 'exp'),
        );
    }

    /** @param array<string, mixed> $payload */
    private static function string(array $payload, string $key): string
    {
        $value = $payload[$key] ?? null;

        if (! is_string($value) || $value === '') {
            throw new InvalidArgumentException("Falta el claim {$key}.");
        }

        return $value;
    }

    /** @param array<string, mixed> $payload */
    private static function int(array $payload, string $key): int
    {
        $value = $payload[$key] ?? null;

        if (! is_int($value)) {
            throw new InvalidArgumentException("El claim {$key} debe ser entero.");
        }

        return $value;
    }

    /** @param array<string, mixed> $payload */
    private static function nullableInt(array $payload, string $key): ?int
    {
        $value = $payload[$key] ?? null;

        if ($value !== null && ! is_int($value)) {
            throw new InvalidArgumentException("El claim {$key} debe ser entero o null.");
        }

        return $value;
    }
}
