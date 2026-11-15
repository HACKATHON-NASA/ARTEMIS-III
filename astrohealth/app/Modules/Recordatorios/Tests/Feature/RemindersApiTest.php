<?php

declare(strict_types=1);

namespace App\Modules\Recordatorios\Tests\Feature;

use App\Models\User;
use App\Modules\Entorno\Infrastructure\Models\Astronaut;
use App\Modules\Gamificacion\Database\Seeders\AchievementSeeder;
use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Tests\Support\InteractsWithJwt;
use App\Modules\Recordatorios\Database\Seeders\ReminderSeeder;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * /api/v1/reminders: cada tripulante maneja su rutina; marcar uno hecho
 * suma +5 en M7 una sola vez por día. La misión de prueba va en el día 24.
 */
final class RemindersApiTest extends TestCase
{
    use InteractsWithJwt;
    use RefreshDatabase;

    private Astronaut $elena;

    private User $elenaUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->elena = $this->astronaut($this->mission());
        $this->elenaUser = $this->userWithRole(Role::Astronaut, $this->elena);
        $this->seed([ReminderSeeder::class, AchievementSeeder::class]);
    }

    /** @return array<string, string> */
    private function as(User $user): array
    {
        return $this->bearer($this->tokenFor($user));
    }

    /** @return list<array<string, mixed>> */
    private function mine(): array
    {
        return $this->getJson('/api/v1/reminders', $this->as($this->elenaUser))->assertOk()->json('data');
    }

    private function first(): int
    {
        return (int) $this->mine()[0]['id'];
    }

    public function test_each_crew_member_starts_with_the_default_routine(): void
    {
        $items = $this->mine();

        $this->assertSame(['08:00', '10:00', '12:00', '18:00', '21:00'], array_column($items, 'time'));
        $this->assertSame('measure_bp', $items[0]['type']);
        $this->assertSame('Hora de medir tu presión 🩺', $items[0]['title']);
        $this->assertFalse($items[0]['done_today']);
        $this->assertNull($items[0]['last_done_at']);
    }

    public function test_the_seeder_does_not_duplicate_the_routine(): void
    {
        $this->seed(ReminderSeeder::class);

        $this->assertCount(5, $this->mine());
    }

    public function test_create_change_and_delete_a_reminder(): void
    {
        $id = $this->postJson('/api/v1/reminders', ['type' => 'custom', 'title' => '  Llamar a casa 📞 ', 'time' => '07:30'], $this->as($this->elenaUser))
            ->assertCreated()
            ->assertJsonPath('data.title', 'Llamar a casa 📞')
            ->assertJsonPath('data.active', true)
            ->json('data.id');

        $this->assertSame('07:30', $this->mine()[0]['time'], 'queda primero por la hora');

        $this->patchJson("/api/v1/reminders/{$id}", ['time' => '22:15', 'active' => false], $this->as($this->elenaUser))
            ->assertOk()
            ->assertJsonPath('data.time', '22:15')
            ->assertJsonPath('data.active', false)
            ->assertJsonPath('data.title', 'Llamar a casa 📞');

        $this->deleteJson("/api/v1/reminders/{$id}", [], $this->as($this->elenaUser))->assertNoContent();
        $this->assertCount(5, $this->mine());
    }

    public function test_the_format_is_checked(): void
    {
        $this->postJson('/api/v1/reminders', ['type' => 'nap', 'title' => '', 'time' => '25:00'], $this->as($this->elenaUser))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['type', 'title', 'time']);
    }

    public function test_completing_a_reminder_gives_five_points_once_a_day(): void
    {
        $id = $this->first();

        $this->postJson("/api/v1/reminders/{$id}/done", [], $this->as($this->elenaUser))
            ->assertOk()
            ->assertJsonPath('data.done_today', true);
        $this->postJson("/api/v1/reminders/{$id}/done", [], $this->as($this->elenaUser))
            ->assertStatus(409)
            ->assertJsonPath('error_code', 'REMINDER_ALREADY_DONE');

        $profile = $this->getJson("/api/v1/astronauts/{$this->elena->id}/gamification", $this->as($this->elenaUser))->json('data');
        $this->assertSame(5, $profile['points']);
        $this->assertSame('reminder', $profile['recent'][0]['reason']);
        $this->assertSame("{$id}-24", $profile['recent'][0]['reference']);
    }

    public function test_tomorrow_it_can_be_done_again(): void
    {
        $id = $this->first();
        $this->postJson("/api/v1/reminders/{$id}/done", [], $this->as($this->elenaUser))->assertOk();

        $this->travelTo(CarbonImmutable::now()->utc()->addDay()->setTime(9, 0));

        $this->assertFalse($this->mine()[0]['done_today']);
        $this->postJson("/api/v1/reminders/{$id}/done", [], $this->as($this->elenaUser))->assertOk();

        $profile = $this->getJson("/api/v1/astronauts/{$this->elena->id}/gamification", $this->as($this->elenaUser))->json('data');
        $this->assertSame(10, $profile['points']);
    }

    public function test_nobody_touches_someone_elses_reminders(): void
    {
        $kenji = $this->userWithRole(Role::Astronaut, $this->astronaut());
        $id = $this->first();

        $this->patchJson("/api/v1/reminders/{$id}", ['title' => 'Mío'], $this->as($kenji))->assertNotFound()->assertJsonPath('error_code', 'REMINDER_NOT_FOUND');
        $this->deleteJson("/api/v1/reminders/{$id}", [], $this->as($kenji))->assertNotFound();
        $this->postJson("/api/v1/reminders/{$id}/done", [], $this->as($kenji))->assertNotFound();
        $this->assertFalse($this->mine()[0]['done_today']);
    }

    public function test_only_the_crew_has_a_routine(): void
    {
        $surgeon = User::factory()->create(['role' => Role::FlightSurgeon]);

        $this->getJson('/api/v1/reminders', $this->as($surgeon))->assertForbidden();
        $this->getJson('/api/v1/reminders')->assertUnauthorized();
    }
}
