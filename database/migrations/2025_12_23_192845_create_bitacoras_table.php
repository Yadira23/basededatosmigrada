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
        Schema::create('bitacoras', function (Blueprint $table) {
            $table->id('id_bitacora');
            $table->foreignId('id_usuario')
                ->constrained('usuarios')
                ->onDelete('cascade');

            $table->foreignId('id_carga')
                ->constrained('cargas')
                ->onDelete('cascade');

            $table->string('accion_bit');
            $table->text('descripcion_bit')->nullable();
            $table->timestamp('fecha_bit');
            $table->string('ip_origen_bit', 45);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bitacoras');
    }
};
