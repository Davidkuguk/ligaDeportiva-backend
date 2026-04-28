<?php

namespace Database\Factories;

use App\Models\Club;
use App\Models\Jugador;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Jugador>
 */
class JugadorFactory extends Factory
{
    /**
     * Esta factoria crea jugadores falsos con datos validos.
     * Sirve para preparar pruebas sin escribir manualmente todos los campos.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->name(),
            'posicion' => fake()->randomElement(['Portero', 'Defensa', 'Centrocampista', 'Delantero']),
            // El dorsal se limita entre 1 y 99 porque es la regla del controlador.
            'dorsal' => fake()->unique()->numberBetween(1, 99),
            // Si el test no indica un club, Laravel crea uno automaticamente.
            'club_id' => Club::factory(),
        ];
    }

    /**
     * Estado auxiliar para fijar un dorsal concreto en un test.
     * Es util cuando queremos probar reglas como "dorsal repetido".
     */
    public function dorsal(int $dorsal): static
    {
        return $this->state(fn (array $attributes) => [
            'dorsal' => $dorsal,
        ]);
    }
}
