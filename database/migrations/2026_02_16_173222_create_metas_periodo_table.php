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
        Schema::create('metas_periodo', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_ind');
            $table->unsignedBigInteger('id_form')->nullable();
            $table->unsignedSmallInteger('ejercicio');

            $table->string('periodicidad', 20);
            $table->unsignedTinyInteger('segmento');

            $table->unsignedInteger('meta')->default(0);

            $table->timestamps();

            $table->index(['id_ind', 'ejercicio', 'periodicidad']);
            $table->index(['id_form']);
            $table->unique(
                ['id_ind', 'ejercicio', 'periodicidad', 'segmento'],
                'uniq_meta_periodo'
            );

            $table->foreign('id_ind')
                ->references('id_ind')
                ->on('indicadores')
                ->onDelete('cascade');

            $table->foreign('id_form')
                ->references('id_form')
                ->on('formularios')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metas_periodo');
    }
};
