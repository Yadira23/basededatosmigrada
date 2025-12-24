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
        Schema::create('dependencias', function (Blueprint $table) {
            $table->id('id_depen');
            $table->string('nombre_depen', 150);
            $table->foreignId('id_sector')->constrained('sectores');
            $table->string('email_depen', 150);
            $table->string('extension_depen', 10)->nullable();
            $table->string('telefono_depen', 20);
            $table->string('calle_depen', 150);
            $table->string('numerocalle_depen', 20);
            $table->string('colonia_depen', 100);
            $table->string('cp_depen', 10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dependencias');
    }
};
