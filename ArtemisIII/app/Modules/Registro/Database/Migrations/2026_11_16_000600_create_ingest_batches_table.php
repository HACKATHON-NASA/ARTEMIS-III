<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * M1 · Los lotes que envían las máquinas. El batch_id único hace que
 * reenviar el mismo lote devuelva el mismo resumen sin guardar nada.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingest_batches', function (Blueprint $table): void {
            $table->id();
            $table->uuid('batch_id')->unique();
            $table->foreignId('device_id')->constrained('devices')->cascadeOnDelete();
            $table->unsignedSmallInteger('received');
            $table->unsignedSmallInteger('stored');
            $table->unsignedSmallInteger('duplicates');
            $table->json('rejected')->comment('[{reading_id, error_code, message}]');
            $table->dateTime('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingest_batches');
    }
};
