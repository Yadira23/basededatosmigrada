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
        Schema::table('cargas', function (Blueprint $table) {
            $table->timestamp('plantilla_descargada_at')->nullable()->after('folioUnico_carga');
            $table->string('plantilla_ambito')->nullable()->after('plantilla_descargada_at'); // opcional
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cargas', function (Blueprint $table) {
            //
        });
    }
};
