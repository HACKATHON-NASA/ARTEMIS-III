<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application;

use App\Modules\Identidad\Application\DTOs\SessionResult;
use App\Modules\Identidad\Application\Servicios\SessionIssuer;
use App\Modules\Identidad\Domain\Enums\RotationDecision;
use App\Modules\Identidad\Domain\Excepciones\RefreshTokenInvalid;
use App\Modules\Identidad\Domain\Excepciones\RefreshTokenReused;
use App\Modules\Identidad\Domain\Reglas\RefreshRotation;
use App\Modules\Identidad\Infrastructure\Repositorios\RefreshToken\FindRefreshTokenRepository;
use App\Modules\Identidad\Infrastructure\Repositorios\RefreshToken\UpdateRefreshTokenRepository;
use App\Modules\Identidad\Infrastructure\Repositorios\User\FindUserRepository;
use App\Shared\Persistencia\Transaction;
use Carbon\CarbonImmutable;

/**
 * Caso de uso: renovar la sesión con el refresh token (rotación).
 *
 * El refresh se usa UNA vez: se marca usado y nace uno nuevo en la misma familia.
 * Si un refresh ya usado vuelve a llegar (posible robo), se revoca toda la familia.
 * La revocación se confirma ANTES de responder el error: por eso el error se lanza
 * fuera de la transacción.
 */
final readonly class RefreshSession
{
    public function __construct(
        private FindRefreshTokenRepository $findRefreshToken,
        private UpdateRefreshTokenRepository $updateRefreshToken,
        private FindUserRepository $findUser,
        private SessionIssuer $sessions,
        private Transaction $transaction,
    ) {}

    /**
     * @throws RefreshTokenInvalid
     * @throws RefreshTokenReused
     */
    public function execute(#[\SensitiveParameter] string $plainRefreshToken, ?string $ip, ?string $userAgent): SessionResult
    {
        $outcome = $this->transaction->run(
            fn (): SessionResult|RotationDecision => $this->rotate($plainRefreshToken, $ip, $userAgent),
            attempts: 3,
        );

        if ($outcome instanceof SessionResult) {
            return $outcome;
        }

        if ($outcome === RotationDecision::RevokeFamily) {
            throw new RefreshTokenReused;
        }

        throw new RefreshTokenInvalid;
    }

    private function rotate(#[\SensitiveParameter] string $plainRefreshToken, ?string $ip, ?string $userAgent): SessionResult|RotationDecision
    {
        $record = $this->findRefreshToken->findByHashForUpdate(SessionIssuer::hash($plainRefreshToken));

        if ($record === null) {
            return RotationDecision::Reject;
        }

        $now = CarbonImmutable::now();
        $decision = RefreshRotation::decide($record->usedAt, $record->revokedAt, $record->expiresAt, $now);

        if ($decision === RotationDecision::RevokeFamily) {
            $this->updateRefreshToken->revokeFamily($record->familyId, $now);

            return $decision;
        }

        if ($decision === RotationDecision::Reject) {
            return $decision;
        }

        $user = $this->findUser->findById($record->userId);

        if ($user === null || ! $user->identity()->canUseApi()) {
            $this->updateRefreshToken->revokeFamily($record->familyId, $now);

            return RotationDecision::Reject;
        }

        $this->updateRefreshToken->markUsed($record->id, $now);

        return $this->sessions->start($user, $ip, $userAgent, $record->familyId);
    }
}
