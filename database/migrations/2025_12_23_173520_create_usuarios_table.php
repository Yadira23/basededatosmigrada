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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->string('usuario_usr', 50)->unique();
            $table->string('nombre_usr', 100);
            $table->string('apellido_paterno', 100);
            $table->string('apellido_materno', 100)->nullable();
            $table->string('email_usr', 150)->unique();
            $table->string('password');

            $table->foreignId('id_depen')->constrained('dependencias');
            $table->foreignId('id_rol')->constrained('rol');

            $table->string('estado_usr', 20);
            $table->string('telefono_usr', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
