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
        Schema::create('configuraciones', function (Blueprint $table) {
            $table->id('id_configuracion');

            // Identidad de la Empresa / Sistema
            $table->string('nombre_sistema')->default('Sistema de Activos Fijos');
            $table->string('nombre_empresa')->default('SAF');
            $table->string('nit_empresa')->nullable();
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();

            // Marca e Imágenes (Rutas de archivos guardados en storage)
            $table->string('logo')->nullable();

            // Paleta de 5 Colores (Códigos HEX)
            $table->string('color_primario', 7)->default('#2563EB');    // Azul principal
            $table->string('color_secundario', 7)->default('#1E293B');  // Slate oscuro
            $table->string('color_fondo', 7)->default('#F8FAFC');       // Gris claro para pantallas
            $table->string('color_superficie', 7)->default('#FFFFFF');  // Blanco para tarjetas/modales
            $table->string('color_texto', 7)->default('#0F172A');       // Texto principal

            // Tipografía
            $table->string('fuente')->default('Roboto');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuraciones');
    }
};