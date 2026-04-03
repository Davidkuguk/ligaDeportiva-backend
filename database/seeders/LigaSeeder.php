<?php

namespace Database\Seeders;

use App\Models\Liga;
use Illuminate\Database\Seeder;

class LigaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Creamos una liga de ejemplo para la temporada actual del proyecto.
        // Igual que antes, updateOrCreate evita duplicar la misma liga
        // si el seeder se ejecuta varias veces.
        Liga::updateOrCreate(
            [
                'nombre' => 'Liga Deportiva IES Maestre de Calatrava',
                'temporada' => '2025/2026',
            ],
            [
                'deporte' => 'Futbol sala',
            ]
        );
    }
}
