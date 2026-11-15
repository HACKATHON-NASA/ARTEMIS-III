<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Tests\Unit;

use App\Modules\Identidad\Domain\Enums\ActorType;
use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Domain\ValueObjects\TokenClaims;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class TokenClaimsTest extends TestCase
{
    public function test_payload_round_trip_keeps_every_claim(): void
    {
        $claims = new TokenClaims(ActorType::User, 7, Role::CrewMedicalOfficer, 4, 1, 'jti-1', 100, 100, 1000);

        $payload = $claims->toPayload();

        $this->assertSame('user:7', $payload['sub']);
        $this->assertEquals($claims, TokenClaims::fromPayload($payload));
    }

    public function test_device_claims_use_device_subject(): void
    {
        $claims = new TokenClaims(ActorType::Device, 3, Role::Device, null, 1, 'jti-2', 100, 100, 1000);

        $this->assertSame('device:3', $claims->subject());
    }

    public function test_sub_must_match_typ(): void
    {
        $payload = (new TokenClaims(ActorType::User, 7, Role::Admin, null, null, 'j', 1, 1, 2))->toPayload();
        $payload['sub'] = 'device:7';

        $this->expectException(InvalidArgumentException::class);
        TokenClaims::fromPayload($payload);
    }

    public function test_unknown_role_is_rejected(): void
    {
        $payload = (new TokenClaims(ActorType::User, 7, Role::Admin, null, null, 'j', 1, 1, 2))->toPayload();
        $payload['role'] = 'superadmin';

        $this->expectException(InvalidArgumentException::class);
        TokenClaims::fromPayload($payload);
    }

    public function test_missing_exp_is_rejected(): void
    {
        $payload = (new TokenClaims(ActorType::User, 7, Role::Admin, null, null, 'j', 1, 1, 2))->toPayload();
        unset($payload['exp']);

        $this->expectException(InvalidArgumentException::class);
        TokenClaims::fromPayload($payload);
    }
}
