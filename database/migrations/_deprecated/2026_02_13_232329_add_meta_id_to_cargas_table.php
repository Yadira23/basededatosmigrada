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
        Schema::table('cargas', function (Blueprint $table) {
            // ✅ como NO tienes id_ind, lo ponemos después de id_form
            $table->unsignedBigInteger('meta_id')->nullable()->after('id_form');

            $table->foreign('meta_id')
                ->references('id')
                ->on('metas')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cargas', function (Blueprint $table) {
            $table->dropForeign(['meta_id']);
            $table->dropColumn('meta_id');
        });
    }
};
