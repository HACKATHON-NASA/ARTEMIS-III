<?php

declare(strict_types=1);

namespace Tests\Feature\Shared;

use App\Models\User;
use App\Shared\Persistencia\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

final class DatabaseTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_the_callback_result(): void
    {
        $result = app(Transaction::class)->run(fn (): int => 42);

        $this->assertSame(42, $result);
    }

    public function test_it_rolls_back_everything_when_something_fails(): void
    {
        try {
            app(Transaction::class)->run(function (): void {
                User::factory()->create(['email' => 'a@astrohealth.test']);
                throw new RuntimeException('falla a mitad');
            });
        } catch (RuntimeException) {
            // esperado
        }

        $this->assertDatabaseMissing('users', ['email' => 'a@astrohealth.test']);
    }
}
