<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('depreciacion', function (Blueprint $table) {
            // Si manejas un tipo de cambio específico para el cálculo:
            $table->unsignedBigInteger('tipo_cambio_id')->nullable()->after('metodo_depreciacion');
            
            $table->foreign('tipo_cambio_id')
                  ->references('id_tipo_cambio')
                  ->on('tipo_cambio')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('depreciacion', function (Blueprint $table) {
            $table->dropForeign(['tipo_cambio_id']);
            $table->dropColumn('tipo_cambio_id');
        });
    }
};