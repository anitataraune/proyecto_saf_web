<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('baja', function (Blueprint $table) {
            $table->text('observacion')->nullable()->after('motivo_baja');
        });
    }

    public function down(): void
    {
        Schema::table('baja', function (Blueprint $table) {
            $table->dropColumn('observacion');
        });
    }
};
