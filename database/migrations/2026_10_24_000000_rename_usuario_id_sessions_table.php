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
        $this->renombrar('usuario_id', 'user_id');
    }

    public function down(): void
    {
        $this->renombrar('user_id', 'usuario_id');
    }

    private function renombrar(string $desde, string $hacia): void
    {
        // El nombre real de la llave e índice puede variar (en MySQL quedaron como "1"), por eso se buscan
        $llaves = collect(Schema::getForeignKeys('sessions'))
            ->filter(fn ($fk) => $fk['columns'] === [$desde]);
        $indices = collect(Schema::getIndexes('sessions'))
            ->filter(fn ($index) => $index['columns'] === [$desde] && ! $index['primary']);

        Schema::table('sessions', function (Blueprint $table) use ($llaves, $desde) {
            foreach ($llaves as $fk) {
                $table->dropForeign($fk['name'] ?: [$desde]);
            }
        });

        Schema::table('sessions', function (Blueprint $table) use ($indices, $desde, $hacia) {
            foreach ($indices as $index) {
                $table->dropIndex($index['name']);
            }

            $table->renameColumn($desde, $hacia);
        });

        Schema::table('sessions', function (Blueprint $table) use ($hacia) {
            $table->index($hacia);
            $table->foreign($hacia)->references('id_usuario')->on('usuario')->onDelete('cascade');
        });
    }
};
