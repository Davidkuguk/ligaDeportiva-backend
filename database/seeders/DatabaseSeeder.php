<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Desde aqui lanzamos todos los seeders principales.
     */
    public function run(): void
    {
        // Este seeder principal solo coordina el orden.
        // Primero creamos clubes, luego jugadores, despues la liga
        // y por ultimo los partidos, porque estos dependen de todo lo anterior.
        $this->call([
            ClubSeeder::class,
            JugadorSeeder::class,
            LigaSeeder::class,
            PartidoSeeder::class,
        ]);
    }
}
