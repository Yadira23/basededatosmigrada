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
        Schema::create('cargas', function (Blueprint $table) {
            $table->id('id_carga');
            $table->string('folioUnico_carga')->unique();
            $table->date('fecha_carga');
            $table->string('periodo'); // mensual, trimestral, anual
            $table->year('ejercicio');          // año, ej. 2025
            $table->string('fuente');           // fuente de información
            $table->string('status_env'); // enviado, en revisión, aprobado, rechazado
            $table->enum('ambito_geo_carga', ['SIN_AMBITO', 'REGION', 'MUNICIPIO'])
                ->default('SIN_AMBITO');
            $table->enum('metodo_captura', ['MANUAL', 'ARCHIVO'])->default('MANUAL');
            $table->text('descripcion_env')->nullable();
            $table->text('observacion_env')->nullable();
            $table->foreignId('id_form')
                ->references('id_form')
                ->on('formularios')
                ->onDelete('cascade');
                
            $table->unsignedBigInteger('meta_id')->nullable()->after('id_form');

            $table->foreign('meta_id')
                ->references('id')
                ->on('metas')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cargas');
    }
};
