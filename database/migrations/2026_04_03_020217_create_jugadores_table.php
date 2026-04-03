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
        Schema::create('jugadores', function (Blueprint $table) {
            // Identificador unico del jugador.
            $table->id();
            // Nombre del jugador.
            $table->string('nombre');
            // Posicion que suele ocupar en el campo.
            $table->string('posicion');
            // Numero de dorsal.
            $table->unsignedSmallInteger('dorsal');
            // club_id es la clave foranea que conecta cada jugador
            // con el club al que pertenece.
            // cascadeOnDelete() hace que, si se borra el club,
            // tambien se borren sus jugadores.
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            // Campos de control de Laravel.
            $table->timestamps();

            // Un mismo club no puede tener dos jugadores con el mismo dorsal.
            // Por eso creamos una restriccion unica combinando club_id y dorsal.
            $table->unique(['club_id', 'dorsal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Elimina la tabla si revertimos esta migracion.
        Schema::dropIfExists('jugadores');
    }
};
