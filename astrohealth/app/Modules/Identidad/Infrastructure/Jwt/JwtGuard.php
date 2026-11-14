<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Infrastructure\Jwt;

use App\Modules\Identidad\Contratos\Actor;
use App\Modules\Identidad\Domain\Enums\ActorType;
use App\Modules\Identidad\Domain\Excepciones\TokenExpired;
use App\Modules\Identidad\Domain\Excepciones\TokenInvalid;
use App\Modules\Identidad\Domain\ValueObjects\TokenClaims;
use App\Modules\Identidad\Infrastructure\Repositorios\DeviceRepository;
use App\Modules\Identidad\Infrastructure\Repositorios\UserRepository;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

/**
 * Guard "jwt" (auth:api): ¿quién llama?
 *
 * Sin token → null (auth:api responde 401 UNAUTHENTICATED).
 * Con token inválido o vencido → lanza el error exacto de M0.
 * Con token válido → carga al actor DESDE LA BASE: su rol y si está activo
 * se leen en cada petición, así un cambio de rol o una desactivación aplica al instante.
 */
final class JwtGuard implements Guard
{
    use GuardHelpers;

    private bool $resolved = false;

    private ?TokenClaims $claims = null;

    public function __construct(
        private readonly JwtVerifier $verifier,
        private readonly RevokedTokens $revokedTokens,
        private readonly UserRepository $users,
        private readonly DeviceRepository $devices,
        private Request $request,
    ) {}

    /**
     * @throws TokenInvalid
     * @throws TokenExpired
     */
    public function user(): ?Authenticatable
    {
        if ($this->resolved) {
            return $this->user;
        }

        $this->resolved = true;

        $token = $this->request->bearerToken();

        if ($token === null || $token === '') {
            return null;
        }

        $claims = $this->verifier->verify($token);

        if ($this->revokedTokens->isRevoked($claims->jti)) {
            throw new TokenInvalid;
        }

        $actor = match ($claims->type) {
            ActorType::User => $this->users->findById($claims->actorId),
            ActorType::Device => $this->devices->findById($claims->actorId),
        };

        if (! $actor instanceof Actor || ! $actor->actorIsActive() || $actor->actorRole() === null) {
            throw new TokenInvalid;
        }

        $this->claims = $claims;
        $this->user = $actor;

        return $actor;
    }

    /** Los claims del token de la petición actual (null si no hay actor). */
    public function claims(): ?TokenClaims
    {
        $this->user();

        return $this->claims;
    }

    /**
     * Este guard no valida credenciales: el login lo hace el caso de uso Login.
     *
     * @param  array<string, mixed>  $credentials
     */
    public function validate(array $credentials = []): bool
    {
        return false;
    }

    /** Laravel lo llama cuando cambia el request (p. ej. varias peticiones en una prueba). */
    public function setRequest(Request $request): static
    {
        $this->request = $request;
        $this->resolved = false;
        $this->claims = null;
        $this->user = null;

        return $this;
    }
}
