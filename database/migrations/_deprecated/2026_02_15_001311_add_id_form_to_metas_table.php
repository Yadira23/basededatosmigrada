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
            $table->unsignedBigInteger('id_form')->nullable()->after('id_ind');
            // si quieres FK (solo si formularios tiene PK id_form):
            // $table->foreign('id_form')->references('id_form')->on('formularios')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('metas', function (Blueprint $table) {
            // $table->dropForeign(['id_form']);
            $table->dropColumn('id_form');
        });
    }
};
