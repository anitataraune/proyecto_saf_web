<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activo_poliza', function (Blueprint $table) {
            $table->id();
            
            // Llaves foráneas
            $table->unsignedBigInteger('activo_id');
            $table->unsignedBigInteger('poliza_seguro_id');
            $table->date('fecha_asignacion');
            
            $table->foreign('activo_id')->references('id_activo')->on('activo')->onDelete('cascade');
            $table->foreign('poliza_seguro_id')->references('id_seguro')->on('poliza_seguro')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activo_poliza');
    }
};