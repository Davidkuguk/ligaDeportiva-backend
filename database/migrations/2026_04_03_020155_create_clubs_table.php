<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de clubes.
     */
    public function up(): void
    {
        Schema::create('clubs', function (Blueprint $table) {
            // id() crea la clave primaria autoincremental.
            $table->id();
            // Guardamos el nombre del club.
            $table->string('nombre');
            // Ciudad a la que pertenece el club.
            $table->string('ciudad');
            // Categoria deportiva del club.
            $table->string('categoria');
            // timestamps() crea created_at y updated_at automaticamente.
            $table->timestamps();
        });
    }

    /**
     * Deshace la migracion eliminando la tabla.
     */
    public function down(): void
    {
        // Si revertimos la migracion, eliminamos la tabla.
        Schema::dropIfExists('clubs');
    }
};
