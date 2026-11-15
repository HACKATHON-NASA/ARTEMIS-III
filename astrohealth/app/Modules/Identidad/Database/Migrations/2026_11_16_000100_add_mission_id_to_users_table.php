<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * M0 · La misión que sigue un médico de vuelo (en Tierra no tiene astronauta
 * del que sacarla). Los tripulantes la toman de su astronauta (M6); admin no
 * sigue ninguna.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('mission_id')->nullable()->after('astronaut_id')
                ->comment('Solo flight_surgeon: la misión que sigue')
                ->constrained('missions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('mission_id');
        });
    }
};
