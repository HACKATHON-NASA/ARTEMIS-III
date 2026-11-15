<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * M1 · Cada valor medido, con el color que le puso M2 y de dónde salió
 * (source). reading_id único: una lectura de una máquina nunca se guarda dos veces.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('measurements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('astronaut_id')->constrained('astronauts')->cascadeOnDelete();
            $table->foreignId('indicator_id')->constrained('indicators')->restrictOnDelete();
            $table->decimal('value', 10, 3);
            $table->string('status', 10)->comment('green · yellow · red (lo pone M2)');
            $table->string('source', 30)->comment('manual · device · simulated_reference · simulated_calibrated_osdr · nasa_donki · simulated_event');
            $table->dateTime('measured_at');
            $table->uuid('reading_id')->nullable()->unique();
            $table->foreignId('ingest_batch_id')->nullable()->constrained('ingest_batches')->nullOnDelete();
            $table->timestamps();

            $table->index(['astronaut_id', 'indicator_id', 'measured_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('measurements');
    }
};
