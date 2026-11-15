<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * M5 · Las indicaciones del médico a un tripulante. Viajan a la nave con el
 * retraso del entorno (arrives_at) y el tripulante las marca como leídas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advice', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('astronaut_id')->constrained('astronauts')->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('author_name', 255);
            $table->string('category', 20)->comment('sleep · exercise · nutrition · medication · rest · mental · other');
            $table->string('title', 80);
            $table->text('body')->comment('Pasos, uno por línea');
            $table->boolean('important')->default(false);
            $table->unsignedSmallInteger('valid_days')->nullable();
            $table->unsignedInteger('delay_seconds');
            $table->dateTime('sent_at');
            $table->dateTime('arrives_at');
            $table->dateTime('read_at')->nullable();
            $table->timestamps();

            $table->index(['astronaut_id', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advice');
    }
};
