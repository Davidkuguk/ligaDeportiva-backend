<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Jugador;
use Illuminate\Database\Seeder;

class JugadorSeeder extends Seeder
{
    /**
     * Inserta jugadores de ejemplo para cada club.
     */
    public function run(): void
    {
        // Agrupamos los jugadores por nombre de club para que sea mas facil
        // ver quien pertenece a quien.
        $jugadoresPorClub = [
            'IES Maestre de Calatrava' => [
                ['nombre' => 'Alvaro Martin', 'posicion' => 'Portero', 'dorsal' => 1],
                ['nombre' => 'Pablo Ruiz', 'posicion' => 'Defensa', 'dorsal' => 4],
                ['nombre' => 'Diego Lopez', 'posicion' => 'Delantero', 'dorsal' => 9],
            ],
            'IES Atenea' => [
                ['nombre' => 'Mario Torres', 'posicion' => 'Portero', 'dorsal' => 1],
                ['nombre' => 'Sergio Molina', 'posicion' => 'Centrocampista', 'dorsal' => 8],
                ['nombre' => 'Adrian Romero', 'posicion' => 'Delantero', 'dorsal' => 11],
            ],
            'Colegio Salesianos Hermano Garate' => [
                ['nombre' => 'Javier Sanchez', 'posicion' => 'Portero', 'dorsal' => 13],
                ['nombre' => 'Hugo Navarro', 'posicion' => 'Defensa', 'dorsal' => 5],
                ['nombre' => 'Carlos Diaz', 'posicion' => 'Ala', 'dorsal' => 7],
            ],
        ];

        // Primero localizamos el club en la base de datos...
        foreach ($jugadoresPorClub as $nombreClub => $jugadores) {
            $club = Club::where('nombre', $nombreClub)->firstOrFail();

            // ...y despues insertamos sus jugadores.
            foreach ($jugadores as $jugador) {
                // La combinacion club_id + dorsal identifica al jugador
                // dentro del equipo para no repetir dorsales.
                Jugador::updateOrCreate(
                    [
                        'club_id' => $club->id,
                        'dorsal' => $jugador['dorsal'],
                    ],
                    [
                        'nombre' => $jugador['nombre'],
                        'posicion' => $jugador['posicion'],
                    ]
                );
            }
        }
    }
}
