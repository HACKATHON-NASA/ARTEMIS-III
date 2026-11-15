<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * M3 · Las notas clínicas: el expediente privado del equipo médico sobre un
 * tripulante. No se editan ni se borran. Se guarda el nombre de quien la
 * escribió y de quien la resolvió (como en una historia clínica en papel).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinical_notes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('astronaut_id')->constrained('astronauts')->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('author_name', 255);
            $table->string('kind', 20)->comment('evolution · impression · plan · follow_up');
            $table->text('text');
            $table->boolean('follow_up')->default(false)->comment('¿Queda un seguimiento pendiente?');
            $table->dateTime('resolved_at')->nullable();
            $table->foreignId('resolved_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('resolved_by_name', 255)->nullable();
            $table->dateTime('created_at')->useCurrent();

            $table->index(['astronaut_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinical_notes');
    }
};
