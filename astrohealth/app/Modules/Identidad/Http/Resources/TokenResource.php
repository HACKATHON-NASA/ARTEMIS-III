<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http\Resources;

use App\Modules\Identidad\Application\DTOs\AccessToken;
use App\Modules\Identidad\Application\DTOs\ActorProfile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Respuesta de login, refresh y device-token. El refresh token NO va aquí:
 * viaja solo en la cookie HttpOnly.
 */
final class TokenResource extends JsonResource
{
    public function __construct(
        private readonly AccessToken $accessToken,
        private readonly ActorProfile $profile,
    ) {
        parent::__construct($accessToken);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'access_token' => $this->accessToken->token,
            'token_type' => 'Bearer',
            'expires_in' => $this->accessToken->expiresIn,
            'actor' => (new ActorResource($this->profile))->toArray($request),
        ];
    }
}
