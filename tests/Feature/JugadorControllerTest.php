<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Jugador;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JugadorControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_players_sorted_by_name_with_their_club_loaded(): void
    {
        // Creo un club sencillo para asociarle los jugadores del test.
        $club = Club::create([
            'nombre' => 'Club Maestre',
            'ciudad' => 'Ciudad Real',
            'categoria' => 'Juvenil',
        ]);

        // Los inserto desordenados para comprobar que el endpoint
        // los devuelve ordenados por nombre.
        Jugador::create([
            'nombre' => 'Zoe Martin',
            'posicion' => 'Ala',
            'dorsal' => 11,
            'club_id' => $club->id,
        ]);

        Jugador::create([
            'nombre' => 'Alvaro Prieto',
            'posicion' => 'Defensa',
            'dorsal' => 5,
            'club_id' => $club->id,
        ]);

        // Compruebo tanto el orden como que venga cargado el club.
        $this->getJson('/api/jugadores')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.nombre', 'Alvaro Prieto')
            ->assertJsonPath('data.0.club.nombre', 'Club Maestre')
            ->assertJsonPath('data.1.nombre', 'Zoe Martin');
    }

    public function test_show_returns_the_requested_player_with_its_club(): void
    {
        // Preparo un jugador real para pedir luego su detalle.
        $club = Club::create([
            'nombre' => 'Club Calatrava',
            'ciudad' => 'Almagro',
            'categoria' => 'Juvenil',
        ]);

        $jugador = Jugador::create([
            'nombre' => 'Lucia Navas',
            'posicion' => 'Centrocampista',
            'dorsal' => 8,
            'club_id' => $club->id,
        ]);

        // El detalle tiene que traer tanto el jugador como su club.
        $this->getJson('/api/jugadores/'.$jugador->id)
            ->assertOk()
            ->assertJsonPath('data.nombre', 'Lucia Navas')
            ->assertJsonPath('data.club.nombre', 'Club Calatrava');
    }

    public function test_store_creates_a_player_and_returns_the_created_resource(): void
    {
        // Necesito un admin porque este endpoint esta protegido.
        $admin = User::factory()->administrador()->create();

        $club = Club::create([
            'nombre' => 'Club Maestre',
            'ciudad' => 'Ciudad Real',
            'categoria' => 'Juvenil',
        ]);

        // Pruebo la creacion normal de un jugador.
        $this->actingAs($admin)->postJson('/api/jugadores', [
            'nombre' => 'Mario Torres',
            'posicion' => 'Portero',
            'dorsal' => 1,
            'club_id' => $club->id,
        ])
            ->assertCreated()
            ->assertJsonPath('message', 'Jugador creado correctamente.')
            ->assertJsonPath('data.nombre', 'Mario Torres')
            ->assertJsonPath('data.club.nombre', 'Club Maestre');

        // Y confirmo que de verdad se ha guardado en la base de datos.
        $this->assertDatabaseHas('jugadores', [
            'nombre' => 'Mario Torres',
            'posicion' => 'Portero',
            'dorsal' => 1,
            'club_id' => $club->id,
        ]);
    }

    public function test_store_rejects_a_repeated_dorsal_in_the_same_club(): void
    {
        $admin = User::factory()->administrador()->create();

        // Monto un caso donde ya existe ese dorsal en el mismo club.
        $club = Club::create([
            'nombre' => 'Club Maestre',
            'ciudad' => 'Ciudad Real',
            'categoria' => 'Juvenil',
        ]);

        Jugador::create([
            'nombre' => 'Jugador Uno',
            'posicion' => 'Defensa',
            'dorsal' => 7,
            'club_id' => $club->id,
        ]);

        // La validacion debe cortar la peticion con error 422.
        $this->actingAs($admin)->postJson('/api/jugadores', [
            'nombre' => 'Jugador Dos',
            'posicion' => 'Ala',
            'dorsal' => 7,
            'club_id' => $club->id,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['dorsal']);
    }

    public function test_store_returns_custom_validation_messages_for_invalid_name_and_position(): void
    {
        $admin = User::factory()->administrador()->create();

        $club = Club::create([
            'nombre' => 'Club Maestre',
            'ciudad' => 'Ciudad Real',
            'categoria' => 'Juvenil',
        ]);

        // Meto datos mal puestos para comprobar los mensajes personalizados.
        $this->actingAs($admin)->postJson('/api/jugadores', [
            'nombre' => 'J1',
            'posicion' => '123',
            'dorsal' => 9,
            'club_id' => $club->id,
        ])
            ->assertUnprocessable()
            ->assertJsonPath('errors.nombre.0', 'El nombre del jugador solo puede contener letras, espacios y signos simples.')
            ->assertJsonPath('errors.posicion.0', 'La posicion solo puede contener letras, espacios y signos simples.');
    }

    public function test_update_allows_the_same_player_to_keep_their_dorsal(): void
    {
        $admin = User::factory()->administrador()->create();

        // Este caso me sirve para comprobar que al editarse a si mismo
        // no salta la restriccion del dorsal repetido.
        $club = Club::create([
            'nombre' => 'Club Maestre',
            'ciudad' => 'Ciudad Real',
            'categoria' => 'Juvenil',
        ]);

        $jugador = Jugador::create([
            'nombre' => 'David Moral',
            'posicion' => 'Defensa',
            'dorsal' => 6,
            'club_id' => $club->id,
        ]);

        // Cambio solo la posicion y dejo el dorsal igual.
        $this->actingAs($admin)->putJson('/api/jugadores/'.$jugador->id, [
            'nombre' => 'David Moral',
            'posicion' => 'Cierre',
            'dorsal' => 6,
            'club_id' => $club->id,
        ])
            ->assertOk()
            ->assertJsonPath('message', 'Jugador actualizado correctamente.')
            ->assertJsonPath('data.posicion', 'Cierre')
            ->assertJsonPath('data.dorsal', 6);
    }

    public function test_destroy_removes_the_player_from_the_database(): void
    {
        $admin = User::factory()->administrador()->create();

        // Primero creo un jugador para borrarlo despues.
        $club = Club::create([
            'nombre' => 'Club Maestre',
            'ciudad' => 'Ciudad Real',
            'categoria' => 'Juvenil',
        ]);

        $jugador = Jugador::create([
            'nombre' => 'Sergio Lara',
            'posicion' => 'Ala',
            'dorsal' => 12,
            'club_id' => $club->id,
        ]);

        // El endpoint tiene que responder bien y eliminar el registro.
        $this->actingAs($admin)->deleteJson('/api/jugadores/'.$jugador->id)
            ->assertOk()
            ->assertJsonPath('message', 'Jugador eliminado correctamente.');

        $this->assertDatabaseMissing('jugadores', [
            'id' => $jugador->id,
        ]);
    }
}
