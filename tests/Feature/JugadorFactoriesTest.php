<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Jugador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JugadorFactoriesTest extends TestCase
{
    // RefreshDatabase deja la base limpia en cada test.
    // Asi una prueba no afecta al resultado de la siguiente.
    use RefreshDatabase;

    public function test_factory_creates_a_player_with_a_related_club(): void
    {
        // Creamos un jugador con factory, pero fijando algunos campos
        // para que el test sea facil de leer y de comprobar.
        $jugador = Jugador::factory()->create([
            'nombre' => 'Nerea Campos',
            'posicion' => 'Defensa',
            'dorsal' => 4,
        ]);

        // Confirmamos que el registro se ha guardado en la tabla jugadores.
        $this->assertDatabaseHas('jugadores', [
            'id' => $jugador->id,
            'nombre' => 'Nerea Campos',
            'posicion' => 'Defensa',
            'dorsal' => 4,
        ]);

        // Como no indicamos club_id, la factory debe crear un club relacionado.
        $this->assertInstanceOf(Club::class, $jugador->club);
    }

    public function test_factory_can_create_controlled_players_for_the_same_club(): void
    {
        // Primero creamos un club conocido para asociarle varios jugadores.
        $club = Club::factory()->create([
            'nombre' => 'IES Maestre',
            'ciudad' => 'Ciudad Real',
            'categoria' => 'Juvenil',
        ]);

        // sequence permite crear varios jugadores con valores distintos
        // sin tener que repetir tres veces Jugador::factory()->create().
        Jugador::factory()
            ->count(3)
            ->sequence(
                ['nombre' => 'Jugador Uno', 'dorsal' => 1],
                ['nombre' => 'Jugador Dos', 'dorsal' => 2],
                ['nombre' => 'Jugador Tres', 'dorsal' => 3],
            )
            ->create([
                'club_id' => $club->id,
                'posicion' => 'Centrocampista',
            ]);

        // Comprobamos tanto la relacion como un registro concreto de la BD.
        $this->assertCount(3, $club->jugadores()->get());
        $this->assertDatabaseHas('jugadores', [
            'nombre' => 'Jugador Dos',
            'dorsal' => 2,
            'club_id' => $club->id,
        ]);
    }
}
