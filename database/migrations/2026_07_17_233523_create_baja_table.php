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
        Schema::create('baja', function (Blueprint $table) {
            $table->id('id_baja');
            $table->foreignId('activo_id')
                  ->constrained('activo', 'id_activo')
                  ->onDelete('cascade');
            $table->date('fecha_baja');
            $table->string('motivo_baja');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('baja');
    }
};
