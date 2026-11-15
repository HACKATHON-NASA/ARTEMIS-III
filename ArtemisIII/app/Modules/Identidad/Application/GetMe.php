<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application;

use App\Modules\Identidad\Application\DTOs\ActorProfile;
use App\Modules\Identidad\Application\Servicios\ActorIdentityLoader;
use App\Modules\Identidad\Application\Servicios\ActorProfiles;
use App\Modules\Identidad\Application\Servicios\TokenFactory;
use App\Modules\Identidad\Contratos\CurrentActor;
use Illuminate\Auth\AuthenticationException;

/**
 * Caso de uso: ¿quién soy y qué puedo hacer? (GET /auth/me)
 */
final readonly class GetMe
{
    public function __construct(
        private CurrentActor $currentActor,
        private ActorIdentityLoader $identities,
        private TokenFactory $tokens,
        private ActorProfiles $profiles,
    ) {}

    public function execute(): ActorProfile
    {
        $claims = $this->currentActor->claims();

        $actor = $this->identities->load($claims->type, $claims->actorId) ?? throw new AuthenticationException;

        return $this->profiles->of($actor, $this->tokens->missionOf($actor));
    }
}
