<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * M0 · Identidad: rol, estado y enlace con el astronauta.
 * No se edita la migración original de users (regla P11): se agregan columnas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role', 30)->nullable()->after('email')
                ->comment('astronaut · crew_medical_officer · flight_surgeon · admin');
            $table->boolean('active')->default(true)->after('role');
            $table->foreignId('astronaut_id')->nullable()->unique()->after('active')
                ->constrained('astronauts')->nullOnDelete();
            $table->dateTime('last_login_at')->nullable()->after('remember_token');

            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('astronaut_id');
            $table->dropIndex(['role']);
            $table->dropColumn(['role', 'active', 'last_login_at']);
        });
    }
};
