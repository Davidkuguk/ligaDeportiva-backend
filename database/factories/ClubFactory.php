<?php

namespace Database\Factories;

use App\Models\Club;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Club>
 */
class ClubFactory extends Factory
{
    /**
     * Esta factoria crea clubes falsos para las pruebas.
     * Asi no dependemos de registros reales de la base de datos.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // unique() evita que Faker repita nombres y nos cause conflictos.
            'nombre' => fake()->unique()->company(),
            // La ciudad y la categoria se generan automaticamente.
            'ciudad' => fake()->city(),
            'categoria' => fake()->randomElement(['Cadete', 'Juvenil', 'Senior']),
        ];
    }
}
