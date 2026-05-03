<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Desde aqui lanzamos todos los seeders principales.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin Liga',
                'email' => 'admin@liga.local',
                'password' => Hash::make('admin'),
                'rol' => 'administrador',
            ]
        );

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
