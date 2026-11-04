<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // El monto no participaba en ningún cálculo; la depreciación solo usa fecha y ufvs
    public function up(): void
    {
        Schema::table('tipo_cambio', function (Blueprint $table) {
            $table->dropColumn('monto');
        });
    }

    public function down(): void
    {
        Schema::table('tipo_cambio', function (Blueprint $table) {
            $table->decimal('monto', 10, 2)->default(0)->after('valor_tc');
        });
    }
};
