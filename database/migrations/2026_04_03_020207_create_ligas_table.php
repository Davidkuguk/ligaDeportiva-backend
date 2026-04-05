<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de ligas.
     */
    public function up(): void
    {
        Schema::create('ligas', function (Blueprint $table) {
            // Clave primaria de la liga.
            $table->id();
            // Nombre visible de la competicion.
            $table->string('nombre');
            // Tipo de deporte que se juega en esta liga.
            $table->string('deporte');
            // Temporada academica o deportiva.
            $table->string('temporada');
            // Fechas de creacion y actualizacion.
            $table->timestamps();
        });
    }

    /**
     * Deshace la migracion eliminando la tabla.
     */
    public function down(): void
    {
        // Borra la tabla si hacemos rollback.
        Schema::dropIfExists('ligas');
    }
};
