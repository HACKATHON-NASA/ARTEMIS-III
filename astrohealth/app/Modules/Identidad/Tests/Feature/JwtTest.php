<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Tests\Feature;

use App\Modules\Identidad\Domain\Enums\ActorType;
use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Domain\Excepciones\TokenExpired;
use App\Modules\Identidad\Domain\Excepciones\TokenInvalid;
use App\Modules\Identidad\Domain\ValueObjects\TokenClaims;
use App\Modules\Identidad\Infrastructure\Jwt\JwtConfig;
use App\Modules\Identidad\Infrastructure\Jwt\JwtIssuer;
use App\Modules\Identidad\Infrastructure\Jwt\JwtVerifier;
use App\Modules\Identidad\Infrastructure\Jwt\MissingJwtSecret;
use Carbon\CarbonImmutable;
use Firebase\JWT\JWT;
use Tests\TestCase;

/**
 * Firmar y verificar: lo que se acepta y todo lo que se rechaza.
 */
final class JwtTest extends TestCase
{
    private function claims(int $ttl = 900): TokenClaims
    {
        $now = CarbonImmutable::now()->getTimestamp();

        return new TokenClaims(ActorType::User, 7, Role::Astronaut, 2, 1, 'jti-test', $now, $now, $now + $ttl);
    }

    private function key(): string
    {
        return app(JwtConfig::class)->key();
    }

    public function test_a_signed_token_verifies_with_the_same_claims(): void
    {
        $claims = $this->claims();
        $token = app(JwtIssuer::class)->issue($claims);

        $this->assertEquals($claims, app(JwtVerifier::class)->verify($token));
    }

    public function test_a_tampered_payload_is_rejected(): void
    {
        $token = app(JwtIssuer::class)->issue($this->claims());
        [$header, , $signature] = explode('.', $token);
        $fakePayload = JWT::urlsafeB64Encode((string) json_encode(['sub' => 'user:1', 'role' => 'admin']));

        $this->expectException(TokenInvalid::class);
        app(JwtVerifier::class)->verify("{$header}.{$fakePayload}.{$signature}");
    }

    public function test_alg_none_is_rejected(): void
    {
        $header = JWT::urlsafeB64Encode('{"alg":"none","typ":"JWT"}');
        $payload = JWT::urlsafeB64Encode((string) json_encode($this->claims()->toPayload()));

        $this->expectException(TokenInvalid::class);
        app(JwtVerifier::class)->verify("{$header}.{$payload}.");
    }

    public function test_another_algorithm_with_the_same_key_is_rejected(): void
    {
        $payload = ['iss' => config('jwt.issuer'), 'aud' => config('jwt.audience'), ...$this->claims()->toPayload()];
        $token = JWT::encode($payload, $this->key().$this->key(), 'HS512');

        $this->expectException(TokenInvalid::class);
        app(JwtVerifier::class)->verify($token);
    }

    public function test_a_token_for_another_audience_is_rejected(): void
    {
        $payload = ['iss' => config('jwt.issuer'), 'aud' => 'otra-api', ...$this->claims()->toPayload()];

        $this->expectException(TokenInvalid::class);
        app(JwtVerifier::class)->verify(JWT::encode($payload, $this->key(), 'HS256'));
    }

    public function test_a_token_from_another_issuer_is_rejected(): void
    {
        $payload = ['iss' => 'https://atacante.test', 'aud' => config('jwt.audience'), ...$this->claims()->toPayload()];

        $this->expectException(TokenInvalid::class);
        app(JwtVerifier::class)->verify(JWT::encode($payload, $this->key(), 'HS256'));
    }

    public function test_garbage_is_rejected(): void
    {
        $this->expectException(TokenInvalid::class);
        app(JwtVerifier::class)->verify('esto-no-es-un-jwt');
    }

    public function test_expired_beyond_the_leeway_is_expired(): void
    {
        $token = app(JwtIssuer::class)->issue($this->claims(ttl: 60));

        $this->travel(60 + 31)->seconds();

        $this->expectException(TokenExpired::class);
        app(JwtVerifier::class)->verify($token);
    }

    public function test_expired_within_the_leeway_is_still_valid(): void
    {
        $token = app(JwtIssuer::class)->issue($this->claims(ttl: 60));

        $this->travel(60 + 20)->seconds();

        $this->assertSame('jti-test', app(JwtVerifier::class)->verify($token)->jti);
    }

    public function test_without_a_secret_nothing_is_signed(): void
    {
        $config = JwtConfig::fromArray(['secret' => null, 'ttl' => 15, 'refresh_ttl' => 60, 'issuer' => 'x', 'audience' => 'y', 'leeway' => 0]);

        $this->expectException(MissingJwtSecret::class);
        (new JwtIssuer($config))->issue($this->claims());
    }

    public function test_a_short_secret_is_refused(): void
    {
        $config = JwtConfig::fromArray(['secret' => 'corto', 'ttl' => 15, 'refresh_ttl' => 60, 'issuer' => 'x', 'audience' => 'y', 'leeway' => 0]);

        $this->expectException(MissingJwtSecret::class);
        $config->key();
    }
}
