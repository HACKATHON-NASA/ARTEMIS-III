<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Tests\Feature;

use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Tests\Support\InteractsWithJwt;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * La migración de users en MySQL: columnas, tipos, llaves y valores por defecto.
 */
final class UsersTableTest extends TestCase
{
    use InteractsWithJwt;
    use RefreshDatabase;

    public function test_users_has_the_access_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('users', ['role', 'active', 'astronaut_id', 'last_login_at']));
        $this->assertTrue(Schema::hasTable('devices'));
        $this->assertTrue(Schema::hasTable('refresh_tokens'));
    }

    public function test_role_is_read_as_the_role_enum_and_active_defaults_to_true(): void
    {
        $user = $this->userWithRole(Role::CrewMedicalOfficer)->fresh();

        $this->assertNotNull($user);
        $this->assertSame(Role::CrewMedicalOfficer, $user->role);
        $this->assertTrue($user->active);
    }

    public function test_an_astronaut_can_be_linked_to_only_one_user(): void
    {
        $astronaut = $this->astronaut();
        $this->userWithRole(Role::Astronaut, $astronaut);

        $this->expectException(QueryException::class);
        $this->userWithRole(Role::Astronaut, $astronaut);
    }

    public function test_deleting_an_astronaut_unlinks_the_user_instead_of_deleting_it(): void
    {
        $astronaut = $this->astronaut();
        $user = $this->userWithRole(Role::Astronaut, $astronaut);

        $astronaut->delete();

        $this->assertNull($user->fresh()?->astronaut_id);
    }

    public function test_refresh_token_dates_are_datetime_not_auto_updated_timestamps(): void
    {
        $column = (array) DB::selectOne("SHOW COLUMNS FROM refresh_tokens WHERE Field = 'expires_at'");

        $this->assertSame('datetime', $column['Type'] ?? null);
        $this->assertStringNotContainsStringIgnoringCase('on update', (string) ($column['Extra'] ?? ''));
    }

    public function test_fortify_leftovers_are_gone(): void
    {
        $this->assertFalse(Schema::hasTable('passkeys'));
        $this->assertFalse(Schema::hasColumns('users', ['two_factor_secret']));
        $this->assertFalse(Schema::hasColumns('users', ['two_factor_recovery_codes']));
        $this->assertFalse(Schema::hasColumns('users', ['two_factor_confirmed_at']));
    }
}
