<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * M5 · "Qué hacer" ante cada indicador en amarillo o en rojo. Se usa el
 * código del indicador (como en thresholds) para no atarse a sus ids.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recommendations', function (Blueprint $table): void {
            $table->id();
            $table->string('indicator_code', 20);
            $table->string('status', 10)->comment('yellow · red');
            $table->string('title', 80);
            $table->text('action_text')->comment('Pasos, uno por línea');
            $table->string('risk_ref', 255)->nullable()->comment('Riesgo de la NASA HRP');
            $table->timestamps();

            $table->unique(['indicator_code', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendations');
    }
};
