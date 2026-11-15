<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * M3 · Las zonas del cuerpo: qué hacen y cómo las afecta el espacio (NASA HRP).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('body_parts', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 20)->unique()->comment('heart · brain · eyes · lungs · bones · ear · immune · metabolism · whole_body');
            $table->string('name', 60);
            $table->text('function_text')->comment('Qué hace');
            $table->text('space_risk_text')->comment('Cómo la afecta el espacio');
            $table->string('hrp_hazard', 60)->comment('El peligro de la NASA HRP con el que se relaciona');
            $table->unsignedTinyInteger('sort_order');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('body_parts');
    }
};
