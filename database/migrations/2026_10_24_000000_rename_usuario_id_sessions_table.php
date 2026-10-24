<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Laravel escribe la sesión en la columna 'user_id' (fijo en DatabaseSessionHandler),
     * por eso se renombra 'usuario_id' para que SESSION_DRIVER=database funcione.
     */
    public function up(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->renameColumn('usuario_id', 'user_id');
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->foreign('user_id')->references('id_usuario')->on('usuario')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->renameColumn('user_id', 'usuario_id');
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->foreign('usuario_id')->references('id_usuario')->on('usuario')->onDelete('cascade');
        });
    }
};
