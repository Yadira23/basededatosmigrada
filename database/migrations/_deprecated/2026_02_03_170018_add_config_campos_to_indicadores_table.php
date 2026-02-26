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
        Schema::table('indicadores', function (Blueprint $table) {
            // Configuración dinámica de campos del indicador
            // Ej: población total, hombres, mujeres, etc.
            $table->json('config_campos')
                ->nullable()
                ->after('fuenteverificacion_ind');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indicadores', function (Blueprint $table) {
            $table->dropColumn('config_campos');
        });
    }
};
