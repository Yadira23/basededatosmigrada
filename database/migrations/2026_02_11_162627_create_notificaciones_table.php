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
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id('id_notificacion');

            // quién recibe (si tu carga trae id_usuario, lo usaremos)
            $table->unsignedBigInteger('id_usuario')->nullable();

            // para agrupar por dependencia (útil aunque no tengas id_depen en usuarios)
            $table->unsignedBigInteger('id_depen')->nullable();

            // opcional: vincular a una carga concreta si quieres
            $table->unsignedBigInteger('id_carga')->nullable();

            $table->string('tipo', 30)->default('RECORDATORIO');
            $table->string('titulo', 180);
            $table->text('mensaje');
            $table->boolean('leida')->default(false);

            $table->timestamps();

            $table->index(['id_usuario', 'leida']);
            $table->index(['id_depen', 'leida']);
            $table->index(['id_carga']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
