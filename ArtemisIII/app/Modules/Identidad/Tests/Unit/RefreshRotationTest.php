<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Tests\Unit;

use App\Modules\Identidad\Domain\Enums\RotationDecision;
use App\Modules\Identidad\Domain\Reglas\RefreshRotation;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class RefreshRotationTest extends TestCase
{
    private DateTimeImmutable $now;

    protected function setUp(): void
    {
        $this->now = new DateTimeImmutable('2026-11-15 10:00:00');
    }

    public function test_a_fresh_token_rotates(): void
    {
        $this->assertSame(RotationDecision::Rotate, RefreshRotation::decide(null, null, $this->now->modify('+1 day'), $this->now));
    }

    public function test_a_used_token_revokes_the_whole_family(): void
    {
        $this->assertSame(RotationDecision::RevokeFamily, RefreshRotation::decide($this->now->modify('-1 minute'), null, $this->now->modify('+1 day'), $this->now));
    }

    public function test_a_revoked_token_is_rejected_even_if_it_was_used(): void
    {
        $this->assertSame(RotationDecision::Reject, RefreshRotation::decide($this->now->modify('-2 minutes'), $this->now->modify('-1 minute'), $this->now->modify('+1 day'), $this->now));
    }

    public function test_an_expired_token_is_rejected(): void
    {
        $this->assertSame(RotationDecision::Reject, RefreshRotation::decide(null, null, $this->now, $this->now));
        $this->assertSame(RotationDecision::Reject, RefreshRotation::decide(null, null, $this->now->modify('-1 second'), $this->now));
    }
}
