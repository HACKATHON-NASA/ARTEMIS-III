<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * M1 · El catálogo de indicadores: unidad, zona del cuerpo, rango
 * físicamente posible (para rechazar errores, no es clínico) y "¿Cómo me mido?".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indicators', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 20)->unique()->comment('hr · bp_sys · bp_dia · spo2 · temp · glucose · sleep · mood · exercise_min · radiation');
            $table->string('name', 60);
            $table->string('unit', 10);
            $table->string('body_part', 20)->comment('Zona del cuerpo (M3)');
            $table->decimal('min_possible', 10, 3);
            $table->decimal('max_possible', 10, 3);
            $table->decimal('step', 6, 3)->default(1);
            $table->unsignedTinyInteger('sort_order');
            $table->text('help_how')->comment('¿Cómo me mido?');
            $table->text('help_why')->comment('Por qué importa en el espacio');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indicators');
    }
};
