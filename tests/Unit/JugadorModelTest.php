<?php

namespace Tests\Unit;

use App\Models\Club;
use App\Models\Jugador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JugadorModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_jugador_uses_the_expected_table_name(): void
    {
        // Este modelo necesita el nombre de tabla manual
        // porque Laravel no pluraliza bien "Jugador".
        $jugador = new Jugador;

        $this->assertSame('jugadores', $jugador->getTable());
    }

    public function test_jugador_declares_expected_mass_assignable_attributes(): void
    {
        // Creamos una instancia vacia para comprobar
        // que el modelo permite asignar solo los campos esperados.
        $jugador = new Jugador;

        $this->assertSame([
            'nombre',
            'posicion',
            'dorsal',
            'club_id',
        ], $jugador->getFillable());
    }

    public function test_jugador_can_be_created_with_valid_business_data(): void
    {
        // Creo un club primero porque el jugador depende de el.
        $club = Club::create([
            'nombre' => 'Club Maestre',
            'ciudad' => 'Ciudad Real',
            'categoria' => 'Juvenil',
        ]);

        // Si los datos son correctos, el modelo debe guardarse sin problemas.
        $jugador = Jugador::create([
            'nombre' => 'Mario Torres',
            'posicion' => 'Portero',
            'dorsal' => 1,
            'club_id' => $club->id,
        ]);

        // Aqui confirmo que el registro ha llegado realmente a la tabla.
        $this->assertDatabaseHas('jugadores', [
            'id' => $jugador->id,
            'nombre' => 'Mario Torres',
            'posicion' => 'Portero',
            'dorsal' => 1,
            'club_id' => $club->id,
        ]);
    }

    public function test_jugador_belongs_to_a_club(): void
    {
        // Primero creamos un club de prueba.
        $club = Club::create([
            'nombre' => 'Club Maestre',
            'ciudad' => 'Ciudad Real',
            'categoria' => 'Juvenil',
        ]);

        // Despues asociamos un jugador a ese club.
        $jugador = Jugador::create([
            'nombre' => 'Mario Torres',
            'posicion' => 'Portero',
            'dorsal' => 1,
            'club_id' => $club->id,
        ]);

        // Aqui comprobamos que la relacion jugador -> club
        // devuelve exactamente el club que hemos creado.
        $this->assertTrue($jugador->club->is($club));
        $this->assertSame('Club Maestre', $jugador->club->nombre);
    }

    public function test_club_can_retrieve_its_related_players(): void
    {
        // Tambien compruebo la relacion inversa desde el club.
        $club = Club::create([
            'nombre' => 'Club Maestre',
            'ciudad' => 'Ciudad Real',
            'categoria' => 'Juvenil',
        ]);

        $jugador = Jugador::create([
            'nombre' => 'Diego Moral',
            'posicion' => 'Cierre',
            'dorsal' => 6,
            'club_id' => $club->id,
        ]);

        $this->assertCount(1, $club->jugadores);
        $this->assertTrue($club->jugadores->first()->is($jugador));
    }
}
