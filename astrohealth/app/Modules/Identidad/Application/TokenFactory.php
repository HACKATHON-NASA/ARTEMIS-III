<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application;

use App\Modules\Entorno\Contratos\CrewQuery;
use App\Modules\Identidad\Application\DTOs\AccessToken;
use App\Modules\Identidad\Contratos\Actor;
use App\Modules\Identidad\Domain\Excepciones\UserWithoutRole;
use App\Modules\Identidad\Domain\ValueObjects\TokenClaims;
use App\Modules\Identidad\Infrastructure\Jwt\JwtConfig;
use App\Modules\Identidad\Infrastructure\Jwt\JwtIssuer;
use App\Modules\Identidad\Infrastructure\Models\Device;
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

    public function accessFor(Actor $actor): AccessToken
    {
        $role = $actor->actorRole() ?? throw new UserWithoutRole;
        $now = CarbonImmutable::now()->getTimestamp();

        $claims = new TokenClaims(
            type: $actor->actorType(),
            actorId: $actor->actorId(),
            role: $role,
            astronautId: $actor->actorAstronautId(),
            missionId: $this->missionOf($actor),
            jti: (string) Str::uuid(),
            issuedAt: $now,
            notBefore: $now,
            expiresAt: $now + $this->config->ttlSeconds(),
        );

        return new AccessToken($this->issuer->issue($claims), $this->config->ttlSeconds(), $claims);
    }

    /** La misión del actor: la del dispositivo, o la del astronauta enlazado (M6). */
    public function missionOf(Actor $actor): ?int
    {
        if ($actor instanceof Device) {
            return $actor->mission_id;
        }

        $astronautId = $actor->actorAstronautId();

        return $astronautId === null ? null : $this->crew->missionOf($astronautId);
    }
}
