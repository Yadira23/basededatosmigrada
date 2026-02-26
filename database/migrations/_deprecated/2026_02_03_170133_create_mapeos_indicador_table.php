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
        Schema::create('mapeos_indicador', function (Blueprint $table) {

            $table->bigIncrements('id_mapeo');

            // Relación lógica (no FK estricta para no romper pruebas)
            $table->unsignedBigInteger('id_ind');
            $table->unsignedBigInteger('id_depen');

            // Columnas de ubicación desde el CSV
            $table->string('col_cve_mun')->nullable();
            $table->string('col_municipio')->nullable();
            $table->string('col_region')->nullable();

            // Mapeo dinámico campo → columna CSV
            // Ej: {"poblacion_total":"Población Total*","hombres":"Hombres*"}
            $table->json('map_campos');

            $table->timestamps();

            $table->index(['id_ind', 'id_depen']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mapeos_indicador');
    }
};
