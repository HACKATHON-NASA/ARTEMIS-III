<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * M8 · La rutina diaria de cada tripulante. Un recordatorio está hecho hoy
 * si last_done_at es de hoy (UTC, como el día de misión): no hace falta
 * reiniciar nada a medianoche.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('astronaut_id')->constrained('astronauts')->cascadeOnDelete();
            $table->string('type', 20)->comment('measure_bp · exercise · log_sleep · mood_check · dosimeter · custom');
            $table->string('title', 80);
            $table->time('time')->comment('Hora del día');
            $table->boolean('active')->default(true);
            $table->dateTime('last_done_at')->nullable();
            $table->timestamps();

            $table->index(['astronaut_id', 'time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
