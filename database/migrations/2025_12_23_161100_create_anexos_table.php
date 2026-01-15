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
        Schema::create('anexos', function (Blueprint $table) {
        $table->id('id_anexo');
        $table->string('nombre_anexo', 200);
        $table->string('tipo_anexo', 50);
        $table->bigInteger('peso_anexo')->nullable();
        $table->text('guia_anexo')->nullable();
        $table->text('fin_proposito_anexo')->nullable();
        $table->date('fecha_subida_anexo');
        $table->string('ruta_archivo_anexo', 255);
        $table->foreignId('id_form')
            ->references('id_form')
            ->on('formularios')
            ->onDelete('cascade');
        $table->foreignId('id_ind')
        ->references('id_ind')
        ->on('indicadores')
        ->onDelete('cascade');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anexos');
    }
};
