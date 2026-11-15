<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * M5 · Las alertas: un valor en amarillo o rojo (vital), un SOS, un pedido
 * de ayuda o una novedad. Todas salen hacia Tierra al crearse y llegan con
 * el retraso del entorno (ground_eta_at). Tierra las atiende y responde.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alerts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('astronaut_id')->constrained('astronauts')->cascadeOnDelete();
            $table->foreignId('measurement_id')->nullable()->unique()
                ->comment('Una alerta por medición (solo las vitales)')
                ->constrained('measurements')->nullOnDelete();
            $table->string('kind', 10)->comment('vital · sos · help · report');
            $table->string('indicator_code', 20)->nullable();
            $table->string('level', 10)->comment('red · yellow · info');
            $table->string('message', 255);
            $table->string('category', 60)->nullable()->comment('El motivo de un pedido de ayuda');
            $table->text('note')->nullable()->comment('Lo que escribió el astronauta');
            $table->string('source', 10)->comment('device · manual · astronaut');
            $table->unsignedInteger('delay_seconds')->comment('Retraso de una vía con Tierra al enviarla');
            $table->dateTime('sent_at');
            $table->dateTime('ground_eta_at');
            $table->dateTime('acknowledged_at')->nullable();
            $table->string('acknowledged_by_name', 255)->nullable();
            $table->text('reply')->nullable()->comment('La respuesta de Tierra');
            $table->timestamps();

            $table->index(['astronaut_id', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
