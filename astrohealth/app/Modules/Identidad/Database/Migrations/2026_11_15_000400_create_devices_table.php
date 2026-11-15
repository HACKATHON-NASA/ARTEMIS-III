<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * M0 · Identidad: las máquinas que envían lecturas (el simulador o los sensores).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('mission_id')->constrained('missions')->cascadeOnDelete();
            $table->foreignId('astronaut_id')->nullable()->comment('null = gateway de toda la misión')
                ->constrained('astronauts')->nullOnDelete();
            $table->string('name', 80);
            $table->string('client_id', 100)->unique();
            $table->string('secret_hash');
            $table->boolean('active')->default(true);
            $table->dateTime('last_seen_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
