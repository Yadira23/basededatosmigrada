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
        Schema::create('indicadores', function (Blueprint $table) {
            $table->id('id_ind');
            $table->string('nombre_ind', 200);
            $table->text('definicion_ind');
            $table->text('formula_ind')->nullable();
            $table->string('tendencia_ind', 50);
            $table->string('restriccion_ind', 100)->nullable();
            $table->string('formato_ind', 50);
            $table->string('unidadmedida_ind', 50);
            $table->decimal('meta_ind', 10, 2)->nullable();
            $table->boolean('requerido_ind');
            $table->string('status_ind', 20);
            $table->string('periodo_ind', 20);
            $table->string('etiquetas_ind', 200)->nullable();
            $table->string('fuenteverificacion_ind', 200)->nullable();
    
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicadores');
    }
};
