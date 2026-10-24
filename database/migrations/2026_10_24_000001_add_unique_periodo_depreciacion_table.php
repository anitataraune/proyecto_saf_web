<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Un activo solo puede tener una depreciación por periodo (mes/año).
     */
    public function up(): void
    {
        Schema::table('depreciacion', function (Blueprint $table) {
            $table->unique(['activo_id', 'anio', 'mes'], 'depreciacion_activo_periodo_unique');
        });
    }

    public function down(): void
    {
        Schema::table('depreciacion', function (Blueprint $table) {
            $table->dropUnique('depreciacion_activo_periodo_unique');
        });
    }
};
