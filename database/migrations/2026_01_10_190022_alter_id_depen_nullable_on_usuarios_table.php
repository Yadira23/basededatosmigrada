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
        Schema::table('usuarios', function (Blueprint $table) {

            // 1️⃣ Quitar foreign key
            $table->dropForeign(['id_depen']);

            // 2️⃣ Quitar unique
            $table->dropUnique('usuarios_id_depen_unique');

            // 3️⃣ Hacer nullable la columna
            $table->unsignedBigInteger('id_depen')->nullable()->change();

            // 4️⃣ Volver a crear unique (1 a 1 se mantiene)
            $table->unique('id_depen');

            // 5️⃣ Volver a crear foreign key
            $table->foreign('id_depen')
                ->references('id_depen')
                ->on('dependencias')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {

            $table->dropForeign(['id_depen']);
            $table->dropUnique('usuarios_id_depen_unique');

            $table->unsignedBigInteger('id_depen')->nullable(false)->change();

            $table->unique('id_depen');

            $table->foreign('id_depen')
                ->references('id_depen')
                ->on('dependencias')
                ->onDelete('restrict');
        });
    }
};
