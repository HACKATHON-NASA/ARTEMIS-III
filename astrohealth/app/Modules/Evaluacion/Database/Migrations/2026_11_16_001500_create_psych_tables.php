<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * M2 · La guía psicológica: el catálogo de tests (que edita el equipo
 * médico) y los resultados de la tripulación. El puntaje, la banda y el
 * color los calcula M2 al recibir las respuestas; las respuestas se guardan
 * y solo las ve el equipo médico.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('psych_tests', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 120);
            $table->string('kind', 20)->comment('psychological (cuestionario) · cognitive (PVT)');
            $table->text('description');
            $table->text('instructions');
            $table->unsignedSmallInteger('frequency_days')->comment('Cada cuántos días se repite');
            $table->string('unit', 20);
            $table->json('scale')->comment('[{label, points}]');
            $table->json('questions')->comment('[{id, text, reverse, critical}]');
            $table->json('bands')->comment('[{max, label, status}] de menor a mayor');
            $table->string('source', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('psych_results', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('test_id')->constrained('psych_tests')->restrictOnDelete();
            $table->foreignId('astronaut_id')->constrained('astronauts')->cascadeOnDelete();
            $table->unsignedSmallInteger('day')->comment('Día de misión');
            $table->dateTime('taken_at');
            $table->integer('score');
            $table->string('status', 10)->comment('green · yellow · red');
            $table->string('band', 60);
            $table->string('detail', 120)->nullable();
            $table->boolean('flagged')->default(false)->comment('Respuesta de riesgo: se avisó a Tierra');
            $table->json('answers')->comment('Lo que respondió (solo para el equipo médico)');
            $table->dateTime('created_at')->useCurrent();

            $table->index(['astronaut_id', 'test_id', 'taken_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('psych_results');
        Schema::dropIfExists('psych_tests');
    }
};
