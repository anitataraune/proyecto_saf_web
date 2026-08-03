<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('depreciacion', function (Blueprint $table) {
            $table->id('id_depreciacion');

            // Relación con el activo
            $table->foreignId('activo_id')
                  ->constrained('activo', 'id_activo')
                  ->onDelete('cascade');

            // Periodo
            $table->integer('mes'); // 1 - 12
            $table->integer('anio'); // Ej. 2026
            $table->date('fecha_proceso');

            // Método
            $table->string('metodo_depreciacion')->default('linea_recta'); // linea_recta, ufv, etc.

            // Valores de Cálculo
            $table->decimal('ufv_inicio', 10, 5)->nullable();
            $table->decimal('ufv_fin', 10, 5)->nullable();
            $table->decimal('valor_inicial', 12, 2);       // Valor previo al proceso
            $table->decimal('actualizacion_valor', 12, 2)->default(0); // Incremento por UFV
            $table->decimal('valor_actualizado', 12, 2);   // Valor inicial + Actualización
            $table->decimal('depreciacion_periodo', 12, 2);// Depreciación calculada este mes
            $table->decimal('depreciacion_acumulada', 12, 2); // Acumulado hasta este mes
            $table->decimal('valor_neto', 12, 2);          // Valor residual/neto en libros

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depreciacion');
    }
};