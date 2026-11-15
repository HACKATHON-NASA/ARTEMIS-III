<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * M2 · Las evaluaciones de cada astronauta: una fila por evaluación, así
 * queda el historial del puntaje.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('astronaut_id')->constrained('astronauts')->cascadeOnDelete();
            $table->unsignedTinyInteger('score')->comment('0–100');
            $table->json('body_status')->comment('{ "heart": "yellow", "brain": "green", … }');
            $table->string('summary', 255);
            $table->dateTime('created_at')->useCurrent();

            $table->index(['astronaut_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
