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
        Schema::create('activo', function (Blueprint $table) {
            $table->id('id_activo');
            $table->string('descripcion');
            // Para dinero siempre usa decimal (12 enteros, 2 decimales por ejemplo)
            $table->decimal('valor_compra', 12, 2); 
            $table->date('fecha_compra');
            $table->integer('nro_depreciacion')->default(0); // Meses o años transcurridos
            $table->decimal('imp_depreciacion', 12, 2)->default(0.00); // Importe acumulado
            $table->string('estado_actual')->default('activo'); // activo, depreciado, dado_de_baja
    
            // Llave foránea de categoría (Ajusta 'id_categoria' si tu tabla usa otro nombre)
            $table->unsignedBigInteger('categoria_id');
            $table->foreign('categoria_id')->references('id_categoria')->on('categoria');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activo');
    }
};
