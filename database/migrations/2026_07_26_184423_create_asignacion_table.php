<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignacion', function (Blueprint $table) {
            $table->id('id_asignacion');
            
            $table->foreignId('activo_id')
                  ->constrained('activo', 'id_activo')
                  ->onDelete('cascade');
                  
            $table->foreignId('responsable_id')
                  ->constrained('responsable', 'id_responsable')
                  ->onDelete('cascade');

            $table->date('fecha_asignacion');
            $table->date('fecha_devolucion')->nullable(); // null indica que la asignación sigue ACTIVA
            $table->string('estado_asignacion')->default('asignado'); // 'asignado', 'devuelto'
            $table->text('observacion')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignacion');
    }
};