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
        Schema::table('detallecargas', function (Blueprint $table) {
            if (!Schema::hasColumn('detallecargas', 'meta_id')) {
                $table->unsignedBigInteger('meta_id')->nullable()->index()->after('id_ind');
            }
        });

        // Foreign key (opcional pero recomendado)
        Schema::table('detallecargas', function (Blueprint $table) {
            // Evita error si ya existiera por alguna razón
            try {
                $table->foreign('meta_id')
                    ->references('id')      // ✅ metas.id (porque en tu Meta usas localKey 'id')
                    ->on('metas')
                    ->nullOnDelete();
            } catch (\Throwable $e) {
                // si ya existe la FK o tu motor no lo permite, no truena el deploy
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detallecargas', function (Blueprint $table) {
            // si existe la FK, la quitamos
            try { $table->dropForeign(['meta_id']); } catch (\Throwable $e) {}
            if (Schema::hasColumn('detallecargas', 'meta_id')) {
                $table->dropColumn('meta_id');
            }
        });
    }
};
