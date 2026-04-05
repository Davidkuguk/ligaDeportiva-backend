<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Jugador;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JugadorModuleWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_jugadores_module_supports_a_complete_http_workflow(): void
    {
        // Necesitamos un administrador porque crear, editar y borrar
        // jugadores esta protegido por el middleware de admin.
        $admin = User::factory()->administrador()->create();

        // Preparamos dos clubes para comprobar relaciones
        // y para reutilizar dorsales en equipos distintos.
        $clubMaestre = Club::create([
            'nombre' => 'Club Maestre',
            'ciudad' => 'Ciudad Real',
            'categoria' => 'Juvenil',
        ]);

        $clubCalatrava = Club::create([
            'nombre' => 'Club Calatrava',
            'ciudad' => 'Almagro',
            'categoria' => 'Juvenil',
        ]);

        // Insertamos jugadores iniciales para verificar
        // que el listado sale ordenado y con el club cargado.
        $alvaro = Jugador::create([
            'nombre' => 'Alvaro Prieto',
            'posicion' => 'Defensa',
            'dorsal' => 5,
            'club_id' => $clubMaestre->id,
        ]);

        Jugador::create([
            'nombre' => 'Zoe Martin',
            'posicion' => 'Ala',
            'dorsal' => 11,
            'club_id' => $clubCalatrava->id,
        ]);

        // Probamos el listado general del recurso.
        $this->getJson('/api/jugadores')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.nombre', 'Alvaro Prieto')
            ->assertJsonPath('data.0.club.nombre', 'Club Maestre')
            ->assertJsonPath('data.1.nombre', 'Zoe Martin');

        // Probamos la consulta de un jugador concreto.
        $this->getJson('/api/jugadores/'.$alvaro->id)
            ->assertOk()
            ->assertJsonPath('data.club.nombre', 'Club Maestre')
            ->assertJsonPath('data.dorsal', 5);

        // Creamos un jugador nuevo a traves de la API.
        $response = $this->actingAs($admin)->postJson('/api/jugadores', [
            'nombre' => 'Lucia Navas',
            'posicion' => 'Centrocampista',
            'dorsal' => 8,
            'club_id' => $clubCalatrava->id,
        ]);

        $response->assertCreated()
            ->assertJsonPath('message', 'Jugador creado correctamente.')
            ->assertJsonPath('data.club.nombre', 'Club Calatrava');

        $jugadorCreadoId = $response->json('data.id');

        // Actualizamos el mismo jugador para comprobar
        // que el endpoint devuelve los cambios persistidos.
        $this->actingAs($admin)->putJson('/api/jugadores/'.$jugadorCreadoId, [
            'nombre' => 'Lucia Navas',
            'posicion' => 'Pivote',
            'dorsal' => 8,
            'club_id' => $clubCalatrava->id,
        ])
            ->assertOk()
            ->assertJsonPath('message', 'Jugador actualizado correctamente.')
            ->assertJsonPath('data.posicion', 'Pivote');

        // Por ultimo eliminamos el registro y comprobamos
        // que realmente desaparece de la base de datos.
        $this->actingAs($admin)->deleteJson('/api/jugadores/'.$jugadorCreadoId)
            ->assertOk()
            ->assertJsonPath('message', 'Jugador eliminado correctamente.');

        $this->assertDatabaseMissing('jugadores', [
            'id' => $jugadorCreadoId,
        ]);
    }

    public function test_same_dorsal_is_allowed_when_players_belong_to_different_clubs(): void
    {
        $admin = User::factory()->administrador()->create();

        // Creamos dos clubes distintos porque la regla del dorsal
        // solo debe ser unica dentro del mismo equipo.
        $clubMaestre = Club::create([
            'nombre' => 'Club Maestre',
            'ciudad' => 'Ciudad Real',
            'categoria' => 'Juvenil',
        ]);

        $clubCalatrava = Club::create([
            'nombre' => 'Club Calatrava',
            'ciudad' => 'Almagro',
            'categoria' => 'Juvenil',
        ]);

        Jugador::create([
            'nombre' => 'Jugador Local',
            'posicion' => 'Defensa',
            'dorsal' => 7,
            'club_id' => $clubMaestre->id,
        ]);

        // Si el dorsal repetido pertenece a otro club,
        // la API debe permitir guardarlo sin error.
        $this->actingAs($admin)->postJson('/api/jugadores', [
            'nombre' => 'Jugador Visitante',
            'posicion' => 'Ala',
            'dorsal' => 7,
            'club_id' => $clubCalatrava->id,
        ])
            ->assertCreated()
            ->assertJsonPath('data.club.nombre', 'Club Calatrava');
    }

    public function test_update_allows_keeping_the_same_dorsal_for_the_same_player(): void
    {
        $admin = User::factory()->administrador()->create();

        // Montamos un caso en el que el jugador conserva su dorsal
        // pero cambia otros datos del perfil.
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

        // Esta actualizacion no deberia fallar por la regla unique
        // porque seguimos editando al mismo jugador.
        $this->actingAs($admin)->putJson('/api/jugadores/'.$jugador->id, [
            'nombre' => 'David Moral',
            'posicion' => 'Cierre',
            'dorsal' => 6,
            'club_id' => $club->id,
        ])
            ->assertOk()
            ->assertJsonPath('data.posicion', 'Cierre')
            ->assertJsonPath('data.dorsal', 6);
    }

    public function test_validation_rejects_invalid_player_payloads(): void
    {
        $admin = User::factory()->administrador()->create();

        // Enviamos datos invalidos para comprobar que el controlador
        // devuelve los errores esperados en cada campo.
        $this->actingAs($admin)->postJson('/api/jugadores', [
            'nombre' => 'J1',
            'posicion' => '123',
            'dorsal' => 0,
            'club_id' => 9999,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nombre', 'posicion', 'dorsal', 'club_id']);
    }

    public function test_deleting_a_club_removes_its_players_from_the_database(): void
    {
        // Esta prueba valida el cascadeOnDelete de la migracion:
        // si desaparece el club, tambien debe desaparecer su jugador.
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

        $club->delete();

        $this->assertDatabaseMissing('jugadores', [
            'id' => $jugador->id,
        ]);
    }
}
