<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Tests\Feature;

use App\Models\User;
use App\Modules\Entorno\Infrastructure\Models\Astronaut;
use App\Modules\Entorno\Infrastructure\Models\Mission;
use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Tests\Support\InteractsWithJwt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Las notas clínicas: privadas del equipo médico de la misión.
 */
final class ClinicalNotesApiTest extends TestCase
{
    use InteractsWithJwt;
    use RefreshDatabase;

    private Mission $mission;

    private Astronaut $patient;

    private User $surgeon;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mission = $this->mission();
        $this->patient = $this->astronaut($this->mission);
        $this->surgeon = User::factory()->create(['name' => 'Dra. Sofía Ramírez', 'role' => Role::FlightSurgeon, 'mission_id' => $this->mission->id]);
    }

    /** @return array<string, string> */
    private function as(User $user): array
    {
        return $this->bearer($this->tokenFor($user));
    }

    private function url(): string
    {
        return "/api/v1/astronauts/{$this->patient->id}/clinical-notes";
    }

    public function test_a_doctor_of_the_mission_writes_and_reads_notes(): void
    {
        $this->postJson($this->url(), ['kind' => 'plan', 'text' => 'Control de presión por 3 días.', 'follow_up' => true, 'author' => 'Otro nombre'], $this->as($this->surgeon))
            ->assertCreated()
            ->assertJsonPath('data.author', 'Dra. Sofía Ramírez')
            ->assertJsonPath('data.kind', 'plan')
            ->assertJsonPath('data.follow_up', true)
            ->assertJsonPath('data.resolved_at', null);

        $cmo = $this->userWithRole(Role::CrewMedicalOfficer, $this->astronaut($this->mission));
        $this->postJson($this->url(), ['kind' => 'evolution', 'text' => 'Duerme mejor desde ayer.'], $this->as($cmo))->assertCreated();

        $this->getJson($this->url(), $this->as($cmo))
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.kind', 'evolution');
    }

    public function test_the_crew_and_the_admin_cannot_see_the_notes(): void
    {
        $this->getJson($this->url(), $this->as($this->userWithRole(Role::Astronaut, $this->patient)))->assertForbidden();
        $this->getJson($this->url(), $this->as($this->userWithRole(Role::Admin)))->assertForbidden();
    }

    public function test_a_doctor_of_another_mission_cannot_see_or_resolve(): void
    {
        $note = $this->postJson($this->url(), ['kind' => 'plan', 'text' => 'Revisar mañana.', 'follow_up' => true], $this->as($this->surgeon))->json('data.id');
        $stranger = User::factory()->create(['role' => Role::FlightSurgeon, 'mission_id' => $this->mission()->id]);

        $this->getJson($this->url(), $this->as($stranger))->assertForbidden();
        $this->patchJson("/api/v1/clinical-notes/{$note}/resolve", [], $this->as($stranger))
            ->assertForbidden()
            ->assertJsonPath('error_code', 'BODY_NOT_YOUR_PATIENT');
    }

    public function test_a_pending_follow_up_is_resolved_once(): void
    {
        $note = $this->postJson($this->url(), ['kind' => 'follow_up', 'text' => 'Revisar el PHQ-9.', 'follow_up' => true], $this->as($this->surgeon))->json('data.id');

        $this->patchJson("/api/v1/clinical-notes/{$note}/resolve", [], $this->as($this->surgeon))
            ->assertOk()
            ->assertJsonPath('data.resolved_by', 'Dra. Sofía Ramírez');

        $this->patchJson("/api/v1/clinical-notes/{$note}/resolve", [], $this->as($this->surgeon))
            ->assertStatus(409)
            ->assertJsonPath('error_code', 'BODY_NOTE_NOT_PENDING');

        $this->patchJson('/api/v1/clinical-notes/999/resolve', [], $this->as($this->surgeon))
            ->assertNotFound()
            ->assertJsonPath('error_code', 'BODY_NOTE_NOT_FOUND');
    }

    public function test_the_format_is_validated(): void
    {
        $this->postJson($this->url(), ['kind' => 'chisme', 'text' => 'no'], $this->as($this->surgeon))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['kind', 'text']);
    }
}
