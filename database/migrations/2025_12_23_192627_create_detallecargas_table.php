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

            // ✅ NUEVO
            $table->enum('ambito_geo', ['SIN_AMBITO', 'REGION', 'MUNICIPIO'])->default('SIN_AMBITO');

            // geo (siguen siendo catálogos, NO se rellenan aquí)
            $table->foreignId('id_region')->nullable()
                ->references('id_region')->on('regiones')
                ->onDelete('restrict')->onUpdate('cascade');

            $table->foreignId('id_mun')->nullable()
                ->references('id_mun')->on('municipios')
                ->onDelete('restrict')->onUpdate('cascade');

            // ✅ NUEVO: consecutivo de fila para permitir "varias filas"
            $table->unsignedInteger('fila_det')->default(1);

            $table->string('periodo_det');        // mensual, trimestral, anual
            $table->year('ejercicio_det');        // aquí SÍ va el año
            $table->date('fecha_registro_det');
            $table->string('fuente_det');

            // ✅ ahora puede ser NULL para indicadores tabulares
            $table->decimal('valor_det', 12, 4)->nullable();

            // ✅ NUEVO: datos dinámicos de la fila (TABULAR)
            $table->json('payload_det')->nullable();

            // ✅ SIN_AMBITO
            $table->unique(
                ['id_carga', 'id_ind', 'ambito_geo', 'periodo_det', 'ejercicio_det', 'fila_det'],
                'uq_detallecargas_sin_ambito'
            );

            // REGION
            $table->unique(
                ['id_carga', 'id_ind', 'ambito_geo', 'id_region', 'periodo_det', 'ejercicio_det', 'fila_det'],
                'uq_detallecargas_region'
            );

            // MUNICIPIO
            $table->unique(
                ['id_carga', 'id_ind', 'ambito_geo', 'id_mun', 'periodo_det', 'ejercicio_det', 'fila_det'],
                'uq_detallecargas_municipio'
            );

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
