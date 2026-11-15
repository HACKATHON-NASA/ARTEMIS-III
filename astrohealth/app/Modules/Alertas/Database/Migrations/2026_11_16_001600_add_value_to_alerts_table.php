<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * M5 · El valor medido de una alerta vital. Con el indicador, el nivel y el
 * valor, la pantalla arma el texto de la alerta en el idioma de quien la lee
 * ("Systolic pressure 148 mmHg (critical)"). `message` se queda como el
 * texto original, para quien no tenga traducción.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alerts', function (Blueprint $table): void {
            $table->decimal('value', 10, 2)->nullable()->after('indicator_code')->comment('Solo en las vitales: el valor que la creó');
        });
    }

    public function down(): void
    {
        Schema::table('alerts', function (Blueprint $table): void {
            $table->dropColumn('value');
        });
    }
};
