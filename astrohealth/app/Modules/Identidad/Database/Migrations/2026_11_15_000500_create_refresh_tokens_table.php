<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * M0 · Identidad: refresh tokens rotativos. Solo se guarda el hash SHA-256.
 *
 * Las fechas son DATETIME y no TIMESTAMP: en MariaDB 10.4
 * (explicit_defaults_for_timestamp = OFF) la primera columna TIMESTAMP NOT NULL
 * recibe ON UPDATE CURRENT_TIMESTAMP y expires_at cambiaría en cada UPDATE.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refresh_tokens', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->uuid('family_id')->index();
            $table->char('token_hash', 64)->unique();
            $table->dateTime('expires_at');
            $table->dateTime('used_at')->nullable();
            $table->dateTime('revoked_at')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->dateTime('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refresh_tokens');
    }
};
