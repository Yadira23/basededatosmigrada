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
        Schema::create('formularios', function (Blueprint $table) {
            $table->id('id_form');
            $table->string('titulo_form', 200);
            $table->date('fecha_creacion_form');
            $table->text('descripcion_form')->nullable();
            $table->string('boton_accion_form', 50);
            $table->string('secciones_form', 100);
            $table->string('periodo_form', 20);
            $table->foreignId('id_depen')->constrained('dependencias');
            $table->foreignId('id_usr')->constrained('usuarios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formularios');
    }
};
