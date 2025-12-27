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
        Schema::create('detallecargas', function (Blueprint $table) {
            $table->id('id_detalle');
            // Relación con la carga
            $table->foreignId('id_carga')
                ->references('id_carga')
                ->on('cargas')
                ->onDelete('cascade');
            // Relación con indicador
            $table->foreignId('id_ind')
                ->references('id_ind')
                ->on('indicadores')
                ->onDelete('restrict');
            // Relación con región (catálogo)
            $table->foreignId('id_region')
                ->references('id_region')
                ->on('regiones')
                ->onDelete('restrict');
            // 🔴 AQUÍ VA id_mun (municipio)
            $table->foreignId('id_mun')
                ->references('id_mun')
                ->on('municipios')
                ->onDelete('restrict');
            // Atributos propios del detalle
            $table->string('periodo_det');        // mensual, trimestral, anual
            $table->year('ejercicio_det');        // aquí SÍ va el año
            $table->date('fecha_registro_det');
            $table->string('fuente_det');
            $table->decimal('valor_det', 12, 4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detallecargas');
    }
};
