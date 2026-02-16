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
        Schema::create('metas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_ind'); // FK a indicadores.id_ind
            $table->string('titulo', 200);        // "Condiciones de vivienda"
            $table->string('periodo', 50)->nullable(); // "S1", "2026-S1"
            $table->unsignedInteger('orden')->default(1);

            // ✅ Igual que tu indicador: JSON de configuración de campos, pero por meta
            $table->json('config_campos')->nullable();

            $table->timestamps();

            $table->foreign('id_ind')
                ->references('id_ind')
                ->on('indicadores')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metas');
    }
};
