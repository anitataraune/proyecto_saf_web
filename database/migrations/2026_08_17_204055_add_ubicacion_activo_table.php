<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activo', function (Blueprint $table) {
            // Agregamos el campo. Se recomienda ponerlo después de categoria_id por orden
            $table->unsignedBigInteger('ubicacion_id')->nullable()->after('categoria_id');

            // Definimos la relación de llave foránea
            $table->foreign('ubicacion_id')->references('id_ubicacion')->on('ubicacion')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activo', function (Blueprint $table) {
            // Primero eliminamos la llave foránea, luego la columna
            $table->dropForeign(['ubicacion_id']);
            $table->dropColumn('ubicacion_id');
        });
    }
};