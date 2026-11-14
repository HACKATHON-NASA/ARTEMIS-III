<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application\Servicios;

use App\Modules\Entorno\Contratos\CrewQuery;
use App\Modules\Identidad\Application\DTOs\AccessToken;
use App\Modules\Identidad\Domain\Excepciones\UserWithoutRole;
use App\Modules\Identidad\Domain\ValueObjects\ActorIdentity;
use App\Modules\Identidad\Domain\ValueObjects\TokenClaims;
use App\Modules\Identidad\Infrastructure\Jwt\JwtConfig;
use App\Modules\Identidad\Infrastructure\Jwt\JwtIssuer;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;

/**
 * Arma los claims de un actor y firma su access token.
 */
final readonly class TokenFactory
{
    public function __construct(
        private JwtIssuer $issuer,
        private JwtConfig $config,
        private CrewQuery $crew,
    ) {}

    public function accessFor(ActorIdentity $actor): AccessToken
    {
        $role = $actor->role ?? throw new UserWithoutRole;
        $now = CarbonImmutable::now()->getTimestamp();

        $claims = new TokenClaims(
            type: $actor->type,
            actorId: $actor->id,
            role: $role,
            astronautId: $actor->astronautId,
            missionId: $this->missionOf($actor),
            jti: (string) Str::uuid(),
            issuedAt: $now,
            notBefore: $now,
            expiresAt: $now + $this->config->ttlSeconds(),
        );

        return new AccessToken($this->issuer->issue($claims), $this->config->ttlSeconds(), $claims);
    }

    /** La misión del actor: la del dispositivo, o la del astronauta enlazado (M6). */
    public function missionOf(ActorIdentity $actor): ?int
    {
        if ($actor->missionId !== null) {
            return $actor->missionId;
        }

        return $actor->astronautId === null ? null : $this->crew->missionOf($actor->astronautId);
    }
}
