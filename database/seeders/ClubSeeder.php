<?php

namespace Database\Seeders;

use App\Models\Club;
use Illuminate\Database\Seeder;

class ClubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Preparamos un array con los 3 clubes de ejemplo.
        $clubs = [
            [
                'nombre' => 'IES Maestre de Calatrava',
                'ciudad' => 'Ciudad Real',
                'categoria' => 'Juvenil',
            ],
            [
                'nombre' => 'IES Atenea',
                'ciudad' => 'Ciudad Real',
                'categoria' => 'Juvenil',
            ],
            [
                'nombre' => 'Colegio Salesianos Hermano Garate',
                'ciudad' => 'Ciudad Real',
                'categoria' => 'Juvenil',
            ],
        ];

        // Recorremos el array y guardamos cada club.
        // updateOrCreate() evita duplicados:
        // si ya existe un club con ese nombre, lo actualiza;
        // si no existe, lo crea.
        foreach ($clubs as $club) {
            Club::updateOrCreate(
                ['nombre' => $club['nombre']],
                $club
            );
        }
    }
}
