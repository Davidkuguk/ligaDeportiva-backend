<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Liga;
use App\Models\Partido;
use Illuminate\Database\Seeder;

class PartidoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Recuperamos la liga creada antes.
        $liga = Liga::where('nombre', 'Liga Deportiva IES Maestre de Calatrava')
            ->where('temporada', '2025/2026')
            ->firstOrFail();

        // Recuperamos tambien los clubes porque vamos a necesitar sus IDs.
        $maestre = Club::where('nombre', 'IES Maestre de Calatrava')->firstOrFail();
        $atenea = Club::where('nombre', 'IES Atenea')->firstOrFail();
        $salesianos = Club::where('nombre', 'Colegio Salesianos Hermano Garate')->firstOrFail();

        // Definimos varios partidos de ejemplo.
        // Hay resultados ya cerrados y uno pendiente para simular casos reales.
        $partidos = [
            [
                'liga_id' => $liga->id,
                'club_local_id' => $maestre->id,
                'club_visitante_id' => $atenea->id,
                'fecha' => '2026-04-10 17:00:00',
                'resultado' => '3-1',
            ],
            [
                'liga_id' => $liga->id,
                'club_local_id' => $salesianos->id,
                'club_visitante_id' => $maestre->id,
                'fecha' => '2026-04-17 17:00:00',
                'resultado' => '2-2',
            ],
            [
                'liga_id' => $liga->id,
                'club_local_id' => $atenea->id,
                'club_visitante_id' => $salesianos->id,
                'fecha' => '2026-04-24 17:00:00',
                'resultado' => null,
            ],
        ];

        // Guardamos los partidos evitando duplicados si el seeder se repite.
        foreach ($partidos as $partido) {
            Partido::updateOrCreate(
                [
                    'liga_id' => $partido['liga_id'],
                    'club_local_id' => $partido['club_local_id'],
                    'club_visitante_id' => $partido['club_visitante_id'],
                    'fecha' => $partido['fecha'],
                ],
                [
                    'resultado' => $partido['resultado'],
                ]
            );
        }
    }
}
