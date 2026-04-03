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
        Schema::create('partidos', function (Blueprint $table) {
            // Clave primaria del partido.
            $table->id();
            // Cada partido pertenece a una liga.
            // Si se borra la liga, sus partidos tambien desaparecen.
            $table->foreignId('liga_id')->constrained('ligas')->cascadeOnDelete();
            // Club que juega en casa.
            // restrictOnDelete() evita borrar un club si todavia hay partidos ligados a el.
            $table->foreignId('club_local_id')->constrained('clubs')->restrictOnDelete();
            // Club que juega fuera.
            $table->foreignId('club_visitante_id')->constrained('clubs')->restrictOnDelete();
            // Fecha y hora del partido.
            $table->dateTime('fecha');
            // El resultado se deja nullable por si el partido aun no se ha jugado.
            $table->string('resultado')->nullable();
            // created_at y updated_at.
            $table->timestamps();

            // Indexamos la fecha porque es un campo muy comun para ordenar
            // o filtrar partidos en consultas futuras.
            $table->index('fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Borra la tabla en caso de rollback.
        Schema::dropIfExists('partidos');
    }
};
