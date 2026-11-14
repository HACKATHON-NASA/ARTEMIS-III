<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application;

use App\Modules\Identidad\Application\DTOs\ActorProfile;
use App\Modules\Identidad\Contratos\CurrentActor;

/**
 * Caso de uso: ¿quién soy y qué puedo hacer? (GET /auth/me)
 */
final readonly class GetMe
{
    public function __construct(
        private CurrentActor $currentActor,
        private TokenFactory $tokens,
        private ActorProfiles $profiles,
    ) {}

    public function execute(): ActorProfile
    {
        $actor = $this->currentActor->get();

        return $this->profiles->of($actor, $this->tokens->missionOf($actor));
    }
}
