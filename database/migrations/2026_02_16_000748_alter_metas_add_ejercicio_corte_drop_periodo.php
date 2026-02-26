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
        Schema::table('metas', function (Blueprint $table) {
            // ✅ Nuevos campos “limpios”
            $table->unsignedSmallInteger('ejercicio')->nullable()->after('titulo'); // 2026
            $table->unsignedTinyInteger('corte')->nullable()->after('ejercicio');   // 1..12, 1..4, 1..2, 1

            // ⚠️ Si ya tienes datos, primero lo dejamos nullable para migrar datos sin romper nada.
            // Después (en otra migración) puedes hacerlo NOT NULL si quieres.
        });

        // ✅ Si quieres conservar periodo solo mientras migras datos, NO lo borres aún.
        // Si tu tabla está nueva o no te importa perder los valores, puedes borrarlo de una vez:
        Schema::table('metas', function (Blueprint $table) {
            if (Schema::hasColumn('metas', 'periodo')) {
                $table->dropColumn('periodo');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('metas', function (Blueprint $table) {
            // Revertir
            if (! Schema::hasColumn('metas', 'periodo')) {
                $table->string('periodo', 50)->nullable()->after('titulo');
            }

            if (Schema::hasColumn('metas', 'corte')) {
                $table->dropColumn('corte');
            }
            if (Schema::hasColumn('metas', 'ejercicio')) {
                $table->dropColumn('ejercicio');
            }
        });
    }
};
