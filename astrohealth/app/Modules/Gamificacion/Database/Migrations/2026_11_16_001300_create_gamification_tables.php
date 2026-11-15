<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * M7 · Las insignias, las que ganó cada tripulante y el registro de puntos.
 * La llave única de points_log hace que una acción sume una sola vez.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 60);
            $table->string('icon', 16);
            $table->string('condition', 120)->comment('Cómo se gana, en simple');
            $table->unsignedSmallInteger('bonus')->comment('Puntos extra al ganarla');
            $table->unsignedSmallInteger('target')->comment('La meta (días, veces o tests)');
            $table->unsignedTinyInteger('sort_order');
            $table->timestamps();
        });

        Schema::create('astronaut_achievements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('astronaut_id')->constrained('astronauts')->cascadeOnDelete();
            $table->foreignId('achievement_id')->constrained('achievements')->cascadeOnDelete();
            $table->unsignedSmallInteger('earned_day')->comment('Día de misión en que se ganó');
            $table->dateTime('earned_at');

            $table->unique(['astronaut_id', 'achievement_id']);
        });

        Schema::create('points_log', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('astronaut_id')->constrained('astronauts')->cascadeOnDelete();
            $table->smallInteger('points');
            $table->string('reason', 20)->comment('daily_complete · streak_7 · sleep_7h · exercise · reminder · psych_test · badge');
            $table->string('reference', 60)->comment('Qué lo ganó (el día, el recordatorio, el test, la insignia)');
            $table->unsignedSmallInteger('day')->comment('Día de misión');
            $table->dateTime('created_at');

            $table->unique(['astronaut_id', 'reason', 'reference']);
            $table->index(['astronaut_id', 'day']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('points_log');
        Schema::dropIfExists('astronaut_achievements');
        Schema::dropIfExists('achievements');
    }
};
