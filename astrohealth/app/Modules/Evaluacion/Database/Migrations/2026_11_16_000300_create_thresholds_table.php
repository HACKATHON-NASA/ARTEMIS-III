<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * M2 · Los rangos del semáforo por indicador y entorno. Se usa el código del
 * indicador (no su id) para que M2 no dependa de M1.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thresholds', function (Blueprint $table): void {
            $table->id();
            $table->string('indicator_code', 20);
            $table->string('environment', 20)->comment('iss · transit · moon · mars');
            $table->decimal('green_min', 8, 2);
            $table->decimal('green_max', 8, 2);
            $table->decimal('yellow_min', 8, 2);
            $table->decimal('yellow_max', 8, 2);
            $table->timestamps();

            $table->unique(['indicator_code', 'environment']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thresholds');
    }
};
