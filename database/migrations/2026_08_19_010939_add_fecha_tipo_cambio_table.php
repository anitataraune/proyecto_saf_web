<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tipo_cambio', function (Blueprint $table) {
            $table->date('fecha')->unique()->after('id_tipo_cambio');
        });
    }

    public function down(): void
    {
        Schema::table('tipo_cambio', function (Blueprint $table) {
            $table->dropColumn('fecha');
        });
    }
};