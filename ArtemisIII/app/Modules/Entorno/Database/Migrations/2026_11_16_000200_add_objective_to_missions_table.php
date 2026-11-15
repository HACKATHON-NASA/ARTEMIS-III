<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * M6 · El objetivo de la misión (lo escribe el admin al crearla).
 * El estado (planificada, en curso, terminada) no se guarda: sale de las
 * fechas con la regla MissionCalendar.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('missions', function (Blueprint $table): void {
            $table->string('objective', 255)->nullable()->after('day_count');
            $table->unique('name');
        });
    }

    public function down(): void
    {
        Schema::table('missions', function (Blueprint $table): void {
            $table->dropUnique(['name']);
            $table->dropColumn('objective');
        });
    }
};
